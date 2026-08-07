<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\HasUuidAndCode;
use Illuminate\Database\Eloquent\Model;

class Deporte extends Model
{
    use Auditable, HasUuidAndCode;

    const CODE_PREFIX = 'DEP';

    protected $table = 'deportes';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'codigo',
        'nombre',
        'descripcion',
        'imagen',
    ];

    protected $hidden = [
        'id',
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
