<?php

use App\Http\Controllers\CategoriaController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'inicio')->name('inicio');
Route::resource('categorias', CategoriaController::class)->except('show');
