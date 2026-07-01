<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Solicitud extends Model
{
    use Auditable;

    protected $table = 'solicitud';
    protected $primaryKey = 's_id';
    public $timestamps = false;

    protected $fillable = [
        's_fecha',
        's_doc',
        's_valor',
        'tipo',
        'solicitante',
        'encargado',
        'solicitantext',
        'descripcion',
        'estado',
        'departamento_encargado',
        'current_step_id',
    ];

    public function solicitanteRelation()
    {
        return $this->belongsTo(Usuario::class, 'solicitante', 'id');
    }

    public function encargadoRelation()
    {
        return $this->belongsTo(Usuario::class, 'encargado', 'id');
    }

    public function solicitantextRelation()
    {
        return $this->belongsTo(External::class, 'solicitantext', 'id_ext');
    }

    public function tipoRelation()
    {
        return $this->belongsTo(SolicitudTipo::class, 'tipo', 'id_tipo');
    }

    public function estadoRelation()
    {
        return $this->belongsTo(SolicitudEstado::class, 'estado', 'id_estado');
    }

    public function departamentoEncargadoRelation()
    {
        return $this->belongsTo(Rol::class, 'departamento_encargado', 'id');
    }

    public function currentStep()
    {
        return $this->belongsTo(WorkflowStep::class, 'current_step_id', 'id');
    }

    public function historiales()
    {
        return $this->hasMany(HistorialSolicitud::class, 'solicitud_id', 's_id');
    }
}
