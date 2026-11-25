<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Medicamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicamentoController extends Controller
{
    // Unidades a partir de las cuales el stock se considera bajo
    const STOCK_BAJO = 10;

    // Listado de medicamentos con búsqueda y filtros
    public function index(Request $request)
    {
        $medicamentos = Medicamento::with('categoria')                   // carga la categoría en 1 sola consulta (evita N+1)
            ->when($request->search, function ($query, $search) {        // solo si escribió algo en el buscador
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->when($request->categoria_id, function ($query, $categoriaId) { // solo si eligió una categoría
                $query->where('categoria_id', $categoriaId);
            })
            ->when($request->boolean('stock_bajo'), function ($query) {   // solo si marcó "stock bajo"
                $query->where('stock', '<=', self::STOCK_BAJO);
            })
            ->orderBy('nombre')
            ->get();

        // Para llenar el <select> del filtro
        $categorias = Categoria::orderBy('nombre')->get();
        $stockBajo = self::STOCK_BAJO;

        return view('management.medicamentos.index', compact('medicamentos', 'categorias', 'stockBajo'));
    }

    // Formulario para crear
    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();

        return view('management.medicamentos.create', compact('categorias'));
    }

    // Guardar nuevo medicamento
    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|max:120',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // máx. 2 MB
        ]);

        // Si subió una imagen, se guarda en storage/app/public/medicamentos
        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('medicamentos', 'public');
        }

        Medicamento::create($validated);

        return to_route('medicamentos.index')->with('ok', 'Medicamento registrado');
    }

    // Formulario para editar
    public function edit(Medicamento $medicamento)
    {
        $categorias = Categoria::orderBy('nombre')->get();

        return view('management.medicamentos.edit', compact('medicamento', 'categorias'));
    }

    // Actualizar medicamento
    public function update(Request $request, Medicamento $medicamento)
    {
        $validated = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|max:120',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            // Borra la imagen anterior y guarda la nueva
            if ($medicamento->imagen) {
                Storage::disk('public')->delete($medicamento->imagen);
            }
            $validated['imagen'] = $request->file('imagen')->store('medicamentos', 'public');
        } else {
            // Si no subió imagen nueva, se mantiene la actual
            unset($validated['imagen']);
        }

        $medicamento->update($validated);

        return to_route('medicamentos.index')->with('ok', 'Medicamento actualizado');
    }

    // Eliminar medicamento (solo si no se ha vendido)
    public function destroy(Medicamento $medicamento)
    {
        if ($medicamento->detallesVenta()->exists()) {
            return to_route('medicamentos.index')->with('error', 'No se puede eliminar el medicamento porque tiene ventas registradas');
        }

        // Borra también su imagen
        if ($medicamento->imagen) {
            Storage::disk('public')->delete($medicamento->imagen);
        }

        $medicamento->delete();

        return to_route('medicamentos.index')->with('ok', 'Medicamento eliminado');
    }
}
