<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleReserva extends Model
{
    use HasFactory;

    protected $table = "detalle_reservas";
    protected $fillable = ['cantidad', 'precio', 'habitacion_id', 'reserva_id'];  // Adjusted fields

    // Relación con el modelo Habitacion
    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class);
    }

    // Relación con la Reserva
    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }
}
