<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudEstado extends Model
{
    protected $table = 'solicitud_estado';

    protected $primaryKey = 'id_estado';

    protected $fillable = [
        'estado_nombre',
    ];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'estado', 'id_estado');
    }
}
