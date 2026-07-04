<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

trait HasUuidAndCode
{
    /**
     * Boot the trait to generate UUID and Code automatically on create.
     */
    protected static function bootHasUuidAndCode()
    {
        static::creating(function ($model) {
            // Check if column exists in schema before setting
            $table = $model->getTable();
            
            if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'uuid')) {
                if (empty($model->uuid)) {
                    $model->uuid = (string) Str::uuid();
                }
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'codigo')) {
                if (empty($model->codigo)) {
                    $model->codigo = static::generateUniqueCode();
                }
            }
        });
    }

    /**
     * Generate a unique alphanumeric code with the configured prefix.
     */
    public static function generateUniqueCode(): string
    {
        $prefix = defined('static::CODE_PREFIX') ? static::CODE_PREFIX : 'GEN';
        $table = (new static)->getTable();
        
        do {
            $code = $prefix . '-' . strtoupper(Str::random(6));
        } while (DB::table($table)->where('codigo', $code)->exists());

        return $code;
    }
}
