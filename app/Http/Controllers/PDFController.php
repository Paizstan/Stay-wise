<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\PDF;

class PDFController extends Controller
{
    public function getReservas(Request $request)
    {
        // Obtener parámetros
        $fecha1 = $request->fechainicio ?? now()->startOfYear()->toDateString(); // Cambiado a inicio de año
        $fecha2 = $request->fechaFinal ?? now()->endOfYear()->toDateString(); // Cambiado a fin de año
        $estado = trim($request->estado ?? 'Confirmado'); // Aseguramos que no haya espacios
    
        // Consulta de reservas
        $reservas = DB::select("
            SELECT 
                r.id, 
                r.fecha_creacion, 
                r.estado, 
                r.pagada,
                u.name AS cliente, 
                h.nombre AS habitacion_nombre, 
                h.tipo, 
                h.descripcion AS habitacion_descripcion,
                dr.fecha_entrada, 
                dr.fecha_salida, 
                dr.precio, 
                (dr.precio) as subtotal
            FROM reservas r 
            INNER JOIN users u ON r.user_id = u.id
            INNER JOIN detalle_reservas dr ON dr.reserva_id = r.id
            INNER JOIN habitaciones h ON dr.habitacion_id = h.id
            WHERE DATE(r.fecha_creacion) BETWEEN ? AND ?
            AND TRIM(r.estado) = ?
            ORDER BY r.id ASC
        ", [$fecha1, $fecha2, $estado]);

        // Asegurarse de que $reservas no sea null
        $data = collect($reservas)->groupBy('id')->map(function ($reserva) {
            $detalle = $reserva->map(function ($item) {
                return [
                    'habitacion' => $item->habitacion_nombre,
                    'tipo' => $item->tipo,
                    'descripcion' => $item->habitacion_descripcion,
                    'fecha_entrada' => $item->fecha_entrada,
                    'fecha_salida' => $item->fecha_salida,
                    'precio' => $item->precio,
                    'subtotal' => $item->subtotal
                ];
            });

            $first = $reserva->first();
            $total = $detalle->sum('subtotal');

            return [
                'id' => $first->id,
                'fecha_creacion' => $first->fecha_creacion,
                'estado' => $first->estado,
                'cliente' => $first->cliente,
                'pagada' => $first->pagada,
                'total' => $total,
                'detalle' => $detalle
            ];
        });

        // Asegurarse de que $data no sea null
        if ($data->isEmpty()) {
            $data = collect([]);
        }

        // Consulta para ocupación del hotel
        $ocupacion = DB::select("
            SELECT 
                MONTHNAME(dr.fecha_entrada) as mes, 
                COUNT(dr.id) as habitaciones_ocupadas, 
                (SELECT COUNT(*) FROM habitaciones) as total_habitaciones,
                ROUND((COUNT(dr.id) / (SELECT COUNT(*) FROM habitaciones)) * 100, 2) as porcentaje_ocupacion
            FROM detalle_reservas dr
            WHERE DATE(dr.fecha_entrada) BETWEEN ? AND ?
            GROUP BY MONTHNAME(dr.fecha_entrada), MONTH(dr.fecha_entrada)
            ORDER BY MONTH(dr.fecha_entrada)
        ", [$fecha1, $fecha2]);

        // Consulta para reporte de estado de reservas por mes
        $estadoReservas = DB::select("
            SELECT 
                SUM(CASE WHEN r.estado = 'Cancelada' THEN 1 ELSE 0 END) AS reservas_canceladas,
                SUM(CASE WHEN r.estado = 'Pendiente' THEN 1 ELSE 0 END) AS reservas_pendientes,
                SUM(CASE WHEN r.estado = 'Confirmado' THEN 1 ELSE 0 END) AS reservas_confirmadas
            FROM reservas r
            WHERE DATE(r.fecha_creacion) BETWEEN ? AND ?
        ", [$fecha1, $fecha2]);

        // Generar PDF
        $pdf = PDF::loadView('reportes.reservasPDF', compact('data', 'fecha1', 'fecha2', 'estado', 'ocupacion', 'estadoReservas'));
        return $pdf->stream('reporte_reservas.pdf');
    }
}