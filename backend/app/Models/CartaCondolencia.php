<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class CartaCondolencia extends Model
{
    use Auditable;

    protected $table = 'carta_condolencias';
    public $timestamps = false;

    protected $fillable = [
        'mensaje',
        'imagen',
        'fecha_eliminar',
    ];

    protected $casts = [
        'fecha_eliminar' => 'datetime',
    ];
}
