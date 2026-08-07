<?php

namespace App\Models;

use App\Traits\HasUuidAndCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory, HasUuidAndCode;

    const CODE_PREFIX = 'DOC';

    protected $table = 'documentos';

    protected $fillable = [
        'uuid',
        'codigo',
        'nombre',
        'descripcion',
        'documento',
    ];

    protected $hidden = [
        'id',
    ];
}
