<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logro extends Model
{
    protected $table = 'logros';

    protected $fillable = [
        'titulo',
        'deporte_id',
        'imagen',
    ];

    public function deporte()
    {
        return $this->belongsTo(Deporte::class, 'deporte_id', 'id');
    }
}
