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
        Schema::create('historial_solicitud', function (Blueprint $table) {
            $table->id('historial_id');
            $table->integer('solicitud_id');
            $table->timestamp('fecha_asignacion')->useCurrent();
            $table->integer('estado');
            $table->integer('responsable');
            $table->integer('departamento');
            $table->integer('tipo')->nullable();

            $table->foreign('solicitud_id')->references('s_id')->on('solicitud')->onDelete('cascade');
            $table->foreign('responsable')->references('id')->on('usuario')->onDelete('restrict');
            $table->foreign('departamento')->references('id')->on('rol')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_solicitud');
    }
};
