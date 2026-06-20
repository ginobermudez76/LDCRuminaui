<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Opcion extends Model
{
    use Auditable;

    protected $table = 'opcion';

    protected $fillable = [
        'uuid',
        'nombre_opcion',
        'descripcion',
        'deleted',
        'deleted_at',
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
