<?php

namespace App\Models;

use App\Traits\HasUuidAndCode;
use Illuminate\Database\Eloquent\Model;

class WorkflowStep extends Model
{
    use HasUuidAndCode;

    const CODE_PREFIX = 'STP';

    protected $table = 'workflow_steps';

    protected $fillable = [
        'uuid',
        'codigo',
        'solicitud_tipo_id',
        'orden',
        'rol_id',
        'nombre_paso',
    ];

    protected $hidden = [
        'id',
    ];

    public function solicitudTipo()
    {
        return $this->belongsTo(SolicitudTipo::class, 'solicitud_tipo_id', 'id_tipo');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id', 'id');
    }
}
