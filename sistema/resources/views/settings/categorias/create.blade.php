{{-- Hereda la plantilla padre: menú, encabezado y estilos --}}
@extends('template.administrador')

{{-- Rellena los huecos (@yield) de la plantilla --}}
@section('title', 'Categorías')
@section('name_section', 'Categorías')
@section('description', 'Nuevo registro de categoría')
@section('item_section', 'Nuevo')
@section('route_section', route('categorias.index'))
@section('section', 'Categorías')
@section('module', 'Configuraciones')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
    <div class="card-body">
        <form action="{{ route('categorias.store') }}" method="POST"> {{-- POST a store() --}}
            @csrf {{-- token de seguridad; sin él aparece el error 419 --}}
            @include('settings.categorias.form') {{-- formulario compartido con edit --}}
        </form>
    </div>
</div>
@endsection
