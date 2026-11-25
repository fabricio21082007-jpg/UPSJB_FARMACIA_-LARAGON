<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MedicamentoController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Route::view('/', 'inicio')->name('inicio');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

    // Gestión
    Route::resource('medicamentos', MedicamentoController::class)->except('show');
    Route::resource('clientes', ClienteController::class)->except('show');
    // Una venta registrada no se edita: se consulta (show) o se anula (destroy)
    Route::resource('ventas', VentaController::class)->except(['edit', 'update']);

    // Configuraciones
    // parameters(): el parámetro de la ruta se llama {rol}, igual que el modelo Rol
    Route::resource('roles', RoleController::class)->except('show')->parameters(['roles' => 'rol']);
    Route::resource('usuarios', UserController::class)->except('show');
    Route::resource('categorias', CategoriaController::class)->except('show');
});
