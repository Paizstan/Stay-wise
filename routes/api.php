<?php

use App\Http\Controllers\HabitacionController;
use App\Http\Controllers\ReporteController;


use App\Http\Controllers\ReservaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::apiResource('habitaciones',HabitacionController::class);
Route::apiResource('reservas',ReservaController::class);
Route::get('/reporte/reservas-mes', [ReporteController::class, 'reservasPorMes']);
Route::post('api/habitaciones/{habitacion}', [HabitacionController::class, 'update']);






