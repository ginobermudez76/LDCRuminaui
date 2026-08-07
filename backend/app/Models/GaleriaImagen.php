<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class GaleriaImagen extends Model
{
    use Auditable;

    protected $table = 'galeria_imagenes';

    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'id_tipo',
        'nombre',
        'ruta_imagenes',
        'ruta_carpeta',
    ];
}
