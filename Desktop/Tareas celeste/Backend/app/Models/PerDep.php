<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerDep extends Model
{
    protected $table = 'per_dep'; // Forzamos el nombre de tu tabla
    protected $fillable = ['id_persona', 'id_depa', 'id_rol', 'residente', 'codigo'];

    public function departamento() {
        return $this->belongsTo(Departamento::class, 'id_depa');
    }
}