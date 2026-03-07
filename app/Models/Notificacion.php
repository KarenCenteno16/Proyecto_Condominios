<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones'; // Nombre de la tabla

    protected $fillable = [
        'usuario_id', 
        'tipo', 
        'titulo', 
        'mensaje', 
        'url', 
        'leido'
    ];
} //