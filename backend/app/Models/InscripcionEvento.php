<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class InscripcionEvento extends Model
{
    use Auditable;

    protected $table = 'inscripciones_eventos';

    public $timestamps = false;

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
