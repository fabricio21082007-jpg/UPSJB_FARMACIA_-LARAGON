{{-- Formulario compartido por create y edit.
     old('campo', $medicamento->campo ?? '') muestra: lo que el usuario escribió si la validación falló,
     o el valor guardado al editar, o vacío al crear. --}}
<div class="row">
    <div class="col-12 col-md-6 mb-3">
        <label for="nombre" class="form-label">Nombre del medicamento</label>
        <input type="text" name="nombre" id="nombre" maxlength="120"
            class="form-control @error('nombre') is-invalid @enderror"
            value="{{ old('nombre', $medicamento->nombre ?? '') }}">
        {{-- @error muestra el mensaje de validación de ese campo --}}
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label for="categoria_id" class="form-label">Categoría</label>
        {{-- Se guarda el id de la categoría (clave foránea), pero se muestra su nombre --}}
        <select name="categoria_id" id="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror">
            <option value="">-- Seleccione --</option>
            {{-- $categorias llega desde create() y edit() del controlador --}}
            @foreach ($categorias as $categoria)
                {{-- @selected marca la opción guardada (al editar) o la elegida antes del error --}}
                <option value="{{ $categoria->id }}" @selected(old('categoria_id', $medicamento->categoria_id ?? '') == $categoria->id)>
                    {{ $categoria->nombre }}
                </option>
            @endforeach
        </select>
        @error('categoria_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label for="precio" class="form-label">Precio (S/)</label>
        <input type="number" name="precio" id="precio" step="0.01" min="0"
            class="form-control @error('precio') is-invalid @enderror"
            value="{{ old('precio', $medicamento->precio ?? '') }}">
        @error('precio')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label for="stock" class="form-label">Stock</label>
        <input type="number" name="stock" id="stock" step="1" min="0"
            class="form-control @error('stock') is-invalid @enderror"
            value="{{ old('stock', $medicamento->stock ?? 0) }}">
        @error('stock')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 mb-3">
        <label for="imagen" class="form-label">
            Imagen <small class="text-muted">(opcional, jpg/png/webp, máx. 2 MB)</small>
        </label>
        {{-- type="file" solo funciona si el <form> tiene enctype="multipart/form-data" --}}
        <input type="file" name="imagen" id="imagen" accept="image/*"
            class="form-control @error('imagen') is-invalid @enderror">
        @error('imagen')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        {{-- Al editar, muestra la imagen actual. asset('storage/...') funciona gracias a storage:link --}}
        @if (!empty($medicamento?->imagen))
            <div class="mt-2">
                <small class="text-muted d-block mb-1">Imagen actual:</small>
                <img src="{{ asset('storage/' . $medicamento->imagen) }}" alt="{{ $medicamento->nombre }}"
                    class="img-thumbnail" style="max-height: 120px">
            </div>
        @endif
    </div>
</div>

<button type="submit" class="btn btn-primary text-white">Guardar</button>
<a href="{{ route('medicamentos.index') }}" class="btn btn-secondary">Cancelar</a>
