<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class DeportistaDestacado extends Model
{
    use Auditable;

    protected $table = 'deportistas_destacados';
    public $timestamps = false;

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
