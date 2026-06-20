<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartaCondolencia extends Model
{
    protected $table = 'carta_condolencias';

    protected $fillable = [
        'mensaje',
        'imagen',
        'fecha_eliminar',
    ];

    protected $casts = [
        'fecha_eliminar' => 'datetime',
    ];
}
