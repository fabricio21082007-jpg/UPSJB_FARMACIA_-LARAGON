<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DashboardControlller;
use Illuminate\Support\Facades\Route;

//Route:: 
Route::view('/', 'inicio')->name('inicio');
Route::resource('categorias', CategoriaController::class)->except('show');
Route::resource('Roles', CategoriaController::class)->except('show');
