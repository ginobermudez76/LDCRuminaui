<?php

namespace App\Traits;

use App\Models\Auditoria;
use Illuminate\Support\Str;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            static::logAudit($model, 'CREATE', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $changed = $model->getChanges();
            // Map original values for changed keys
            $original = array_intersect_key($model->getOriginal(), $changed);
            
            // If nothing actually changed, don't audit
            if (empty($changed)) {
                return;
            }
            static::logAudit($model, 'UPDATE', $original, $changed);
        });

        static::deleted(function ($model) {
            static::logAudit($model, 'DELETE', $model->getOriginal(), null);
        });
    }

    protected static function logAudit($model, string $action, $before, $after)
    {
        // Prevent infinite recursion if auditing the Auditoria model itself
        if ($model instanceof Auditoria) {
            return;
        }

        try {
            $user = auth('api')->user()?->nombre_usuario ?? 'System';
        } catch (\Exception $e) {
            $user = 'System';
        }

        // Determine entity primary key value
        $idKey = $model->getKeyName();
        $idVal = $model->getAttribute($idKey) ?? 0;

        Auditoria::create([
            'uuid' => (string) Str::uuid(),
            'entidad' => get_class($model),
            'id_entidad' => intval($idVal),
            'accion' => $action,
            'datos_anteriores' => $before,
            'datos_nuevos' => $after,
            'usuario' => $user,
            'fecha' => now(),
        ]);
    }
}
