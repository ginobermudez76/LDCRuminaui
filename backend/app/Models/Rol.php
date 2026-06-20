<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Rol extends Model
{
    use Auditable;

    protected $table = 'rol';

    protected $fillable = [
        'uuid',
        'codigo',
        'nombre_rol',
        'descripcion',
        'deleted',
        'deleted_at',
    ];

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'rol_usuario', 'id_rol', 'id_usuario');
    }

    public function opciones()
    {
        return $this->belongsToMany(Opcion::class, 'rol_opcion', 'id_rol', 'id_opcion');
    }

    public function solicitudesAsignadas()
    {
        return $this->hasMany(Solicitud::class, 'departamento_encargado', 'id');
    }
}
