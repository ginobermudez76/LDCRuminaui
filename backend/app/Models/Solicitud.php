<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'solicitud';
    protected $primaryKey = 's_id';

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
        return $this->belongsTo(Rol::class, 'departamento_encargado', 'id_rol');
    }
}
