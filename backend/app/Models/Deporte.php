<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deporte extends Model
{
    protected $table = 'deportes';

    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen',
    ];

    public function deportistasDestacados()
    {
        return $this->hasMany(DeportistaDestacado::class, 'deporte_id', 'id');
    }

    public function eventos()
    {
        return $this->hasMany(Evento::class, 'deporte_id', 'id');
    }

    public function logros()
    {
        return $this->hasMany(Logro::class, 'deporte_id', 'id');
    }
}
