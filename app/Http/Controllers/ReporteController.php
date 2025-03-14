<?php

namespace App\Http\Controllers;
use App\Models\Reserva;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DetallesReserva;
use Inertia\Inertia;


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
    
     // Método para mostrar la vista previa en Vue
     public function vistaReporte()
     {
         return inertia('Reports/Hotel'); // Renderiza el componente Vue
     }
 
     // Método para generar el PDF con datos de la BD
     public function obtenerDatos()
     {
         // Obtiene las reservas y detalles
         $reservas = Reserva::with(['usuario', 'detalles'])->get();
 
         return response()->json($reservas);
     }
 
     public function generarPDF()
     {
         $reservas = Reserva::with(['usuario', 'detalles'])->get();
 
         //$pdf = \PDF::loadView('pdf.reporte', compact('reservas'));
        // return $pdf->download('reporte_hotel.pdf');
     }
     
}
