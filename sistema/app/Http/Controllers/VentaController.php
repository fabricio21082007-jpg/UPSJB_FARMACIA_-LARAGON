<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Medicamento;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaController extends Controller
{
    // Listado de ventas (con cliente y vendedor cargados)
    public function index()
    {
        $ventas = Venta::with(['cliente', 'usuario'])->latest('fecha')->get();

        return view('management.ventas.index', compact('ventas'));
    }

    // Formulario para registrar una venta
    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $medicamentos = Medicamento::where('stock', '>', 0)->orderBy('nombre')->get();

        return view('management.ventas.create', compact('clientes', 'medicamentos'));
    }

    // Guardar la venta con sus detalles
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'items' => 'required|array|min:1',
            'items.*.medicamento_id' => 'required|distinct|exists:medicamentos,id',
            'items.*.cantidad' => 'required|integer|min:1',
        ], [
            'items.required' => 'Debe agregar al menos un medicamento.',
            'items.*.medicamento_id.distinct' => 'No repita el mismo medicamento, aumente la cantidad.',
        ]);

        // Transacción: si algo falla, no se guarda nada
        $venta = DB::transaction(function () use ($request) {
            // 1. Crear la venta (el total se calcula después)
            $venta = Venta::create([
                'cliente_id' => $request->cliente_id,
                'user_id' => auth()->id(),
                'fecha' => now(),
                'total' => 0,
                'igv' => 0,
            ]);

            // 2. Registrar cada medicamento vendido
            foreach ($request->items as $item) {
                // lockForUpdate(): bloquea la fila hasta terminar, para que dos ventas a la vez no vendan el mismo stock
                $medicamento = Medicamento::lockForUpdate()->find($item['medicamento_id']);

                if ($medicamento->stock < $item['cantidad']) {
                    // Lanzar la excepción deshace todo lo guardado en la transacción (rollback)
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para {$medicamento->nombre} (disponible: {$medicamento->stock}).",
                    ]);
                }

                // El precio se toma de la base de datos, no del formulario
                $venta->detalles()->create([
                    'medicamento_id' => $medicamento->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $medicamento->precio,
                    'subtotal' => round($medicamento->precio * $item['cantidad'], 2),
                ]);

                // 3. Descontar el stock (método del diagrama: actualizarStock)
                $medicamento->actualizarStock($item['cantidad']);
            }

            // 4. Guardar total e IGV (el precio ya incluye el 18% de IGV)
            // calcularTotal() suma los subtotales y separa el IGV: total × 18 / 118
            $venta->calcularTotal();
            $venta->save();

            return $venta;
        });

        return to_route('ventas.show', $venta)->with('ok', 'Venta registrada');
    }

    // Ver el detalle de una venta
    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'usuario', 'detalles.medicamento']);

        return view('management.ventas.show', compact('venta'));
    }

    // Anular venta: devuelve el stock y la elimina
    public function destroy(Venta $venta)
    {
        DB::transaction(function () use ($venta) {
            foreach ($venta->detalles as $detalle) {
                Medicamento::where('id', $detalle->medicamento_id)->increment('stock', $detalle->cantidad);
            }

            // Los detalles se borran solos (cascadeOnDelete en la migración)
            $venta->delete();
        });

        return to_route('ventas.index')->with('ok', 'Venta anulada y stock devuelto');
    }
}
