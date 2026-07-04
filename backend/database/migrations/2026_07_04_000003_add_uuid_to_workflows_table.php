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
        // 1. Add columns as nullable first
        Schema::table('solicitud_tipo', function (Blueprint $table) {
            $table->uuid('uuid')->nullable();
            $table->string('codigo', 100)->nullable();
        });

        Schema::table('workflow_steps', function (Blueprint $table) {
            $table->uuid('uuid')->nullable();
            $table->string('codigo', 100)->nullable();
        });

        // 2. Populate values
        $prefixes = [
            'solicitud_tipo' => 'WFT',
            'workflow_steps' => 'STP'
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

                $primaryKey = $table === 'solicitud_tipo' ? 'id_tipo' : 'id';
                DB::table($table)->where($primaryKey, $r->{$primaryKey})->update($updates);
            }
        }

        // 3. Enforce NOT NULL and UNIQUE
        Schema::table('solicitud_tipo', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
            $table->string('codigo', 100)->nullable(false)->change();
            $table->unique('uuid');
            $table->unique('codigo');
        });

        Schema::table('workflow_steps', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
            $table->string('codigo', 100)->nullable(false)->change();
            $table->unique('uuid');
            $table->unique('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_tipo', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'codigo']);
        });

        Schema::table('workflow_steps', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'codigo']);
        });
    }
};
