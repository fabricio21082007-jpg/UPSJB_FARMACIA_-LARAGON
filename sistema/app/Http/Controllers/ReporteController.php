<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function ventas(Request $request)
    {
        // 1. Valida los filtros. Todos son opcionales; si no llegan fechas, se usa el mes actual
        $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date|after_or_equal:desde',
            'cliente_id' => 'nullable|exists:clientes,id',
            'user_id' => 'nullable|exists:users,id',
            'categoria_id' => 'nullable|exists:categorias,id',
        ]);
        // ?: también cubre el caso en que el campo de fecha se envía vacío
        $desde = $request->input('desde') ?: now()->startOfMonth()->toDateString();
        $hasta = $request->input('hasta') ?: now()->toDateString();

        // 2. Consulta con filtros dinámicos: cada when() se aplica solo si el filtro tiene valor
        $ventas = Venta::with(['cliente', 'usuario'])
            ->whereBetween('fecha', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])   // rango de fechas
            ->when($request->cliente_id, fn ($q, $id) => $q->where('cliente_id', $id))
            ->when($request->user_id, fn ($q, $id) => $q->where('user_id', $id))
            // whereHas: ventas que tienen AL MENOS un detalle de un medicamento de esa categoría
            ->when($request->categoria_id, fn ($q, $id) =>
                $q->whereHas('detalles.medicamento', fn ($m) => $m->where('categoria_id', $id)))
            ->orderBy('fecha')
            ->get();

        // 3. Totales del reporte (se calculan sobre la colección ya filtrada)
        $resumen = [
            'cantidad' => $ventas->count(),
            'total' => $ventas->sum('total'),
            'igv' => $ventas->sum('igv'),
            'promedio' => $ventas->avg('total') ?? 0,
        ];

        // 4. Datos para el gráfico: total por día dentro del rango
        $grafico = $ventas->groupBy(fn ($venta) => $venta->fecha->format('d/m'))
            ->map(fn ($grupo) => round($grupo->sum('total'), 2));

        // 5. Listas para los <select> de los filtros
        $clientes = Cliente::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();
        $categorias = Categoria::orderBy('nombre')->get();

        return view('principal.reportes.ventas', compact(
            'ventas', 'resumen', 'grafico', 'desde', 'hasta', 'clientes', 'usuarios', 'categorias'
        ));
    }
}
