<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\HasUuidAndCode;
use Illuminate\Database\Eloquent\Model;

class Logro extends Model
{
    use Auditable, HasUuidAndCode;

    const CODE_PREFIX = 'LOG';

    protected $table = 'logros';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'codigo',
        'titulo',
        'deporte_id',
        'imagen',
        'tipologro',
    ];

    protected $hidden = [
        'id',
    ];

    public function deporte()
    {
        return $this->belongsTo(Deporte::class, 'deporte_id', 'id');
    }
}
