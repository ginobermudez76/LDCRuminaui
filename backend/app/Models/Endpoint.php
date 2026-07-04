<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

use App\Traits\HasUuidAndCode;

class Endpoint extends Model
{
    use Auditable, HasUuidAndCode;

    const CODE_PREFIX = 'END';

    protected $table = 'endpoint';

    protected $fillable = [
        'uuid',
        'codigo',
        'nombre_endpoint',
        'metodo',
        'url',
        'rbac_enabled',
        'deleted',
        'deleted_at',
    ];

    protected $hidden = [
        'id',
    ];

    public function opciones()
    {
        return $this->belongsToMany(Opcion::class, 'opcion_endpoint', 'id_endpoint', 'id_opcion');
    }
}
