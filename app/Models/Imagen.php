<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imagen extends Model
{
    protected $table = 'imagenes';

    protected $fillable = ['nombre','habitacion_id'];

    public function habitacion(){
        return $this->belongsTo(Habitacion::class);
    }
}
