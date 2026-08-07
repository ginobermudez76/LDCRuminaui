<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Ensure default request types exist
        DB::table('solicitud_tipo')->updateOrInsert(['id_tipo' => 1], ['name_tipo' => 'Deportiva']);
        DB::table('solicitud_tipo')->updateOrInsert(['id_tipo' => 2], ['name_tipo' => 'Administrativa/Alquiler']);
        DB::table('solicitud_tipo')->updateOrInsert(['id_tipo' => 3], ['name_tipo' => 'Administrativa/Cultural']);
        DB::table('solicitud_tipo')->updateOrInsert(['id_tipo' => 4], ['name_tipo' => 'Otro tipo']);

        // Update requirements for default request types
        DB::table('solicitud_tipo')->where('id_tipo', 1)->update([
            'requiere_documento' => false,
            'requiere_valor' => false,
            'requiere_descripcion' => true,
        ]);

        DB::table('solicitud_tipo')->where('id_tipo', 2)->update([
            'requiere_documento' => false,
            'requiere_valor' => false,
            'requiere_descripcion' => true,
        ]);

        DB::table('solicitud_tipo')->where('id_tipo', 3)->update([
            'requiere_documento' => false,
            'requiere_valor' => false,
            'requiere_descripcion' => true,
        ]);

        DB::table('solicitud_tipo')->where('id_tipo', 4)->update([
            'requiere_documento' => false,
            'requiere_valor' => false,
            'requiere_descripcion' => true,
        ]);

        // 2. Clear existing steps
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('workflow_steps')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 3. Seed default steps

        // Deportiva (id 1): Metodólogo (rol 2) -> Tesorería (rol 3)
        DB::table('workflow_steps')->insert([
            [
                'solicitud_tipo_id' => 1,
                'orden' => 1,
                'rol_id' => 2, // Metodólogo
                'nombre_paso' => 'Aprobación Metodología',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'solicitud_tipo_id' => 1,
                'orden' => 2,
                'rol_id' => 3, // Tesorería
                'nombre_paso' => 'Aprobación Tesorería',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Administrativa Alquiler (id 2): Coordinador General (rol 4) -> Tesorería (rol 3)
        DB::table('workflow_steps')->insert([
            [
                'solicitud_tipo_id' => 2,
                'orden' => 1,
                'rol_id' => 4, // Coordinador General
                'nombre_paso' => 'Revisión Coordinador',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'solicitud_tipo_id' => 2,
                'orden' => 2,
                'rol_id' => 3, // Tesorería
                'nombre_paso' => 'Aprobación Tesorería',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Administrativa Cultural (id 3): Coordinador General (rol 4) -> Tesorería (rol 3)
        DB::table('workflow_steps')->insert([
            [
                'solicitud_tipo_id' => 3,
                'orden' => 1,
                'rol_id' => 4, // Coordinador General
                'nombre_paso' => 'Revisión Coordinador',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'solicitud_tipo_id' => 3,
                'orden' => 2,
                'rol_id' => 3, // Tesorería
                'nombre_paso' => 'Aprobación Tesorería',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
