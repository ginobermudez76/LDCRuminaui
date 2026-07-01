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
        $option = DB::table('opcion')->where('nombre_opcion', 'REGISTRAR_USUARIOS')->first();
        
        if (!$option) {
            return;
        }

        $optionId = $option->id;

        $endpoints = [
            ['name' => 'Listar usuarios', 'method' => 'GET', 'url' => 'api/usuarios'],
            ['name' => 'Crear usuario', 'method' => 'POST', 'url' => 'api/usuarios'],
            ['name' => 'Actualizar usuario', 'method' => 'PUT', 'url' => 'api/usuarios/{id}'],
            ['name' => 'Eliminar usuario', 'method' => 'DELETE', 'url' => 'api/usuarios/{id}'],
            ['name' => 'Activar o desactivar usuario', 'method' => 'PATCH', 'url' => 'api/usuarios/{id}/toggle-active'],
            ['name' => 'Reestablecer contraseña', 'method' => 'POST', 'url' => 'api/usuarios/{id}/reset-password'],
            ['name' => 'Reenviar invitación', 'method' => 'POST', 'url' => 'api/usuarios/{id}/resend-invitation'],
        ];

        foreach ($endpoints as $end) {
            $endId = DB::table('endpoint')->insertGetId([
                'uuid' => (string) Str::uuid(),
                'nombre_endpoint' => $end['name'],
                'metodo' => $end['method'],
                'url' => $end['url'],
                'rbac_enabled' => true,
                'deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Link to the REGISTRAR_USUARIOS option
            DB::table('opcion_endpoint')->insert([
                'uuid' => (string) Str::uuid(),
                'id_opcion' => $optionId,
                'id_endpoint' => $endId,
                'deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $option = DB::table('opcion')->where('nombre_opcion', 'REGISTRAR_USUARIOS')->first();
        if ($option) {
            $endpoints = [
                'api/usuarios',
                'api/usuarios/{id}',
                'api/usuarios/{id}/toggle-active',
                'api/usuarios/{id}/reset-password',
                'api/usuarios/{id}/resend-invitation',
            ];

            $endIds = DB::table('endpoint')
                ->whereIn('url', $endpoints)
                ->pluck('id');

            DB::table('opcion_endpoint')->whereIn('id_endpoint', $endIds)->delete();
            DB::table('endpoint')->whereIn('id', $endIds)->delete();
        }
    }
};
