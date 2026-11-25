{{-- Hereda la plantilla padre: menú, encabezado y estilos --}}
@extends('template.administrador')

{{-- Rellena los huecos (@yield) de la plantilla --}}
@section('title', 'Ventas')
@section('name_section', 'Ventas')
@section('description', 'Detalle de la venta')
@section('item_section', 'Detalle')
@section('route_section', route('ventas.index'))
@section('section', 'Ventas')
@section('module', 'Gestión')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
    <div class="card-body">
        @include('partials.alerts')

        <div class="row mb-3">
            <div class="col-12 col-md-4 mb-2">
                <small class="text-muted d-block">Venta Nº</small>
                {{-- str_pad: 7 se muestra como 000007 --}}
                <span class="fw-semibold">{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="col-12 col-md-4 mb-2">
                <small class="text-muted d-block">Fecha</small>
                <span class="fw-semibold">{{ $venta->fecha->format('d/m/Y H:i') }}</span>
            </div>
            <div class="col-12 col-md-4 mb-2">
                <small class="text-muted d-block">Vendedor</small>
                <span class="fw-semibold">{{ $venta->usuario->name }}</span>
            </div>
            <div class="col-12 col-md-4 mb-2">
                <small class="text-muted d-block">Cliente</small>
                <span class="fw-semibold">{{ $venta->cliente->nombre }}</span>
            </div>
            <div class="col-12 col-md-4 mb-2">
                <small class="text-muted d-block">DNI</small>
                <span class="fw-semibold">{{ $venta->cliente->dni }}</span>
            </div>
        </div>

        <table class="table table-sm align-middle">
            <thead>
                <tr>
                    <th class="small text-uppercase text-center">Nº</th>
                    <th class="small text-uppercase">Medicamento</th>
                    <th class="small text-uppercase text-center">Cantidad</th>
                    <th class="small text-uppercase text-end">Precio unit.</th>
                    <th class="small text-uppercase text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($venta->detalles as $detalle)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $detalle->medicamento->nombre }}</td>
                    <td class="text-center">{{ $detalle->cantidad }}</td>
                    <td class="text-end">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td class="text-end">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    {{-- El precio ya incluye IGV: op. gravada = total - igv --}}
                    <td colspan="4" class="text-end">Op. gravada</td>
                    <td class="text-end">S/ {{ number_format($venta->total - $venta->igv, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end">IGV (18%)</td>
                    <td class="text-end">S/ {{ number_format($venta->igv, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end fw-semibold">Total</td>
                    <td class="text-end fw-bold">S/ {{ number_format($venta->total, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <a href="{{ route('ventas.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>
@endsection
