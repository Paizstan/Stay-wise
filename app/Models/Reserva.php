<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Ensure User model is imported

class Reserva extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'user_id']; // Ensure user_id is fillable

    public function user()  
    {  
        return $this->belongsTo(User::class, 'user_id'); // Cambia hasMany por belongsTo  
    }
}
