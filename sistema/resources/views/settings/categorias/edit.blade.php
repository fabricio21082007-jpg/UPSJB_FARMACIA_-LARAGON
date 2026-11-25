{{-- Hereda la plantilla padre: menú, encabezado y estilos --}}
@extends('template.administrador')

{{-- Rellena los huecos (@yield) de la plantilla --}}
@section('title', 'Categorías')
@section('name_section', 'Categorías')
@section('description', 'Editar registro de categoría')
@section('item_section', 'Editar')
@section('route_section', route('categorias.index'))
@section('section', 'Categorías')
@section('module', 'Configuraciones')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
    <div class="card-body">
        <form action="{{ route('categorias.update', $categoria) }}" method="POST">
            @csrf {{-- token de seguridad --}}
            @method('PUT') {{-- HTML solo envía GET/POST: así Laravel sabe que es update() --}}
            @include('settings.categorias.form') {{-- mismo formulario, ahora con $categoria lleno --}}
        </form>
    </div>
</div>
@endsection
