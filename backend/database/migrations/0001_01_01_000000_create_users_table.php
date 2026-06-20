<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->integer('id_rol')->autoIncrement();
                $table->string('rol_name', 45)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('usuarios')) {
            Schema::create('usuarios', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('primer_nombre', 45)->nullable();
                $table->string('segundo_nombre', 45)->nullable();
                $table->string('primer_apellido', 45)->nullable();
                $table->string('segundo_apellido', 45)->nullable();
                $table->string('cedula', 10)->nullable();
                $table->string('celular', 10)->nullable();
                $table->string('correo', 100)->nullable();
                $table->string('nombre_usuario', 150)->nullable();
                $table->string('contrasena', 1000)->nullable();
                $table->integer('rol')->nullable();
                $table->date('fecha_nac')->nullable();
                $table->rememberToken();
                $table->timestamps();

                $table->foreign('rol')->references('id_rol')->on('roles')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->integer('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('roles');
    }
};
