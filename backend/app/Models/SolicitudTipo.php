<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasUuidAndCode;

class SolicitudTipo extends Model
{
    use HasUuidAndCode;

    const CODE_PREFIX = 'WFT';

    protected $table = 'solicitud_tipo';
    protected $primaryKey = 'id_tipo';

    protected $fillable = [
        'uuid',
        'codigo',
        'name_tipo',
        'requiere_documento',
        'requiere_valor',
        'requiere_descripcion',
        'activo',
    ];

    protected $hidden = [
        'id_tipo',
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
