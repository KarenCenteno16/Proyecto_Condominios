<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class Persona extends Model
{
    protected $table = 'personas';
    protected $fillable = ['nombre', 'apellido_p', 'apellido_m', 'celular', 'activo'];

    // Relación con Usuario
    public function usuario() {
        return $this->hasOne(Usuario::class, 'id_persona');
    }

    // Relación con la tabla intermedia per_dep
  
    public function perDeps() {
        return $this->hasMany(PerDep::class, 'id_persona');
    }
}