<?php

namespace App\Http\Controllers;
use App\Models\Reserva;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function reservasPorMes()
    {
        $datos = Reserva::selectRaw('MONTH(fecha_creacion) as mes, COUNT(*) as total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Generar un array con 12 meses inicializados en 0
        $reservas = array_fill(0, 12, 0);
        foreach ($datos as $dato) {
            $reservas[$dato->mes - 1] = $dato->total;
        }

        return response()->json($reservas);
    }
    
}
