<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeportistaDestacado extends Model
{
    protected $table = 'deportistas_destacados';

    protected $fillable = [
        'nombre_deportista',
        'deporte_id',
        'imagen',
    ];

    public function deporte()
    {
        return $this->belongsTo(Deporte::class, 'deporte_id', 'id');
    }
}
