<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\Auditable;

class Evento extends Model
{
    use Auditable;

    protected $table = 'eventos';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'fecha_eliminar',
        'imagen',
        'descripcion',
        'deporte_id',
        'estado',
        'inscripciones',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_eliminar' => 'datetime',
    ];

    public function deporte()
    {
        return $this->belongsTo(Deporte::class, 'deporte_id', 'id');
    }

    public function inscripcionesEventos()
    {
        return $this->hasMany(InscripcionEvento::class, 'evento_id', 'id');
    }

    /**
     * Replaces SQL Triggers (tr_antesdeinsertar & tr_before_update_evento)
     */
    protected static function booted()
    {
        // Call parent boot logic for traits like Auditable
        parent::booted();

        static::saving(function ($evento) {
            // Update fecha_eliminar based on fecha_fin + 3 days
            if ($evento->fecha_fin) {
                $fechaFin = Carbon::parse($evento->fecha_fin);
                $evento->fecha_eliminar = $fechaFin->addDays(3);
            }

            // Update status based on current date
            if ($evento->fecha_inicio && $evento->fecha_fin) {
                $today = Carbon::today();
                $start = Carbon::parse($evento->fecha_inicio);
                $end = Carbon::parse($evento->fecha_fin);

                if ($end->lt($today)) {
                    $evento->estado = 'Finalizado';
                } elseif ($today->gte($start) && $today->lte($end)) {
                    $evento->estado = 'En curso';
                } elseif ($start->gt($today)) {
                    $evento->estado = 'Proximamente';
                }
            }
        });
    }
}
