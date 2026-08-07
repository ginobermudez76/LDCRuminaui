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
        if (! Schema::hasTable('cursos')) {
            Schema::create('cursos', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 100)->nullable();
                $table->date('fecha_inicio')->nullable();
                $table->date('fecha_fin')->nullable();
                $table->dateTime('fecha_eliminar')->nullable();
                $table->string('imagen', 1000)->nullable();
                $table->string('descripcion', 300)->nullable();
                $table->unsignedBigInteger('deporte_id')->nullable();
                $table->string('estado', 50)->nullable();
                $table->string('inscripciones', 20)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('documentos')) {
            Schema::create('documentos', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 200);
                $table->string('descripcion', 2000)->nullable();
                $table->string('documento', 2000)->nullable(); // file path
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
        Schema::dropIfExists('cursos');
    }
};
