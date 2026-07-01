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
        // 1. Seed the CONFIGURAR_RBAC option
        $optionId = DB::table('opcion')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'nombre_opcion' => 'CONFIGURAR_RBAC',
            'descripcion' => 'Administración de Roles y Permisos',
            'deleted' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Link this Option to the Administrador role (ID 8)
        DB::table('rol_opcion')->insert([
            'uuid' => (string) Str::uuid(),
            'id_rol' => 8,
            'id_opcion' => $optionId,
            'deleted' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Define all RbacAdmin endpoints to be protected
        $endpoints = [
            ['name' => 'Listar roles', 'method' => 'GET', 'url' => 'api/rbac/roles'],
            ['name' => 'Crear rol', 'method' => 'POST', 'url' => 'api/rbac/roles'],
            ['name' => 'Actualizar rol', 'method' => 'PUT', 'url' => 'api/rbac/roles/{id}'],
            ['name' => 'Eliminar rol', 'method' => 'DELETE', 'url' => 'api/rbac/roles/{id}'],
            ['name' => 'Asociar opciones a rol', 'method' => 'POST', 'url' => 'api/rbac/roles/{id}/opciones'],

            ['name' => 'Listar opciones', 'method' => 'GET', 'url' => 'api/rbac/opciones'],
            ['name' => 'Crear opcion', 'method' => 'POST', 'url' => 'api/rbac/opciones'],
            ['name' => 'Actualizar opcion', 'method' => 'PUT', 'url' => 'api/rbac/opciones/{id}'],
            ['name' => 'Eliminar opcion', 'method' => 'DELETE', 'url' => 'api/rbac/opciones/{id}'],
            ['name' => 'Asociar endpoints a opcion', 'method' => 'POST', 'url' => 'api/rbac/opciones/{id}/endpoints'],

            ['name' => 'Listar endpoints', 'method' => 'GET', 'url' => 'api/rbac/endpoints'],
            ['name' => 'Crear endpoint', 'method' => 'POST', 'url' => 'api/rbac/endpoints'],
            ['name' => 'Actualizar endpoint', 'method' => 'PUT', 'url' => 'api/rbac/endpoints/{id}'],
            ['name' => 'Eliminar endpoint', 'method' => 'DELETE', 'url' => 'api/rbac/endpoints/{id}'],
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

            // Link to the CONFIGURAR_RBAC option
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
        $option = DB::table('opcion')->where('nombre_opcion', 'CONFIGURAR_RBAC')->first();
        if ($option) {
            $endIds = DB::table('opcion_endpoint')
                ->where('id_opcion', $option->id)
                ->pluck('id_endpoint');

            DB::table('opcion_endpoint')->where('id_opcion', $option->id)->delete();
            DB::table('endpoint')->whereIn('id', $endIds)->delete();
            DB::table('rol_opcion')->where('id_opcion', $option->id)->delete();
            DB::table('opcion')->where('id', $option->id)->delete();
        }
    }
};
