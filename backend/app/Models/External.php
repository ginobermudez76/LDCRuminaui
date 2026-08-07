<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class External extends Model
{
    use Auditable;

    protected $table = 'external';

    protected $primaryKey = 'id_ext';

    public $timestamps = false;

    protected $fillable = [
        'ext_nombre',
        'ext_snombre',
        'ext_apellido',
        'ext_sapellido',
        'ext_email',
        'ext_celular',
        'cedula',
        'fecha_nac',
    ];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'solicitantext', 'id_ext');
    }
}
