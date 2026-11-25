{{-- Formulario compartido por create y edit.
     old('campo', $cliente->campo ?? '') muestra: lo que el usuario escribió si la validación falló,
     o el valor guardado al editar, o vacío al crear. --}}
<div class="row">
    <div class="col-12 mb-3">
        <label for="nombre" class="form-label">Nombre completo</label>
        <input type="text" name="nombre" id="nombre" maxlength="150"
            class="form-control @error('nombre') is-invalid @enderror"
            value="{{ old('nombre', $cliente->nombre ?? '') }}">
        {{-- @error muestra el mensaje de validación de ese campo --}}
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4 mb-3">
        <label for="dni" class="form-label">DNI</label>
        {{-- maxlength ayuda en el navegador; la regla digits:8 es la que de verdad valida --}}
        <input type="text" name="dni" id="dni" maxlength="8"
            class="form-control @error('dni') is-invalid @enderror"
            value="{{ old('dni', $cliente->dni ?? '') }}">
        @error('dni')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4 mb-3">
        <label for="telefono" class="form-label">Teléfono</label>
        <input type="text" name="telefono" id="telefono" maxlength="9"
            class="form-control @error('telefono') is-invalid @enderror"
            value="{{ old('telefono', $cliente->telefono ?? '') }}">
        @error('telefono')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4 mb-3">
        <label for="edad" class="form-label">Edad</label>
        <input type="number" name="edad" id="edad" min="0" max="120"
            class="form-control @error('edad') is-invalid @enderror"
            value="{{ old('edad', $cliente->edad ?? '') }}">
        @error('edad')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<button type="submit" class="btn btn-primary text-white">Guardar</button>
<a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
