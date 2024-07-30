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
        Schema::create('tabla_configs', function (Blueprint $table) {
            $table->id();
            $table->decimal('caja_contador', 10, 2)->nullable();
            $table->decimal('deuda_extarna', 10, 2)->nullable();
            $table->decimal('ganancia_procesal_procuraduria', 10, 2)->nullable()->comment('es el sumado de ganacias de procuraduria y procesal');
            $table->text('imagen_index')->nullable()->comment('imagen de index de la pagina');
            $table->string('doc_aranceles',400)->nullable()->comment('nombre del archivo de aranceles de abogado');
            $table->string('doc_normas',400)->nullable()->comment('nombre del archivo de normas');
            $table->string('estado', 20)->comment('estado ACTIVO,INACTIVO');
            $table->integer('es_eliminado')->comment('1 es eliminado, 0 no es eliminado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabla_configs');
    }
};
