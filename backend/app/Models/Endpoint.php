<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Endpoint extends Model
{
    use Auditable;

    protected $table = 'endpoint';

    protected $fillable = [
        'uuid',
        'nombre_endpoint',
        'metodo',
        'url',
        'rbac_enabled',
        'deleted',
        'deleted_at',
    ];

    public function opciones()
    {
        return $this->belongsToMany(Opcion::class, 'opcion_endpoint', 'id_endpoint', 'id_opcion');
    }
}
