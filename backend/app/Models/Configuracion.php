<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Configuracion extends Model
{
    use Auditable;

    protected $table = 'configuracion';

    protected $fillable = [
        'uuid',
        'clave',
        'valor',
        'descripcion',
        'deleted',
        'deleted_at',
    ];
}
