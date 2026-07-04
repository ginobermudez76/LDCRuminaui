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
        Schema::table('usuario', function (Blueprint $table) {
            if (!Schema::hasColumn('usuario', 'codigo')) {
                $table->string('codigo', 100)->nullable();
            }
        });

        Schema::table('solicitud', function (Blueprint $table) {
            if (!Schema::hasColumn('solicitud', 'uuid')) {
                $table->uuid('uuid')->nullable();
            }
            if (!Schema::hasColumn('solicitud', 'codigo')) {
                $table->string('codigo', 100)->nullable();
            }
        });

        Schema::table('opcion', function (Blueprint $table) {
            if (!Schema::hasColumn('opcion', 'codigo')) {
                $table->string('codigo', 100)->nullable();
            }
        });

        Schema::table('endpoint', function (Blueprint $table) {
            if (!Schema::hasColumn('endpoint', 'codigo')) {
                $table->string('codigo', 100)->nullable();
            }
        });

        $tablesWithBoth = ['deportes', 'eventos', 'logros', 'cursos', 'documentos', 'deportistas_destacados', 'carta_condolencias'];
        foreach ($tablesWithBoth as $t) {
            Schema::table($t, function (Blueprint $table) use ($t) {
                if (!Schema::hasColumn($t, 'uuid')) {
                    $table->uuid('uuid')->nullable();
                }
                if (!Schema::hasColumn($t, 'codigo')) {
                    $table->string('codigo', 100)->nullable();
                }
            });
        }

        // 2. Populate values for existing records
        $this->populateExistingRecords();

        // 3. Enforce UNIQUE and NOT NULL constraints
        Schema::table('usuario', function (Blueprint $table) {
            $table->string('codigo', 100)->nullable(false)->change();
            try {
                $table->unique('codigo');
            } catch (\Exception $e) {}
        });

        Schema::table('solicitud', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
            $table->string('codigo', 100)->nullable(false)->change();
            try {
                $table->unique('uuid');
            } catch (\Exception $e) {}
            try {
                $table->unique('codigo');
            } catch (\Exception $e) {}
        });

        Schema::table('opcion', function (Blueprint $table) {
            $table->string('codigo', 100)->nullable(false)->change();
            try {
                $table->unique('codigo');
            } catch (\Exception $e) {}
        });

        Schema::table('endpoint', function (Blueprint $table) {
            $table->string('codigo', 100)->nullable(false)->change();
            try {
                $table->unique('codigo');
            } catch (\Exception $e) {}
        });

        foreach ($tablesWithBoth as $t) {
            Schema::table($t, function (Blueprint $table) {
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

    private function populateExistingRecords(): void
    {
        $prefixes = [
            'usuario' => 'USR',
            'solicitud' => 'SOL',
            'opcion' => 'OPC',
            'endpoint' => 'END',
            'deportes' => 'DEP',
            'eventos' => 'EVE',
            'logros' => 'LOG',
            'cursos' => 'CUR',
            'documentos' => 'DOC',
            'deportistas_destacados' => 'DST',
            'carta_condolencias' => 'CAR'
        ];

        foreach ($prefixes as $table => $pref) {
            $records = DB::table($table)->get();
            foreach ($records as $r) {
                $updates = [];
                
                $uuidVal = isset($r->uuid) && !empty($r->uuid) ? $r->uuid : (string) Str::uuid();
                $updates['uuid'] = $uuidVal;

                // Check if current row already has a code
                if (isset($r->codigo) && !empty($r->codigo)) {
                    $codeVal = $r->codigo;
                } else {
                    $codeVal = $pref . '-' . strtoupper(Str::random(6));
                    // Ensure unique code
                    while (DB::table($table)->where('codigo', $codeVal)->exists()) {
                        $codeVal = $pref . '-' . strtoupper(Str::random(6));
                    }
                }
                $updates['codigo'] = $codeVal;

                $primaryKey = $table === 'solicitud' ? 's_id' : 'id';
                DB::table($table)->where($primaryKey, $r->{$primaryKey})->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            if (Schema::hasColumn('usuario', 'codigo')) {
                $table->dropColumn('codigo');
            }
        });

        Schema::table('solicitud', function (Blueprint $table) {
            if (Schema::hasColumn('solicitud', 'uuid')) {
                $table->dropColumn('uuid');
            }
            if (Schema::hasColumn('solicitud', 'codigo')) {
                $table->dropColumn('codigo');
            }
        });

        Schema::table('opcion', function (Blueprint $table) {
            if (Schema::hasColumn('opcion', 'codigo')) {
                $table->dropColumn('codigo');
            }
        });

        Schema::table('endpoint', function (Blueprint $table) {
            if (Schema::hasColumn('endpoint', 'codigo')) {
                $table->dropColumn('codigo');
            }
        });

        $tablesWithBoth = ['deportes', 'eventos', 'logros', 'cursos', 'documentos', 'deportistas_destacados', 'carta_condolencias'];
        foreach ($tablesWithBoth as $t) {
            Schema::table($t, function (Blueprint $table) use ($t) {
                if (Schema::hasColumn($t, 'uuid')) {
                    $table->dropColumn('uuid');
                }
                if (Schema::hasColumn($t, 'codigo')) {
                    $table->dropColumn('codigo');
                }
            });
        }
    }
};
