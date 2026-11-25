{{-- Hereda la plantilla padre: menú, encabezado y estilos --}}
@extends('template.administrador')

{{-- Rellena los huecos (@yield) de la plantilla --}}
@section('title', 'Categorías')
@section('name_section', 'Categorías')
@section('description', 'Listado de registros de categorías')
@section('item_section', 'Inicio')
@section('route_section', route('categorias.index'))
@section('section', 'Categorías')
@section('module', 'Configuraciones')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
    <div class="card-header bg-white">
        <div class="row">
            <div class="col-12 col-md-3">
                <a href="{{ route('categorias.create') }}" class="btn btn-primary rounded-3 text-uppercase text-white w-100">
                    <i class="bi bi-plus-lg me-2"></i><span class="small fw-medium">Nuevo registro</span>
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        {{-- Mensajes flash: ok (verde) y error (rojo) --}}
        @include('partials.alerts')

        <h6 class="text-uppercase small text-muted mb-3">Total de registros encontrados: {{ $categorias->count() }}</h6>
        <table id="display" class="table table-sm table-hover w-100 bg-white">
            <thead>
                <tr>
                    <th class="text-center align-middle text-uppercase small">Nº</th>
                    <th class="text-center align-middle text-uppercase small">Nombre</th>
                    <th class="text-center align-middle text-uppercase small">Descripción</th>
                    <th class="text-center align-middle text-uppercase small">Medicamentos</th>
                    <th class="text-center align-middle text-uppercase small">Acciones</th>
                </tr>
            </thead>
            <tbody>
                {{-- @forelse recorre la colección; si está vacía muestra el bloque @empty --}}
                @forelse ($categorias as $categoria)
                <tr>
                    <td class="text-center align-middle">{{ $loop->iteration }} {{-- 1, 2, 3... --}}</td>
                    <td class="text-start align-middle">{{ $categoria->nombre }}</td>
                    <td class="text-start align-middle">{{ $categoria->descripcion ?? '—' }}</td>
                    {{-- medicamentos_count viene de withCount('medicamentos') del controlador --}}
                    <td class="text-center align-middle">{{ $categoria->medicamentos_count }}</td>
                    <td class="text-center align-middle">
                        <div class="dropdown">
                            <button class="btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('categorias.edit', $categoria) }}">Editar</a></li>
                                <li>
                                    <form action="{{ route('categorias.destroy', $categoria) }}" method="POST"
                                        onsubmit="return confirm('¿Seguro que desea eliminar esta categoría?')">
                                        @csrf
                                        @method('DELETE') {{-- verbo real: destroy() --}}
                                        <button type="submit" class="dropdown-item text-danger">Eliminar</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">No hay categorías registradas</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
