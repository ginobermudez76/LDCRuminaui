<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudTipo extends Model
{
    protected $table = 'solicitud_tipo';
    protected $primaryKey = 'id_tipo';

    protected $fillable = [
        'name_tipo',
        'requiere_documento',
        'requiere_valor',
        'requiere_descripcion',
        'activo',
    ];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'tipo', 'id_tipo');
    }

    public function steps()
    {
        return $this->hasMany(WorkflowStep::class, 'solicitud_tipo_id', 'id_tipo')->orderBy('orden', 'asc');
    }
}
