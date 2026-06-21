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
        // 1. Alter solicitud_tipo table
        Schema::table('solicitud_tipo', function (Blueprint $table) {
            $table->boolean('requiere_documento')->default(false)->after('name_tipo');
            $table->boolean('requiere_valor')->default(false)->after('requiere_documento');
            $table->boolean('requiere_descripcion')->default(true)->after('requiere_valor');
            $table->boolean('activo')->default(true)->after('requiere_descripcion');
        });

        // 2. Create workflow_steps table
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('solicitud_tipo_id');
            $table->integer('orden');
            $table->integer('rol_id');
            $table->string('nombre_paso', 100)->nullable();
            $table->timestamps();

            $table->foreign('solicitud_tipo_id')
                ->references('id_tipo')
                ->on('solicitud_tipo')
                ->onDelete('cascade');

            $table->foreign('rol_id')
                ->references('id')
                ->on('rol')
                ->onDelete('cascade');
        });

        // 3. Alter solicitud table to add current_step_id
        Schema::table('solicitud', function (Blueprint $table) {
            $table->integer('current_step_id')->nullable()->after('departamento_encargado');
            $table->foreign('current_step_id')
                ->references('id')
                ->on('workflow_steps')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud', function (Blueprint $table) {
            if (Schema::hasColumn('solicitud', 'current_step_id')) {
                $table->dropForeign(['current_step_id']);
                $table->dropColumn('current_step_id');
            }
        });

        Schema::dropIfExists('workflow_steps');

        Schema::table('solicitud_tipo', function (Blueprint $table) {
            $table->dropColumn(['requiere_documento', 'requiere_valor', 'requiere_descripcion', 'activo']);
        });
    }
};
