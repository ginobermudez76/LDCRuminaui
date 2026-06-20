<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InscripcionEvento extends Model
{
    protected $table = 'inscripciones_eventos';

    protected $fillable = [
        'evento_id',
        'nombre',
        'ciudad',
        'imagen',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id', 'id');
    }
}
