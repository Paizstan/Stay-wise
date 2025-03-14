<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});*/

Route::get("/", function(){
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

Route::get('/catalogos', function () {
    return Inertia::render('catalogos/Catalogos');
})->middleware(['auth', 'verified'])->name('catalogos');

Route::get('/gestion', function () {
    return Inertia::render('catalogos/Gestion');
})->middleware(['auth', 'verified'])->name('gestion');


require __DIR__.'/auth.php';
