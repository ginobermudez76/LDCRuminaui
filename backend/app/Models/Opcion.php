<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

use App\Traits\HasUuidAndCode;

class Opcion extends Model
{
    use Auditable, HasUuidAndCode;

    const CODE_PREFIX = 'OPC';

    protected $table = 'opcion';

    protected $fillable = [
        'uuid',
        'codigo',
        'nombre_opcion',
        'descripcion',
        'deleted',
        'deleted_at',
    ];

    protected $hidden = [
        'id',
    ];

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rol_opcion', 'id_opcion', 'id_rol');
    }

    public function endpoints()
    {
        return $this->belongsToMany(Endpoint::class, 'opcion_endpoint', 'id_opcion', 'id_endpoint');
    }
}
