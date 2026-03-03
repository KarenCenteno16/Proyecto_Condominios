<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_persona'; 
    public $incrementing = false; 
    
    protected $fillable = [
        'id_persona', 
        'pass', 
        'admin'
    ];

    protected $hidden = [
        'pass', 
        'remember_token'
    ];

    public function getAuthPassword()
    {
        return $this->pass; 
    }
}