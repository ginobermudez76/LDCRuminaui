<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Noticia extends Model
{
    use Auditable;

    protected $table = 'noticias';
    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'imagen',
        'cuerpo',
        'fecha',
    ];
}
