<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Traits\Auditable;

class Usuario extends Authenticatable implements JWTSubject
{
    use Notifiable, Auditable;

    protected $table = 'usuario';

    protected $fillable = [
        'uuid',
        'nombre_usuario',
        'correo_electronico',
        'password_hash',
        'nombres',
        'apellidos',
        'activo',
        'cedula',
        'celular',
        'fecha_nac',
        'deleted',
        'deleted_at',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $appends = [
        'rol',
        'rol_relation',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'correo',
        'contrasena',
        'opciones',
    ];

    /**
     * Get the password for the user (override default).
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rol_usuario', 'id_usuario', 'id_rol');
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
     * Virtual attributes to preserve legacy code compatibility.
     */

    public function getRolAttribute()
    {
        return $this->roles->first()?->id;
    }

    public function getRolRelationAttribute()
    {
        $firstRole = $this->roles->first();
        if (!$firstRole) {
            return null;
        }
        return [
            'id_rol' => $firstRole->id,
            'rol_name' => $firstRole->nombre_rol
        ];
    }

    public function getPrimerNombreAttribute()
    {
        if (empty($this->nombres)) {
            return '';
        }
        $parts = explode(' ', trim($this->nombres));
        return $parts[0] ?? '';
    }

    public function getSegundoNombreAttribute()
    {
        if (empty($this->nombres)) {
            return '';
        }
        $parts = explode(' ', trim($this->nombres));
        array_shift($parts); // Remove first name
        return implode(' ', $parts);
    }

    public function getPrimerApellidoAttribute()
    {
        if (empty($this->apellidos)) {
            return '';
        }
        $parts = explode(' ', trim($this->apellidos));
        return $parts[0] ?? '';
    }

    public function getSegundoApellidoAttribute()
    {
        if (empty($this->apellidos)) {
            return '';
        }
        $parts = explode(' ', trim($this->apellidos));
        array_shift($parts); // Remove first surname
        return implode(' ', $parts);
    }

    public function getCorreoAttribute()
    {
        return $this->correo_electronico;
    }

    public function getContrasenaAttribute()
    {
        return $this->password_hash;
    }

    /**
     * Retrieve options (permissions) assigned to user's roles.
     */
    public function getOpcionesAttribute()
    {
        $opciones = [];
        foreach ($this->roles as $role) {
            foreach ($role->opciones as $opcion) {
                $opciones[] = $opcion->nombre_opcion;
            }
        }
        return array_values(array_unique($opciones));
    }

    /**
     * Helper to verify permission.
     */
    public function hasOption(string $optionName): bool
    {
        return in_array($optionName, $this->opciones);
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
        $firstRole = $this->roles->first();
        return [
            'username' => $this->nombre_usuario,
            'role' => $firstRole ? $firstRole->nombre_rol : null,
            'opciones' => $this->opciones,
        ];
    }
}
