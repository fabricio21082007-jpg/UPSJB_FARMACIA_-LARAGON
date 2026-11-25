<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Listado de usuarios (con su rol cargado)
    public function index()
    {
        $usuarios = User::with('rol')->latest()->get();

        return view('settings.usuarios.index', compact('usuarios'));
    }

    // Formulario para crear
    public function create()
    {
        $roles = Rol::orderBy('nombre')->get();

        return view('settings.usuarios.create', compact('roles'));
    }

    // Guardar nuevo usuario
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rol_id' => 'required|exists:roles,id',
            'name' => 'required|max:255',
            'dni' => 'required|digits:8|unique:users',
            'telefono' => 'nullable|max:15',
            'colegiatura' => 'nullable|max:20',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|confirmed', // confirmed: debe coincidir con password_confirmation
        ]);

        User::create($validated); // el cast 'hashed' del modelo encripta la contraseña al guardar

        return to_route('usuarios.index')->with('ok', 'Usuario registrado');
    }

    // Formulario para editar
    public function edit(User $usuario)
    {
        $roles = Rol::orderBy('nombre')->get();

        return view('settings.usuarios.edit', compact('usuario', 'roles'));
    }

    // Actualizar usuario
    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'rol_id' => 'required|exists:roles,id',
            'name' => 'required|max:255',
            'dni' => ['required', 'digits:8', Rule::unique('users')->ignore($usuario->id)],
            'telefono' => 'nullable|max:15',
            'colegiatura' => 'nullable|max:20',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Si no escribió contraseña nueva, se mantiene la actual
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $usuario->update($validated);

        return to_route('usuarios.index')->with('ok', 'Usuario actualizado');
    }

    // Eliminar usuario (solo si no tiene ventas)
    public function destroy(User $usuario)
    {
        if ($usuario->ventas()->exists()) {
            return to_route('usuarios.index')->with('error', 'No se puede eliminar el usuario porque tiene ventas registradas');
        }

        $usuario->delete();

        return to_route('usuarios.index')->with('ok', 'Usuario eliminado');
    }
}
