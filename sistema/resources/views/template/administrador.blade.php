<!doctype html>
<html lang="es" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="San Juan – Sistema de Venta de Medicamentos">
    <title>@yield('title', 'Dashboard') | FARM SJB</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @stack('estilos')
</head>
<body>
<div id="app-wrapper" class="d-flex">
    @include('partials.sidebar')

    <div class="main-wrapper d-flex flex-column flex-grow-1 overflow-hidden">
        @include('partials.header')

        <main class="app-content flex-grow-1 overflow-y-auto p-3 p-lg-4">
            <div class="container-fluid p-0">
                <div class="d-flex align-items-center justify-content-between
                            flex-column flex-sm-row gap-2 mb-4">
                    {{-- Descripción de la página --}}
                    <p class="text-secondary mb-0">@yield('description')</p>

                    {{-- Ruta de navegación: item › sección › módulo --}}
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item">@yield('item_section', 'Inicio')</li>
                        <li class="breadcrumb-item">
                            <a href="@yield('route_section', route('dashboard.index'))">@yield('section', 'Dashboard')</a>
                        </li>
                        <li class="breadcrumb-item active">@yield('module', 'Principal')</li>
                    </ol>
                </div>

                @yield('content')
            </div>
        </main>
    </div>
</div>

@include('partials.modal-logout')

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
