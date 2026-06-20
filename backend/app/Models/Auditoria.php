<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'auditoria';
    
    // Disable Laravel's automatic timestamp columns since bitacora uses custom date column "fecha"
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'entidad',
        'id_entidad',
        'accion',
        'datos_anteriores',
        'datos_nuevos',
        'usuario',
        'fecha',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'fecha' => 'datetime',
    ];
}
