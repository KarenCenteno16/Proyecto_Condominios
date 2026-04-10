<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'personas';

    protected $fillable = [
        'nombre',
        'apellido_p',
        'apellido_m',
        'celular',
        'correo', 
        'activo'
    ];

    public function usuario() {
        return $this->hasOne(Usuario::class, 'id_persona');
    }
}