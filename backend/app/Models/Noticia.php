<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

use App\Traits\HasUuidAndCode;

class Noticia extends Model
{
    use Auditable, HasUuidAndCode;

    const CODE_PREFIX = 'NOT';

    protected $table = 'noticias';
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'codigo',
        'titulo',
        'imagen',
        'cuerpo',
        'fecha',
    ];

    protected $hidden = [
        'id',
    ];
}
