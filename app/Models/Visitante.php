<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitante extends Model
{
    // Si tu tabla se llama distinto (ej: 'visitas'), cámbialo aquí
    protected $table = 'visitantes'; 

    // Campos que permites que se llenen (ajusta según tus columnas de Postgres)
    protected $fillable = ['nombre', 'motivo', 'id_persona', 'fecha_ingreso'];
}