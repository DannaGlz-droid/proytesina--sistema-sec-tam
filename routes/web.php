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

// Usuarios (UserController)

Route::get('usuario/gestion-de-usuarios', [UserController::class, 'index'])->name('user.user-gestion');
Route::get('usuario/gestion-de-usuarios/registro', [UserController::class, 'create'])->name('user.create');
Route::post('/usuario/gestion-de-usuarios/store', [UserController::class, 'store'])->name('user.store');
Route::get('/usuario/gestion-de-usuarios/actualizar-registro/{user}', [UserController::class, 'edit'])->name('user.edit');
Route::put('/usuario/gestion-de-usuarios/actualizar-registro/{user}', [UserController::class, 'update'])->name('user.update');
Route::get('usuario/gestion-de-usuarios/actualizar-contrasena/{user?}', [UserController::class, 'password'])->name('user.update-password');
Route::put('usuario/gestion-de-usuarios/actualizar-contrasena/{user}', [UserController::class, 'updatePassword'])->name('user.update-password.update');
Route::get('/usuario/gestion-de-usuarios/mostrar/{user}', [UserController::class, 'show'])->name('user.show');
Route::delete('/usuario/gestion-de-usuarios/eliminar/{user}', [UserController::class, 'destroy'])->name('user.destroy');
//---
//Route::get('/usuarios/gestion', [App\Http\Controllers\UserController::class, 'gestion'])->name('user.gestion');
Route::get('usuario/gestion-de-usuarios/actualizar-registro/{id}', [App\Http\Controllers\UserController::class, 'edit'])->name('usuario.actualizar-registro');

// Estadísticas (StatisticsController)

Route::get('estadisticas/datos', [App\Http\Controllers\DeathController::class, 'datos'])->name('statistic.data');
Route::get('estadisticas/datos/export', [App\Http\Controllers\DeathExportController::class, 'export'])->name('statistic.export');
Route::post('estadisticas/import', [App\Http\Controllers\DeathImportController::class, 'import'])->name('statistic.import');
Route::get('estadisticas/registro', [App\Http\Controllers\DeathController::class, 'create'])->name('statistic.create');
Route::post('estadisticas/store', [App\Http\Controllers\DeathController::class, 'store'])->name('statistic.store');
Route::get('estadisticas/actualizar-registro/{death}', [App\Http\Controllers\DeathController::class, 'edit'])->name('statistic.edit');
Route::put('estadisticas/actualizar-registro/{death}', [App\Http\Controllers\DeathController::class, 'update'])->name('statistic.update');
Route::delete('estadisticas/datos/{death}', [App\Http\Controllers\DeathController::class, 'destroy'])->name('statistic.destroy');
// API endpoints for autocomplete
Route::get('api/municipalities/search', [App\Http\Controllers\MunicipalityController::class, 'search'])->name('api.municipalities.search');
Route::post('api/municipalities', [App\Http\Controllers\MunicipalityController::class, 'store'])->name('api.municipalities.store');
// Lookup endpoints for remote autocompletes
Route::get('api/causes/search', [App\Http\Controllers\LookupController::class, 'searchCauses'])->name('api.causes.search');
Route::get('api/locations/search', [App\Http\Controllers\LookupController::class, 'searchLocations'])->name('api.locations.search');
//  VISTAS  ---------------------------------------------------------

// Usuarios (views that are not controlled by UserController)
Route::view('usuario/miperfil', 'usuarios.miperfil')->name('usuario.miperfil');
// Controller-backed route above provides the view with the required $user variable.

// Note: the gestion and registro URIs are handled by UserController routes above
// (user.index and user.create). We removed the Route::view duplicates to ensure
// the controller can pass data (positions/roles/etc.) to the views.

// Estadísticas (4)
//Route::view('estadisticas/datos', 'estadisticas.datos')->name('estadisticas.datos');
//Route::view('estadisticas/registro', 'estadisticas.acciones.registro')->name('estadisticas.nuevo-registro');
// The actualizar-registro view is now controller-backed (requires an id). Remove the standalone view route to avoid confusion.
// Route::view('estadisticas/actualizar-registro', 'estadisticas.acciones.actualizar-registro')->name('estadisticas.actualizar-registro');
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
