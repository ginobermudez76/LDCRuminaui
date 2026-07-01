<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialSolicitud extends Model
{
    use HasFactory;

    protected $table = 'historial_solicitud';
    protected $primaryKey = 'historial_id';
    public $timestamps = false;

    protected $fillable = [
        'solicitud_id',
        'fecha_asignacion',
        'estado',
        'responsable',
        'departamento',
        'tipo',
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id', 's_id');
    }

    public function responsableUsuario()
    {
        return $this->belongsTo(Usuario::class, 'responsable', 'id');
    }

    public function departamentoRol()
    {
        return $this->belongsTo(Rol::class, 'departamento', 'id');
    }

    public function estadoRelation()
    {
        return $this->belongsTo(SolicitudEstado::class, 'estado', 'id_estado');
    }
}
