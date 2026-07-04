<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasUuidAndCode;

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
