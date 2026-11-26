<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MedicamentoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

// ── Solo para visitantes (sin sesión): el login
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Route::view('/', 'inicio')->name('inicio');

// ── Solo para usuarios con sesión iniciada
Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Todos los roles
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::resource('clientes', ClienteController::class)->except('show');
    // Una venta registrada no se edita: se consulta (show) o se anula (destroy)
    Route::resource('ventas', VentaController::class)->except(['edit', 'update'])
        ->middlewareFor('destroy', 'role:Administrador');   // solo el administrador anula ventas

    // Administrador y Químico farmacéutico
        Route::middleware('role:Administrador,Químico farmacéutico')->group(function () {
        Route::resource('medicamentos', MedicamentoController::class)->except('show');
        Route::get('/reportes/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
        Route::get('/reportes/ventas/pdf', [ReporteController::class, 'ventasPdf'])->name('reportes.ventas.pdf');
        Route::get('/reportes/ventas/excel', [ReporteController::class, 'ventasExcel'])->name('reportes.ventas.excel');
    });
    // Solo el administrador: Configuraciones
    Route::middleware('role:Administrador')->group(function () {
        // parameters(): el parámetro de la ruta se llama {rol}, igual que el modelo Rol
        Route::resource('roles', RoleController::class)->except('show')->parameters(['roles' => 'rol']);
        Route::resource('usuarios', UserController::class)->except('show');
        Route::resource('categorias', CategoriaController::class)->except('show');
    });
});
