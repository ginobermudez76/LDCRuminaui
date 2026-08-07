<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

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
