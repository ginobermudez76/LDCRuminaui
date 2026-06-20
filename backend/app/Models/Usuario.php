<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Usuario extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'cedula',
        'celular',
        'correo',
        'nombre_usuario',
        'contrasena',
        'rol',
        'fecha_nac',
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    /**
     * Get the password for the user (override default).
     */
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function rolRelation()
    {
        return $this->belongsTo(Rol::class, 'rol', 'id_rol');
    }

    public function solicitudesCreadas()
    {
        return $this->hasMany(Solicitud::class, 'solicitante', 'id');
    }

    public function solicitudesAsignadas()
    {
        return $this->hasMany(Solicitud::class, 'encargado', 'id');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [
            'username' => $this->nombre_usuario,
            'role' => $this->rolRelation ? $this->rolRelation->rol_name : null,
        ];
    }
}
