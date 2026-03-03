<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    use HasFactory;

    protected $table = 'mensajes';

    public $timestamps = true; 

    protected $fillable = [
        'remitente', 
        'destinatario', 
        'mensaje', 
        'fecha'
    ];


    public function usuarioRemitente()
    {
        return $this->belongsTo(Usuario::class, 'remitente', 'id');
    }

    public function usuarioDestinatario()
    {
        return $this->belongsTo(Usuario::class, 'destinatario', 'id');
    }
}