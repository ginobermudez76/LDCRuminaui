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
        Schema::create('solicitud_estado', function (Blueprint $table) {
            $table->integer('id_estado')->autoIncrement();
            $table->string('estado_nombre', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('solicitud_tipo', function (Blueprint $table) {
            $table->integer('id_tipo')->autoIncrement();
            $table->string('name_tipo', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('external', function (Blueprint $table) {
            $table->integer('id_ext')->autoIncrement();
            $table->string('ext_nombre', 45)->nullable();
            $table->string('ext_snombre', 45)->nullable();
            $table->string('ext_apellido', 45)->nullable();
            $table->string('ext_sapellido', 45)->nullable();
            $table->string('ext_email', 45)->nullable();
            $table->string('ext_celular', 45)->nullable();
            $table->string('cedula', 45)->nullable();
            $table->date('fecha_nac')->nullable();
            $table->timestamps();
        });

        Schema::create('solicitud', function (Blueprint $table) {
            $table->integer('s_id')->autoIncrement();
            $table->dateTime('s_fecha')->nullable();
            $table->string('s_doc', 255)->nullable();
            $table->double('s_valor')->nullable();
            $table->integer('tipo')->nullable();
            $table->integer('solicitante')->nullable();
            $table->integer('encargado')->nullable();
            $table->integer('solicitantext')->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->integer('estado')->nullable();
            $table->integer('departamento_encargado')->nullable();
            $table->timestamps();

            $table->foreign('tipo')->references('id_tipo')->on('solicitud_tipo')->onDelete('set null');
            $table->foreign('solicitante')->references('id')->on('usuarios')->onDelete('set null');
            $table->foreign('encargado')->references('id')->on('usuarios')->onDelete('set null');
            $table->foreign('solicitantext')->references('id_ext')->on('external')->onDelete('set null');
            $table->foreign('estado')->references('id_estado')->on('solicitud_estado')->onDelete('set null');
            $table->foreign('departamento_encargado')->references('id_rol')->on('roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud');
        Schema::dropIfExists('external');
        Schema::dropIfExists('solicitud_tipo');
        Schema::dropIfExists('solicitud_estado');
    }
};
