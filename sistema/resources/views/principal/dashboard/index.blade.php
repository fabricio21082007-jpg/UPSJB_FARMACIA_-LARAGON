{{-- Hereda la plantilla padre: menú, encabezado y estilos --}}
@extends('template.administrador')

{{-- Rellena los huecos (@yield) de la plantilla --}}
@section('title', 'Dashboard')
@section('name_section', 'Dashboard')
@section('description', 'Resumen del sistema de venta de medicamentos')
@section('item_section', 'Inicio')
@section('route_section', route('dashboard.index'))
@section('section', 'Dashboard')
@section('module', 'Principal')

@section('content')
<div class="row g-3">
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm"><div class="card-body">
            <div class="text-secondary small">Gestión</div>
            <div class="fw-bold">Medicamentos, clientes y ventas</div>
        </div></div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm"><div class="card-body">
            <div class="text-secondary small">Configuraciones</div>
            <div class="fw-bold">Roles, usuarios y categorías</div>
        </div></div>
    </div>
</div>
@endsection
