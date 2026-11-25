<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    // Listado de clientes
    public function index()
    {
        $clientes = Cliente::latest()->get();

        return view('management.clientes.index', compact('clientes'));
    }

    // Formulario para crear
    public function create()
    {
        return view('management.clientes.create');
    }

    // Guardar nuevo cliente
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:150',
            'dni' => 'required|digits:8|unique:clientes',
            'telefono' => 'required|digits:9',
            'edad' => 'required|integer|min:0|max:120',
        ]);

        Cliente::create($validated);

        return to_route('clientes.index')->with('ok', 'Cliente registrado');
    }

    // Formulario para editar
    public function edit(Cliente $cliente)
    {
        return view('management.clientes.edit', compact('cliente'));
    }

    // Actualizar cliente
    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:150',
            // ignore(): el DNI no se repite, sin contar el cliente que se está editando
            'dni' => ['required', 'digits:8', Rule::unique('clientes')->ignore($cliente->id)],
            'telefono' => 'required|digits:9',
            'edad' => 'required|integer|min:0|max:120',
        ]);

        $cliente->update($validated);

        return to_route('clientes.index')->with('ok', 'Cliente actualizado');
    }

    // Eliminar cliente (solo si no tiene ventas)
    public function destroy(Cliente $cliente)
    {
        if ($cliente->ventas()->exists()) {
            return to_route('clientes.index')->with('error', 'No se puede eliminar el cliente porque tiene ventas registradas');
        }

        $cliente->delete();

        return to_route('clientes.index')->with('ok', 'Cliente eliminado');
    }
}
