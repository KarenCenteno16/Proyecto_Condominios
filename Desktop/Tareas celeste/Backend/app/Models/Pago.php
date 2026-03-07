<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';
    protected $fillable = ['id_depa', 'monto', 'fecha', 'id_tipo', 'id_motivo', 'descripcion', 'comprobante', 'efectuado'];

    public function departamento() {
        return $this->belongsTo(Departamento::class, 'id_depa');
    }
}