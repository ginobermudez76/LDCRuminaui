<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\HasUuidAndCode;
use Illuminate\Database\Eloquent\Model;

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
