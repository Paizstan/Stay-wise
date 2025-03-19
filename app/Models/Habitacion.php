<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model
{
    use HasFactory;
    protected $table = "habitaciones";
    protected $fillable = ['nombre', 'tipo', 'capacidad', 'descripcion', 'precio'];
    //relacion de uno a muchos 
    public function imagenes(){
        return $this->hasMany(Imagen::class);
    
    }
}