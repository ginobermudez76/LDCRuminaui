<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop foreign keys on existing tables referencing roles/usuarios
        Schema::table('solicitud', function (Blueprint $table) {
            $table->dropForeign(['solicitante']);
            $table->dropForeign(['encargado']);
            $table->dropForeign(['departamento_encargado']);
        });

        // 2. Create the new tables
        Schema::create('rol', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->uuid('uuid')->unique();
            $table->string('codigo', 100)->unique();
            $table->string('nombre_rol', 100);
            $table->string('descripcion', 255)->nullable();
            $table->boolean('deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('usuario', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->uuid('uuid')->unique();
            $table->string('nombre_usuario', 150)->unique();
            $table->string('correo_electronico', 100);
            $table->string('password_hash', 1000);
            $table->string('nombres', 150)->nullable();
            $table->string('apellidos', 150)->nullable();
            $table->boolean('activo')->default(true);
            $table->string('cedula', 10)->nullable();
            $table->string('celular', 10)->nullable();
            $table->date('fecha_nac')->nullable();
            $table->boolean('deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('rol_usuario', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->uuid('uuid')->unique();
            $table->integer('id_rol');
            $table->integer('id_usuario');
            $table->boolean('deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('id_rol')->references('id')->on('rol')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id')->on('usuario')->onDelete('cascade');
        });

        Schema::create('opcion', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->uuid('uuid')->unique();
            $table->string('nombre_opcion', 150);
            $table->string('descripcion', 255)->nullable();
            $table->boolean('deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('endpoint', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->uuid('uuid')->unique();
            $table->string('nombre_endpoint', 150);
            $table->string('metodo', 15);
            $table->string('url', 255);
            $table->boolean('rbac_enabled')->default(true);
            $table->boolean('deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('rol_opcion', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->uuid('uuid')->unique();
            $table->integer('id_rol');
            $table->integer('id_opcion');
            $table->boolean('deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('id_rol')->references('id')->on('rol')->onDelete('cascade');
            $table->foreign('id_opcion')->references('id')->on('opcion')->onDelete('cascade');
        });

        Schema::create('opcion_endpoint', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->uuid('uuid')->unique();
            $table->integer('id_opcion');
            $table->integer('id_endpoint');
            $table->boolean('deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('id_opcion')->references('id')->on('opcion')->onDelete('cascade');
            $table->foreign('id_endpoint')->references('id')->on('endpoint')->onDelete('cascade');
        });

        Schema::create('configuracion', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->uuid('uuid')->unique();
            $table->string('clave', 150)->unique();
            $table->text('valor')->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->boolean('deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('auditoria', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();
            $table->string('entidad', 150);
            $table->integer('id_entidad');
            $table->string('accion', 150);
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->string('usuario', 150)->nullable();
            $table->timestamp('fecha');
        });

        // 3. Migrate data from old tables roles and usuarios
        // Populate new roles first
        $rolesData = [
            ['id' => 1, 'nombre_rol' => 'Presidente', 'codigo' => 'PRESIDENTE'],
            ['id' => 2, 'nombre_rol' => 'Metodologo', 'codigo' => 'METODOLOGO'],
            ['id' => 3, 'nombre_rol' => 'Tesoreria', 'codigo' => 'TESORERIA'],
            ['id' => 4, 'nombre_rol' => 'Coordinador general', 'codigo' => 'COORDINADOR'],
            ['id' => 5, 'nombre_rol' => 'Deportista', 'codigo' => 'DEPORTISTA'],
            ['id' => 6, 'nombre_rol' => 'Entrenador', 'codigo' => 'ENTRENADOR'],
            ['id' => 7, 'nombre_rol' => 'Publicista', 'codigo' => 'PUBLICISTA'],
            ['id' => 8, 'nombre_rol' => 'Administrador', 'codigo' => 'ADMINISTRADOR'],
            ['id' => 9, 'nombre_rol' => 'Secretaría', 'codigo' => 'SECRETARIA'],
        ];

        foreach ($rolesData as $rol) {
            DB::table('rol')->insert([
                'id' => $rol['id'],
                'uuid' => (string) Str::uuid(),
                'codigo' => $rol['codigo'],
                'nombre_rol' => $rol['nombre_rol'],
                'descripcion' => 'Rol de '.$rol['nombre_rol'],
                'deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Migrate users data
        $users = DB::table('usuarios')->get();
        if ($users->isEmpty()) {
            // Seed a default admin
            $userId = DB::table('usuario')->insertGetId([
                'uuid' => (string) Str::uuid(),
                'nombre_usuario' => 'admin',
                'correo_electronico' => 'admin@admin.com',
                'password_hash' => Hash::make('admin123'),
                'nombres' => 'Administrador',
                'activo' => true,
                'deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('rol_usuario')->insert([
                'uuid' => (string) Str::uuid(),
                'id_rol' => 8, // Administrador
                'id_usuario' => $userId,
                'deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            foreach ($users as $user) {
                // Concatenate names/surnames
                $nombres = trim(($user->primer_nombre ?? '').' '.($user->segundo_nombre ?? ''));
                $apellidos = trim(($user->primer_apellido ?? '').' '.($user->segundo_apellido ?? ''));

                DB::table('usuario')->insert([
                    'id' => $user->id,
                    'uuid' => (string) Str::uuid(),
                    'nombre_usuario' => $user->nombre_usuario ?? ('user_'.$user->id),
                    'correo_electronico' => $user->correo ?? ('user_'.$user->id.'@example.com'),
                    'password_hash' => $user->contrasena ?? '',
                    'nombres' => $nombres !== '' ? $nombres : null,
                    'apellidos' => $apellidos !== '' ? $apellidos : null,
                    'cedula' => $user->cedula,
                    'celular' => $user->celular,
                    'fecha_nac' => $user->fecha_nac,
                    'activo' => true,
                    'deleted' => false,
                    'created_at' => $user->created_at ?? now(),
                    'updated_at' => $user->updated_at ?? now(),
                ]);

                // Assign role in the pivot table
                if ($user->rol) {
                    DB::table('rol_usuario')->insert([
                        'uuid' => (string) Str::uuid(),
                        'id_rol' => $user->rol,
                        'id_usuario' => $user->id,
                        'deleted' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 4. Drop the old tables
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('roles');

        // 5. Recreate foreign keys referencing the new tables
        Schema::table('solicitud', function (Blueprint $table) {
            $table->foreign('solicitante')->references('id')->on('usuario')->onDelete('set null');
            $table->foreign('encargado')->references('id')->on('usuario')->onDelete('set null');
            $table->foreign('departamento_encargado')->references('id')->on('rol')->onDelete('set null');
        });

        // 6. Populate default options and endpoints (Seed)
        $optionsList = [
            'G_SOLICITUDES_PROPIAS' => 'Permite ver, crear y consultar detalles de sus propias solicitudes.',
            'REGISTRAR_USUARIOS' => 'Permite registrar nuevos usuarios.',
            'G_SOLICITUDES_ASIGNADAS' => 'Permite ver, reasignar y actualizar solicitudes asignadas.',
            'PUBLICAR_CONTENIDO' => 'Permite a los publicistas gestionar deportes, eventos, logros y noticias.',
            'APROBAR_SOLICITUDES' => 'Permite aprobar o denegar solicitudes (cambio de estado de flujo).',
        ];

        $insertedOptions = [];
        foreach ($optionsList as $key => $desc) {
            $opcionId = DB::table('opcion')->insertGetId([
                'uuid' => (string) Str::uuid(),
                'nombre_opcion' => $key,
                'descripcion' => $desc,
                'deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $insertedOptions[$key] = $opcionId;
        }

        $endpointsList = [
            // G_SOLICITUDES_PROPIAS
            ['name' => 'Listar solicitudes', 'method' => 'GET', 'url' => 'api/solicitudes', 'opt' => 'G_SOLICITUDES_PROPIAS'],
            ['name' => 'Crear solicitud', 'method' => 'POST', 'url' => 'api/solicitudes', 'opt' => 'G_SOLICITUDES_PROPIAS'],
            ['name' => 'Ver solicitud', 'method' => 'GET', 'url' => 'api/solicitudes/{id}', 'opt' => 'G_SOLICITUDES_PROPIAS'],
            ['name' => 'Actualizar solicitud', 'method' => 'PUT', 'url' => 'api/solicitudes/{id}', 'opt' => 'G_SOLICITUDES_PROPIAS'],
            ['name' => 'Eliminar solicitud', 'method' => 'DELETE', 'url' => 'api/solicitudes/{id}', 'opt' => 'G_SOLICITUDES_PROPIAS'],

            // REGISTRAR_USUARIOS
            ['name' => 'Registrar usuario', 'method' => 'POST', 'url' => 'api/auth/register', 'opt' => 'REGISTRAR_USUARIOS'],

            // G_SOLICITUDES_ASIGNADAS
            ['name' => 'Listar solicitudes asignadas', 'method' => 'GET', 'url' => 'api/solicitudes/asignadas', 'opt' => 'G_SOLICITUDES_ASIGNADAS'],
            ['name' => 'Reasignar solicitud', 'method' => 'PATCH', 'url' => 'api/solicitudes/{id}/reassign', 'opt' => 'G_SOLICITUDES_ASIGNADAS'],

            // APROBAR_SOLICITUDES
            ['name' => 'Aprobar o denegar solicitud', 'method' => 'POST', 'url' => 'api/solicitudes/{id}/procesar', 'opt' => 'APROBAR_SOLICITUDES'],

            // PUBLICAR_CONTENIDO (Write endpoints)
            ['name' => 'Crear deporte', 'method' => 'POST', 'url' => 'api/deportes', 'opt' => 'PUBLICAR_CONTENIDO'],
            ['name' => 'Actualizar deporte', 'method' => 'PUT', 'url' => 'api/deportes/{deporte}', 'opt' => 'PUBLICAR_CONTENIDO'],
            ['name' => 'Eliminar deporte', 'method' => 'DELETE', 'url' => 'api/deportes/{deporte}', 'opt' => 'PUBLICAR_CONTENIDO'],

            ['name' => 'Crear evento', 'method' => 'POST', 'url' => 'api/eventos', 'opt' => 'PUBLICAR_CONTENIDO'],
            ['name' => 'Actualizar evento', 'method' => 'PUT', 'url' => 'api/eventos/{evento}', 'opt' => 'PUBLICAR_CONTENIDO'],
            ['name' => 'Eliminar evento', 'method' => 'DELETE', 'url' => 'api/eventos/{evento}', 'opt' => 'PUBLICAR_CONTENIDO'],

            ['name' => 'Crear logro', 'method' => 'POST', 'url' => 'api/logros', 'opt' => 'PUBLICAR_CONTENIDO'],
            ['name' => 'Actualizar logro', 'method' => 'PUT', 'url' => 'api/logros/{logro}', 'opt' => 'PUBLICAR_CONTENIDO'],
            ['name' => 'Eliminar logro', 'method' => 'DELETE', 'url' => 'api/logros/{logro}', 'opt' => 'PUBLICAR_CONTENIDO'],

            ['name' => 'Crear noticia', 'method' => 'POST', 'url' => 'api/noticias', 'opt' => 'PUBLICAR_CONTENIDO'],
            ['name' => 'Actualizar noticia', 'method' => 'PUT', 'url' => 'api/noticias/{noticia}', 'opt' => 'PUBLICAR_CONTENIDO'],
            ['name' => 'Eliminar noticia', 'method' => 'DELETE', 'url' => 'api/noticias/{noticia}', 'opt' => 'PUBLICAR_CONTENIDO'],
        ];

        foreach ($endpointsList as $end) {
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

            // Link to Option
            $optId = $insertedOptions[$end['opt']];
            DB::table('opcion_endpoint')->insert([
                'uuid' => (string) Str::uuid(),
                'id_opcion' => $optId,
                'id_endpoint' => $endId,
                'deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 7. Associate Options to Roles (rol_opcion)
        // G_SOLICITUDES_PROPIAS to ALL roles
        $allRoles = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        foreach ($allRoles as $roleId) {
            DB::table('rol_opcion')->insert([
                'uuid' => (string) Str::uuid(),
                'id_rol' => $roleId,
                'id_opcion' => $insertedOptions['G_SOLICITUDES_PROPIAS'],
                'deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // REGISTRAR_USUARIOS to Administrador (8)
        DB::table('rol_opcion')->insert([
            'uuid' => (string) Str::uuid(),
            'id_rol' => 8,
            'id_opcion' => $insertedOptions['REGISTRAR_USUARIOS'],
            'deleted' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // G_SOLICITUDES_ASIGNADAS to staff roles: 1, 2, 3, 4, 9
        $staffRoles = [1, 2, 3, 4, 9];
        foreach ($staffRoles as $roleId) {
            DB::table('rol_opcion')->insert([
                'uuid' => (string) Str::uuid(),
                'id_rol' => $roleId,
                'id_opcion' => $insertedOptions['G_SOLICITUDES_ASIGNADAS'],
                'deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // PUBLICAR_CONTENIDO to Publicista (7)
        DB::table('rol_opcion')->insert([
            'uuid' => (string) Str::uuid(),
            'id_rol' => 7,
            'id_opcion' => $insertedOptions['PUBLICAR_CONTENIDO'],
            'deleted' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // APROBAR_SOLICITUDES to staff roles: 1, 2, 3, 4, 8, 9
        $approvalRoles = [1, 2, 3, 4, 8, 9];
        foreach ($approvalRoles as $roleId) {
            DB::table('rol_opcion')->insert([
                'uuid' => (string) Str::uuid(),
                'id_rol' => $roleId,
                'id_opcion' => $insertedOptions['APROBAR_SOLICITUDES'],
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
        // Reversal logic if needed
        Schema::table('solicitud', function (Blueprint $table) {
            $table->dropForeign(['solicitante']);
            $table->dropForeign(['encargado']);
            $table->dropForeign(['departamento_encargado']);
        });

        Schema::dropIfExists('auditoria');
        Schema::dropIfExists('configuracion');
        Schema::dropIfExists('opcion_endpoint');
        Schema::dropIfExists('rol_opcion');
        Schema::dropIfExists('endpoint');
        Schema::dropIfExists('opcion');
        Schema::dropIfExists('rol_usuario');
        Schema::dropIfExists('usuario');
        Schema::dropIfExists('rol');
    }
};
