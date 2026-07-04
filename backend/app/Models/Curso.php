<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasUuidAndCode;

class Curso extends Model
{
    use HasFactory, HasUuidAndCode;

    const CODE_PREFIX = 'CUR';

    protected $table = 'cursos';

    protected $fillable = [
        'uuid',
        'codigo',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'fecha_eliminar',
        'imagen',
        'descripcion',
        'deporte_id',
        'estado',
        'inscripciones',
    ];

    protected $hidden = [
        'id',
    ];

    public function deporte()
    {
        return $this->belongsTo(Deporte::class, 'deporte_id');
    }
}
