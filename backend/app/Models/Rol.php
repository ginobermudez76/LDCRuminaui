<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

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

    protected $hidden = [
        'id',
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
