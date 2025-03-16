<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Ensure User model is imported

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas';

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleReserva::class, 'reserva_id');
    }
}
