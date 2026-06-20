<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudTipo extends Model
{
    protected $table = 'solicitud_tipo';
    protected $primaryKey = 'id_tipo';

    protected $fillable = [
        'name_tipo',
    ];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'tipo', 'id_tipo');
    }
}
