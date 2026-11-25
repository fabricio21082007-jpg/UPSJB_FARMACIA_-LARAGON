<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::withCount('medicamentos')
            ->orderBy('nombre')->get();

        return view('settings.categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('settings.categorias.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre'      => 'required|max:80|unique:categorias',
            'descripcion' => 'nullable|max:255',
        ], $this->mensajes());

        Categoria::create($datos);

        return to_route('categorias.index')
            ->with('ok', 'Categoría registrada correctamente.');
    }

    public function edit(Categoria $categoria)
    {
        return view('settings.categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $datos = $request->validate([
            'nombre'      => ['required', 'max:80',
                              Rule::unique('categorias')->ignore($categoria)],
            'descripcion' => 'nullable|max:255',
        ], $this->mensajes());

        $categoria->update($datos);

        return to_route('categorias.index')
            ->with('ok', 'Categoría actualizada correctamente.');
    }

    public function destroy(Categoria $categoria)
    {
        // Regla del diagrama: Categoria 1 -- 0..* Medicamento (restrictOnDelete)
        if ($categoria->medicamentos()->exists()) {
            return to_route('categorias.index')->with('error',
                'No se puede eliminar: tiene medicamentos registrados.');
        }

        $categoria->delete();

        return to_route('categorias.index')
            ->with('ok', 'Categoría eliminada correctamente.');
    }

    private function mensajes(): array
    {
        return [
            'nombre.required'  => 'Escribe el nombre de la categoría.',
            'nombre.max'       => 'El nombre admite máximo 80 caracteres.',
            'nombre.unique'    => 'Esa categoría ya está registrada.',
            'descripcion.max'  => 'La descripción admite máximo 255 caracteres.',
        ];
    }
}
