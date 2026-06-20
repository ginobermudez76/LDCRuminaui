<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id_rol';

    protected $fillable = [
        'rol_name',
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'rol', 'id_rol');
    }

    public function solicitudesAsignadas()
    {
        return $this->hasMany(Solicitud::class, 'departamento_encargado', 'id_rol');
    }
}
