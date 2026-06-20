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
        if (!Schema::hasTable('deportes')) {
            Schema::create('deportes', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('nombre', 100);
                $table->string('descripcion', 300)->nullable();
                $table->string('imagen', 1000)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('deportistas_destacados')) {
            Schema::create('deportistas_destacados', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('nombre_deportista', 200);
                $table->integer('deporte_id')->nullable();
                $table->string('imagen', 1000)->nullable();
                $table->timestamps();

                $table->foreign('deporte_id')->references('id')->on('deportes')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('eventos')) {
            Schema::create('eventos', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('nombre', 100)->nullable();
                $table->date('fecha_inicio')->nullable();
                $table->date('fecha_fin')->nullable();
                $table->dateTime('fecha_eliminar')->nullable();
                $table->string('imagen', 1000)->nullable();
                $table->string('descripcion', 300)->nullable();
                $table->integer('deporte_id')->nullable();
                $table->string('estado', 50)->nullable();
                $table->string('inscripciones', 20)->nullable();
                $table->timestamps();

                $table->foreign('deporte_id')->references('id')->on('deportes')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('inscripciones_eventos')) {
            Schema::create('inscripciones_eventos', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->integer('evento_id')->nullable();
                $table->string('nombre', 50)->nullable();
                $table->string('ciudad', 50)->nullable();
                $table->string('imagen', 255)->nullable();
                $table->timestamps();

                $table->foreign('evento_id')->references('id')->on('eventos')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('logros')) {
            Schema::create('logros', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('titulo', 200);
                $table->integer('deporte_id')->nullable();
                $table->string('imagen', 1000)->nullable();
                $table->timestamps();

                $table->foreign('deporte_id')->references('id')->on('deportes')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('noticias')) {
            Schema::create('noticias', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('titulo', 100)->nullable();
                $table->string('imagen', 1000)->nullable();
                $table->string('cuerpo', 2000)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('galeria_imagenes')) {
            Schema::create('galeria_imagenes', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('tipo', 50);
                $table->integer('id_tipo');
                $table->string('nombre', 100);
                $table->string('ruta_imagenes', 255)->nullable();
                $table->string('ruta_carpeta', 255)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('carta_condolencias')) {
            Schema::create('carta_condolencias', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('mensaje', 700);
                $table->string('imagen', 1000)->nullable();
                $table->dateTime('fecha_eliminar');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carta_condolencias');
        Schema::dropIfExists('galeria_imagenes');
        Schema::dropIfExists('noticias');
        Schema::dropIfExists('logros');
        Schema::dropIfExists('inscripciones_eventos');
        Schema::dropIfExists('eventos');
        Schema::dropIfExists('deportistas_destacados');
        Schema::dropIfExists('deportes');
    }
};
