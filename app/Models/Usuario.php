<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_persona';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_persona',
        'pass',
        'admin',
        'email_verified_at'
    ];

    protected $hidden = [
        'pass',
        'remember_token'
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona', 'id');
    }

    public function getAuthPassword()
    {
        return $this->pass;
    }

    public function routeNotificationForMail($notification)
    {
        return $this->persona ? $this->persona->correo : null;
    }

    public function getEmailForVerification()
    {
        return $this->persona ? $this->persona->correo : null;
    }
}