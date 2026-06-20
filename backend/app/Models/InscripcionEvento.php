<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

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
