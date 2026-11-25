{{-- Hereda la plantilla padre: menú, encabezado y estilos --}}
@extends('template.administrador')

{{-- Rellena los huecos (@yield) de la plantilla --}}
@section('title', 'Dashboard')
@section('name_section', 'Dashboard')
@section('description', 'Resumen del negocio para tomar decisiones')
@section('item_section', 'Inicio')
@section('route_section', route('dashboard.index'))
@section('section', 'Dashboard')
@section('module', 'Principal')

@section('content')
  {{-- ── 1. Tarjetas de indicadores --}}
  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
      <div class="card bg-white border-0 shadow-sm h-100 p-3 d-flex flex-row align-items-center gap-3">
        <div class="kpi-icon bg-primary-subtle text-primary"><i class="bi bi-cash-coin"></i></div>
        <div><small class="text-secondary">Ventas de hoy</small>
          <div class="fs-5 fw-bold">S/ {{ number_format($ventasHoy, 2) }}</div></div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card bg-white border-0 shadow-sm h-100 p-3 d-flex flex-row align-items-center gap-3">
        <div class="kpi-icon bg-dark-subtle text-dark"><i class="bi bi-calendar3"></i></div>
        <div><small class="text-secondary">Ventas del mes</small>
          <div class="fs-5 fw-bold">S/ {{ number_format($ventasMes, 2) }}</div></div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card bg-white border-0 shadow-sm h-100 p-3 d-flex flex-row align-items-center gap-3">
        <div class="kpi-icon bg-secondary-subtle text-secondary"><i class="bi bi-people"></i></div>
        <div><small class="text-secondary">Clientes</small>
          <div class="fs-5 fw-bold">{{ $clientes }}</div></div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card bg-white border-0 shadow-sm h-100 p-3 d-flex flex-row align-items-center gap-3">
        <div class="kpi-icon bg-warning-subtle text-warning"><i class="bi bi-exclamation-triangle"></i></div>
        <div><small class="text-secondary">Medicamentos con stock bajo</small>
          <div class="fs-5 fw-bold">{{ $stockBajo }}</div></div>
      </div>
    </div>
  </div>

  {{-- ── 2. Gráficos: cada <canvas> es un lienzo donde Chart.js dibuja --}}
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary h-100 p-3">
        <h2 class="h6 fw-bold">Ventas de los últimos 7 días (S/)</h2>
        {{-- Altura fija: en pantallas anchas el gráfico no se achata ni deja espacio vacío --}}
        <div style="height:300px"><canvas id="chartDays"></canvas></div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary h-100 p-3">
        <h2 class="h6 fw-bold">Ventas del mes por categoría</h2>
        {{-- Misma altura que el gráfico de líneas: la dona ya no crece con el ancho de la pantalla --}}
        <div style="height:300px"><canvas id="chartCategories"></canvas></div>
      </div>
    </div>
    <div class="col-12">
      <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary p-3">
        <h2 class="h6 fw-bold">Los 5 medicamentos más vendidos del mes (unidades)</h2>
        {{-- Un contenedor con altura fija evita que el gráfico se aplaste --}}
        <div style="height:260px"><canvas id="chartTop"></canvas></div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  {{-- Chart.js guardado en public/js: funciona sin internet --}}
  <script src="{{ asset('js/chart.umd.min.js') }}"></script>
  {{-- @json convierte los datos de PHP en datos de JavaScript --}}
  <script>
    const dias            = @json($dias);
    const totales         = @json($totales);
    const porCategoria    = @json($porCategoria);
    const topMedicamentos = @json($topMedicamentos);

    const PRIMARY = '#ff0a41', DARK = '#323232', GRAY = '#4b5563';  // colores de bootstrap.css del proyecto

    // Gráfico de líneas: ventas por día
    new Chart(document.getElementById('chartDays'), {
      type: 'line',
      data: { labels: dias,
              datasets: [{ label: 'Ventas (S/)', data: totales, borderColor: PRIMARY,
                           backgroundColor: 'rgba(255,10,65,.12)', fill: true, tension: .3 }] },
      options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    // Gráfico de dona: ventas por categoría
    new Chart(document.getElementById('chartCategories'), {
      type: 'doughnut',
      data: { labels: porCategoria.map(c => c.nombre),
              datasets: [{ data: porCategoria.map(c => c.total),
                           backgroundColor: [PRIMARY, DARK, GRAY, '#ff8aa5', '#9ca3af', '#ffd0db', '#7a0520', '#d1d5db'] }] },
      options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } } }
    });

    // Gráfico de barras horizontales: los 5 más vendidos
    new Chart(document.getElementById('chartTop'), {
      type: 'bar',
      data: { labels: topMedicamentos.map(m => m.nombre),
              datasets: [{ label: 'Unidades', data: topMedicamentos.map(m => m.unidades), backgroundColor: DARK }] },
      options: { indexAxis: 'y', maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });
  </script>
@endpush
