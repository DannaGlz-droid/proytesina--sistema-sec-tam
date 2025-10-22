<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

// CONTROLADORES  ---------------------------------------------------

Route::get('/hola', [UserController::class, 'index'])->name('user.index');
Route::get('/create', [UserController::class, 'create'])->name('user.create');
Route::get('/usuarios/gestion', [App\Http\Controllers\UserController::class, 'gestion'])->name('user.gestion');

//  VISTAS  ---------------------------------------------------------

// Usuarios (5)
Route::view('usuario/miperfil', 'usuarios.miperfil')->name('usuario.miperfil');
Route::view('usuario/gestion-de-usuarios', 'usuarios.gestion-de-usuarios')->name('usuario.gestion-de-usuarios');
Route::view('usuario/gestion-de-usuarios/registro', 'usuarios.acciones.registro')->name('usuario.registro');
Route::view('usuario/gestion-de-usuarios/actualizar-registro', 'usuarios.acciones.actualizar-registro')->name('usuario.actualizar-registro');
Route::view('usuario/gestion-de-usuarios/actualizar-contrasena', 'usuarios.acciones.actualizar-contrasena')->name('usuario.actualizar-contrasena');

// Estadísticas (4)
Route::view('estadisticas/datos', 'estadisticas.datos')->name('estadisticas.datos');
Route::view('estadisticas/registro', 'estadisticas.acciones.registro')->name('estadisticas.nuevo-registro');
Route::view('estadisticas/actualizar-registro', 'estadisticas.acciones.actualizar-registro')->name('estadisticas.actualizar-registro');
Route::view('estadisticas/graficas', 'estadisticas.graficas')->name('estadisticas.graficas');

// Reportes (4)
Route::view('reportes/centro-de-control', 'reportes.centro-de-control')->name('reportes.centro-de-control');
Route::view('reportes/registro/seguridad-vial', 'reportes.registro.seguridad-vial')->name('reportes.seguridad-vial');
Route::view('reportes/registro/observatorio-de-lesiones', 'reportes.registro.observatorio-de-lesiones')->name('reportes.observatorio-de-lesiones');
Route::view('reportes/registro/alcoholimetria', 'reportes.registro.alcoholimetria')->name('reportes.alcoholimetria');
//---
Route::view('reportes/publicaciones', 'reportes.publicaciones')->name('reportes.publicaciones');

// total: 13

require __DIR__.'/auth.php';
