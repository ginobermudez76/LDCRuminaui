<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = ['noticias', 'galeria_imagenes', 'inscripciones_eventos'];

        foreach ($tables as $t) {
            Schema::table($t, function (Blueprint $table) use ($t) {
                if (!Schema::hasColumn($t, 'uuid')) {
                    $table->uuid('uuid')->nullable();
                }
                if (!Schema::hasColumn($t, 'codigo')) {
                    $table->string('codigo', 100)->nullable();
                }
            });
        }

        // Populate
        $prefixes = [
            'noticias' => 'NOT',
            'galeria_imagenes' => 'GAL',
            'inscripciones_eventos' => 'INS'
        ];

        foreach ($prefixes as $table => $pref) {
            $records = DB::table($table)->get();
            foreach ($records as $r) {
                $updates = [];
                $updates['uuid'] = (string) Str::uuid();

                $codeVal = $pref . '-' . strtoupper(Str::random(6));
                while (DB::table($table)->where('codigo', $codeVal)->exists()) {
                    $codeVal = $pref . '-' . strtoupper(Str::random(6));
                }
                $updates['codigo'] = $codeVal;

                DB::table($table)->where('id', $r->id)->update($updates);
            }
        }

        // Constraints
        foreach ($tables as $t) {
            Schema::table($t, function (Blueprint $table) use ($t) {
                $table->uuid('uuid')->nullable(false)->change();
                $table->string('codigo', 100)->nullable(false)->change();
                try {
                    $table->unique('uuid');
                } catch (\Exception $e) {}
                try {
                    $table->unique('codigo');
                } catch (\Exception $e) {}
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['noticias', 'galeria_imagenes', 'inscripciones_eventos'];
        foreach ($tables as $t) {
            Schema::table($t, function (Blueprint $table) use ($t) {
                $table->dropColumn(['uuid', 'codigo']);
            });
        }
    }
};
