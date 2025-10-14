<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::view('/prueba', 'landing.prueba')->name('prueba');

//---------------------------------------------------------

// Estadísticas
Route::view('estadisticas/registro', 'estadisticas.registro')->name('estadisticas.nuevo-registro');
Route::view('estadisticas/actualizar-registro', 'estadisticas.actualizar-registro')->name('estadisticas.actualizar-registro');

// Usuarios
Route::view('usuario/gestion-de-usuarios/registro', 'usuario.gestion-de-usuarios.registro')->name('usuario.registro');
Route::view('usuario/gestion-de-usuarios/actualizar-registro', 'usuario.gestion-de-usuarios.actualizar-registro')->name('usuario.actualizar-registro');
Route::view('usuario/gestion-de-usuarios/actualizar-contrasena', 'usuario.gestion-de-usuarios.actualizar-contrasena')->name('usuario.actualizar-contrasena');

// Reportes

Route::view('reportes/hola', 'reportes.hola')->name('reportes.hola');

Route::view('reportes/registro/seguridad-vial', 'reportes.registro.seguridad-vial')->name('reportes.seguridad-vial');
Route::view('reportes/registro/observatorio-de-lesiones', 'reportes.registro.observatorio-de-lesiones')->name('reportes.observatorio-de-lesiones');
Route::view('reportes/registro/alcoholimetria', 'reportes.registro.alcoholimetria')->name('reportes.alcoholimetria');
require __DIR__.'/auth.php';
