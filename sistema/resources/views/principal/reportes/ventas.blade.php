{{-- Hereda la plantilla padre: menú, encabezado y estilos --}}
@extends('template.administrador')

{{-- Rellena los huecos (@yield) de la plantilla --}}
@section('title', 'Reporte de ventas')
@section('name_section', 'Reporte de ventas')
@section('description', 'Ventas por rango de fechas y filtros')
@section('item_section', 'Inicio')
@section('route_section', route('reportes.ventas'))
@section('section', 'Reportes')
@section('module', 'Principal')

@section('content')
  {{-- ── 1. Filtros: formulario GET, así el reporte se puede volver a abrir con la misma URL --}}
  <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary p-3 mb-3">
    <form method="GET" action="{{ route('reportes.ventas') }}" class="row g-2 align-items-end">
      <div class="col-md-2">
        <label for="desde" class="form-label small">Desde</label>
        <input type="date" name="desde" id="desde" value="{{ $desde }}" class="form-control @error('desde') is-invalid @enderror">
      </div>
      <div class="col-md-2">
        <label for="hasta" class="form-label small">Hasta</label>
        <input type="date" name="hasta" id="hasta" value="{{ $hasta }}" class="form-control @error('hasta') is-invalid @enderror">
      </div>
      <div class="col-md-2">
        <label for="cliente_id" class="form-label small">Cliente</label>
        <select name="cliente_id" id="cliente_id" class="form-select">
          <option value="">Todos</option>
          @foreach ($clientes as $cliente)
            <option value="{{ $cliente->id }}" @selected(request('cliente_id') == $cliente->id)>{{ $cliente->nombre }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label for="user_id" class="form-label small">Vendedor</label>
        <select name="user_id" id="user_id" class="form-select">
          <option value="">Todos</option>
          @foreach ($usuarios as $usuario)
            <option value="{{ $usuario->id }}" @selected(request('user_id') == $usuario->id)>{{ $usuario->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label for="categoria_id" class="form-label small">Categoría</label>
        <select name="categoria_id" id="categoria_id" class="form-select">
          <option value="">Todas</option>
          @foreach ($categorias as $categoria)
            <option value="{{ $categoria->id }}" @selected(request('categoria_id') == $categoria->id)>{{ $categoria->nombre }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 d-flex gap-2">
        <button class="btn btn-primary text-white flex-grow-1"><i class="bi bi-funnel"></i> Filtrar</button>
        <a href="{{ route('reportes.ventas') }}" class="btn btn-light" title="Quitar filtros"><i class="bi bi-x-lg"></i></a>
      </div>
<div class="col-12 d-flex gap-2 justify-content-end">
        <button type="submit" formaction="{{ route('reportes.ventas.pdf') }}" class="btn btn-outline-primary">
          <i class="bi bi-file-earmark-pdf"></i> PDF
        </button>
        <button type="submit" formaction="{{ route('reportes.ventas.excel') }}" class="btn btn-outline-success">
          <i class="bi bi-file-earmark-excel"></i> Excel
        </button>
      </div>

      @error('desde') <div class="col-12 text-danger small">{{ $message }}</div> @enderror
      @error('hasta') <div class="col-12 text-danger small">{{ $message }}</div> @enderror
    </form>
  </div>

  {{-- ── 2. Resumen --}}
  <div class="row g-3 mb-3">
    <div class="col-6 col-lg-3"><div class="card bg-white border-0 shadow-sm p-3"><small class="text-secondary">N.° de ventas</small><div class="fs-5 fw-bold">{{ $resumen['cantidad'] }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="card bg-white border-0 shadow-sm p-3"><small class="text-secondary">Total vendido</small><div class="fs-5 fw-bold">S/ {{ number_format($resumen['total'], 2) }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="card bg-white border-0 shadow-sm p-3"><small class="text-secondary">IGV</small><div class="fs-5 fw-bold">S/ {{ number_format($resumen['igv'], 2) }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="card bg-white border-0 shadow-sm p-3"><small class="text-secondary">Venta promedio</small><div class="fs-5 fw-bold">S/ {{ number_format($resumen['promedio'], 2) }}</div></div></div>
  </div>

  {{-- ── 3. Gráfico del rango --}}
  <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary p-3 mb-3">
    <h2 class="h6 fw-bold">Total vendido por día (S/)</h2>
    {{-- Altura fija: el gráfico se ve igual en pantallas angostas y anchas --}}
    <div style="height:260px"><canvas id="chartReport"></canvas></div>
  </div>

  {{-- ── 4. Detalle --}}
  <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary p-3">
    <div class="table-responsive">
      <table class="table table-sm table-hover w-100 bg-white mb-0">
        <thead><tr class="text-uppercase small"><th>N.°</th><th>Fecha</th><th>Cliente</th><th>Vendedor</th><th class="text-end">IGV</th><th class="text-end">Total</th></tr></thead>
        <tbody>
          @forelse ($ventas as $venta)
            <tr>
              <td><a href="{{ route('ventas.show', $venta) }}">{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</a></td>
              <td>{{ $venta->fecha->format('d/m/Y H:i') }}</td>
              <td>{{ $venta->cliente->nombre }}</td>
              <td>{{ $venta->usuario->name }}</td>
              <td class="text-end">S/ {{ number_format($venta->igv, 2) }}</td>
              <td class="text-end">S/ {{ number_format($venta->total, 2) }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-secondary">No hay ventas con esos filtros.</td></tr>
          @endforelse
        </tbody>
        @if ($ventas->isNotEmpty())
          <tfoot><tr><th colspan="4" class="text-end">Totales</th>
            <th class="text-end">S/ {{ number_format($resumen['igv'], 2) }}</th>
            <th class="text-end">S/ {{ number_format($resumen['total'], 2) }}</th></tr></tfoot>
        @endif
      </table>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('js/chart.umd.min.js') }}"></script>
  {{-- $grafico es una colección ['24/11' => 85.5, ...]: keys() son los días y values() los montos --}}
  <script>
    const labels = @json($grafico->keys());
    const values = @json($grafico->values());

    new Chart(document.getElementById('chartReport'), {
      type: 'bar',
      data: { labels, datasets: [{ label: 'Total (S/)', data: values, backgroundColor: '#ff0a41' }] },
      options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
  </script>
@endpush
