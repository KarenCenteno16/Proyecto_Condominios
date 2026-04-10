<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class Usuario extends Authenticatable implements MustVerifyEmail 
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id'; 
    
    protected $fillable = [
        'id_persona', 
        'pass', 
        'admin', 
        'email_verified_at' // <--- Agrégala aquí
    ];

    protected $hidden = ['pass', 'remember_token'];

    // RELACIÓN: Un usuario pertenece a una persona
    public function persona() {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    // MAPEO DE CONTRASEÑA: Laravel busca 'password', tú usas 'pass'
    public function getAuthPassword() {
        return $this->pass;
    }

    /**
     * IMPORTANTE: Laravel MustVerifyEmail busca el campo 'email'.
     * Como tú lo tienes en la tabla personas como 'correo', usamos este método:
     */

    // También indicamos dónde encontrar el email para notificaciones generales
    public function routeNotificationForMail($notification) {
        return $this->persona->correo;
    }

        public function getEmailForVerification()
    {
        // Esto le dice a Laravel que el correo para verificar 
        // está en la relación con persona
        return $this->persona->correo;
    }
}