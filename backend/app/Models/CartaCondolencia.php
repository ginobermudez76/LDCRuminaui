<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\HasUuidAndCode;
use Illuminate\Database\Eloquent\Model;

class CartaCondolencia extends Model
{
    use Auditable, HasUuidAndCode;

    const CODE_PREFIX = 'CAR';

    protected $table = 'carta_condolencias';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'codigo',
        'mensaje',
        'imagen',
        'fecha_eliminar',
    ];

    protected $hidden = [
        'id',
    ];

    protected $casts = [
        'fecha_eliminar' => 'datetime',
    ];
}
