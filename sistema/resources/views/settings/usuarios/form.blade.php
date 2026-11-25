{{-- Formulario compartido por create y edit.
     old('campo', $usuario->campo ?? '') muestra: lo que el usuario escribió si la validación falló,
     o el valor guardado al editar, o vacío al crear. --}}
<div class="row">
    <div class="col-12 col-md-6 mb-3">
        <label for="name" class="form-label">Nombre completo</label>
        <input type="text" name="name" id="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $usuario->name ?? '') }}">
        {{-- @error muestra el mensaje de validación de ese campo --}}
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label for="rol_id" class="form-label">Rol</label>
        {{-- Clave foránea: se guarda rol_id, se muestra el nombre del rol --}}
        <select name="rol_id" id="rol_id" class="form-select @error('rol_id') is-invalid @enderror">
            <option value="">-- Seleccione --</option>
            @foreach ($roles as $rol)
                <option value="{{ $rol->id }}" @selected(old('rol_id', $usuario->rol_id ?? '') == $rol->id)>
                    {{ $rol->nombre }}
                </option>
            @endforeach
        </select>
        @error('rol_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4 mb-3">
        <label for="dni" class="form-label">DNI</label>
        <input type="text" name="dni" id="dni" maxlength="8"
            class="form-control @error('dni') is-invalid @enderror"
            value="{{ old('dni', $usuario->dni ?? '') }}">
        @error('dni')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4 mb-3">
        <label for="telefono" class="form-label">Teléfono</label>
        <input type="text" name="telefono" id="telefono" maxlength="15"
            class="form-control @error('telefono') is-invalid @enderror"
            value="{{ old('telefono', $usuario->telefono ?? '') }}">
        @error('telefono')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4 mb-3">
        <label for="colegiatura" class="form-label">Colegiatura</label>
        <input type="text" name="colegiatura" id="colegiatura" maxlength="20"
            class="form-control @error('colegiatura') is-invalid @enderror"
            value="{{ old('colegiatura', $usuario->colegiatura ?? '') }}">
        @error('colegiatura')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" name="email" id="email"
            class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $usuario->email ?? '') }}">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label for="password" class="form-label">
            Contraseña
            {{-- @isset: este texto solo aparece al editar, cuando $usuario existe --}}
            @isset($usuario)
                <small class="text-muted">(déjela vacía para no cambiarla)</small>
            @endisset
        </label>
        {{-- Nunca se rellena con value: la contraseña guardada está encriptada --}}
        <input type="password" name="password" id="password"
            class="form-control @error('password') is-invalid @enderror">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        {{-- La regla 'confirmed' compara password con password_confirmation --}}
        <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
    </div>
</div>

<button type="submit" class="btn btn-primary text-white">Guardar</button>
<a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
