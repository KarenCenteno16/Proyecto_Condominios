<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens; // Añadido para la API
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios'; // Nombre de tu tabla en Postgres
    
    protected $fillable = [
        'id_persona', 
        'pass', // Tu columna de contraseña
        'admin'
    ];

    protected $hidden = [
        'pass', 
        'remember_token'
    ];

    // Relación con Persona
    public function persona() {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    /**
     * Laravel busca por defecto la columna 'password'. 
     * Como tú usas 'pass', debemos indicárselo así:
     */
    public function getAuthPassword()
    {
        return $this->pass;
    }
}