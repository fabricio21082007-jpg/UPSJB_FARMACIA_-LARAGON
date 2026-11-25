@extends('template.administrador')

@section('titulo', 'Nueva categoría')
@section('subtitulo', 'Registrar una categoría de medicamentos')

@section('contenido')
<div class="card bg-white rounded-0 border-0 shadow-sm border-5 border-top border-secondary">
    <form action="{{ route('categorias.store') }}" method="POST" class="card-body">
        @csrf

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de la categoría</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                   @class(['form-control', 'is-invalid' => $errors->has('nombre')])>
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción (opcional)</label>
            <textarea name="descripcion" id="descripcion" rows="3"
                      @class(['form-control', 'is-invalid' => $errors->has('descripcion')])
            >{{ old('descripcion') }}</textarea>
            @error('descripcion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary text-white">Guardar</button>
        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
