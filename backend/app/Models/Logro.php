<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Logro extends Model
{
    use Auditable;

    protected $table = 'logros';
    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'deporte_id',
        'imagen',
        'tipologro',
    ];

    public function deporte()
    {
        return $this->belongsTo(Deporte::class, 'deporte_id', 'id');
    }
}
