<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ReservaController;
<<<<<<< HEAD
use App\Http\Controllers\HabitacionController;
=======
use App\Http\Controllers\PDFController;
>>>>>>> ae1b1f003e48e413f4c29b0632e637f4625bbe23


/*Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});*/

Route::get("/", function () {
    return Inertia::render('Catalogo');
})->name('catalogo');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/habitaciones', function () {
    return Inertia::render('catalogos/Habitaciones');
})->middleware(['auth', 'verified'])->name('habitaciones');

Route::get('/reservas', function () {
    return Inertia::render('reservas/GestionReservas');
})->middleware(['auth', 'verified'])->name('reservas');

Route::get('/gestion', function () {
    return Inertia::render('catalogos/Gestion');
})->middleware(['auth', 'verified'])->name('gestion');

/* Route::middleware(['auth'])->group(function () {
    Route::get('/reporte/vista', [ReporteController::class, 'vistaReporte'])->name('reporte.vista');  // Vista previa en Vue
    Route::get('/reporte/pdf', [ReporteController::class, 'generarPDF'])->name('reporte.pdf'); // Descargar PDF
    Route::get('/reporte/datos', [ReservaController::class, 'getDatos']); // Ensure this route points to ReservaController
<<<<<<< HEAD
    
});

Route::post('api/habitaciones/{habitacion}', [HabitacionController::class, 'update']);
require __DIR__.'/auth.php';
=======
}); */

Route::get('/reportes/reservas/rango', function () {
    return Inertia::render('reportes/ReporteReservas');
})->middleware(['auth', 'verified'])->name('reserva.rango');

Route::get('/reportes/reservas', [PDFController::class, 'getReservas'])
    ->middleware(['auth', 'verified'])->name('reportes.reservas');

require __DIR__ . '/auth.php';
>>>>>>> ae1b1f003e48e413f4c29b0632e637f4625bbe23
