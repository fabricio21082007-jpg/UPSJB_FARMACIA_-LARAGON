<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    // Listado de roles
    public function index()
    {
        $roles = Rol::latest()->get();

        return view('settings.roles.index', compact('roles'));
    }

    // Formulario para crear
    public function create()
    {
        return view('settings.roles.create');
    }

    // Guardar nuevo rol
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:50|unique:roles',
            'descripcion' => 'nullable|max:255',
        ]);

        Rol::create($validated);

        return to_route('roles.index')->with('ok', 'Rol registrado');
    }

    // Formulario para editar
    public function edit(Rol $rol)
    {
        return view('settings.roles.edit', compact('rol'));
    }

    // Actualizar rol
    public function update(Request $request, Rol $rol)
    {
        $validated = $request->validate([
            // ignore(): el nombre no se repite, sin contar el rol que se está editando
            'nombre' => ['required', 'max:50', Rule::unique('roles')->ignore($rol->id)],
            'descripcion' => 'nullable|max:255',
        ]);

        $rol->update($validated);

        return to_route('roles.index')->with('ok', 'Rol actualizado');
    }

    // Eliminar rol (solo si no tiene usuarios)
    public function destroy(Rol $rol)
    {
        if ($rol->usuarios()->exists()) {
            return to_route('roles.index')->with('error', 'No se puede eliminar el rol porque tiene usuarios asignados');
        }

        $rol->delete();

        return to_route('roles.index')->with('ok', 'Rol eliminado');
    }
}
