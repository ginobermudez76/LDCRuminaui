<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\HasUuidAndCode;
use Illuminate\Database\Eloquent\Model;

class DeportistaDestacado extends Model
{
    use Auditable, HasUuidAndCode;

    const CODE_PREFIX = 'DST';

    protected $table = 'deportistas_destacados';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'codigo',
        'nombre_deportista',
        'deporte_id',
        'imagen',
    ];

    protected $hidden = [
        'id',
    ];

    public function deporte()
    {
        return $this->belongsTo(Deporte::class, 'deporte_id', 'id');
    }
}
