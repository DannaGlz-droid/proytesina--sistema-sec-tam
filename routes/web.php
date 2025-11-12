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

// ========== RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN) ==========
Route::middleware('auth')->group(function () {
    
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
    Route::get('usuario/gestion-de-usuarios/actualizar-registro/{id}', [App\Http\Controllers\UserController::class, 'edit'])->name('usuario.actualizar-registro');
    Route::view('usuario/miperfil', 'usuarios.miperfil')->name('usuario.miperfil');

    // Estadísticas (StatisticsController)
    Route::get('estadisticas/datos', [App\Http\Controllers\DeathController::class, 'datos'])->name('statistic.data');
    Route::get('estadisticas/datos/export', [App\Http\Controllers\DeathExportController::class, 'export'])->name('statistic.export');
    Route::post('estadisticas/import', [App\Http\Controllers\DeathImportController::class, 'import'])->name('statistic.import');
    Route::get('estadisticas/registro', [App\Http\Controllers\DeathController::class, 'create'])->name('statistic.create');
    Route::post('estadisticas/store', [App\Http\Controllers\DeathController::class, 'store'])->name('statistic.store');
    Route::get('estadisticas/actualizar-registro/{death}', [App\Http\Controllers\DeathController::class, 'edit'])->name('statistic.edit');
    Route::put('estadisticas/actualizar-registro/{death}', [App\Http\Controllers\DeathController::class, 'update'])->name('statistic.update');
    Route::delete('estadisticas/datos/{death}', [App\Http\Controllers\DeathController::class, 'destroy'])->name('statistic.destroy');
    Route::get('estadisticas/graficas', [App\Http\Controllers\StatisticsController::class, 'index'])->name('estadisticas.graficas');
    Route::get('estadisticas/charts-data', [App\Http\Controllers\StatisticsController::class, 'chartsData'])->name('estadisticas.charts-data');
    
    // API endpoints for autocomplete
    Route::get('api/municipalities/search', [App\Http\Controllers\MunicipalityController::class, 'search'])->name('api.municipalities.search');
    Route::post('api/municipalities', [App\Http\Controllers\MunicipalityController::class, 'store'])->name('api.municipalities.store');
    Route::get('api/causes/search', [App\Http\Controllers\LookupController::class, 'searchCauses'])->name('api.causes.search');
    Route::get('api/locations/search', [App\Http\Controllers\LookupController::class, 'searchLocations'])->name('api.locations.search');

    // Reportes (vistas)
    Route::view('reportes/centro-de-control', 'reportes.centro-de-control')->name('reportes.centro-de-control');
    Route::get('reportes/publicaciones', [App\Http\Controllers\ReportController::class, 'index'])->name('reportes.index');

    // Reportes (formularios de registro - vistas)
    Route::get('reportes/registro/seguridad-vial', [App\Http\Controllers\ReportController::class, 'createSeguridadVial'])->name('reportes.seguridad-vial');
    Route::get('reportes/registro/observatorio-de-lesiones', [App\Http\Controllers\ReportController::class, 'createObservatorio'])->name('reportes.observatorio-de-lesiones');
    Route::view('reportes/registro/alcoholimetria', 'reportes.registro.alcoholimetria')->name('reportes.alcoholimetria');

    // Reportes (controlador - guardar datos)
    Route::post('reportes/seguridad-vial/store', [App\Http\Controllers\ReportController::class, 'storeSeguridadVial'])->name('reportes.seguridad-vial.store');
    Route::post('reportes/observatorio/store', [App\Http\Controllers\ReportController::class, 'storeObservatorio'])->name('reportes.observatorio.store');
    Route::post('reportes/alcoholimetria/store', [App\Http\Controllers\ReportController::class, 'storeAlcoholimetria'])->name('reportes.alcoholimetria.store');

    // Reportes (editar)
    Route::get('reportes/seguridad-vial/{publication}/edit', [App\Http\Controllers\ReportController::class, 'editSeguridadVial'])->name('reportes.seguridad-vial.edit');
    Route::put('reportes/seguridad-vial/{publication}', [App\Http\Controllers\ReportController::class, 'updateSeguridadVial'])->name('reportes.seguridad-vial.update');
    Route::get('reportes/observatorio/{publication}/edit', [App\Http\Controllers\ReportController::class, 'editObservatorio'])->name('reportes.observatorio.edit');
    Route::put('reportes/observatorio/{publication}', [App\Http\Controllers\ReportController::class, 'updateObservatorio'])->name('reportes.observatorio.update');
    Route::get('reportes/alcoholimetria/{publication}/edit', [App\Http\Controllers\ReportController::class, 'editAlcoholimetria'])->name('reportes.alcoholimetria.edit');
    Route::put('reportes/alcoholimetria/{publication}', [App\Http\Controllers\ReportController::class, 'updateAlcoholimetria'])->name('reportes.alcoholimetria.update');

    // Eliminar una publicación/reporte
    Route::delete('reportes/{publication}', [App\Http\Controllers\ReportController::class, 'destroy'])->name('reportes.destroy');

    // Eliminar archivo individual de una publicación
    Route::delete('reportes/file/{file}', [App\Http\Controllers\ReportController::class, 'destroyFile'])->name('reportes.file.delete');
});

// total: 13

require __DIR__.'/auth.php';
