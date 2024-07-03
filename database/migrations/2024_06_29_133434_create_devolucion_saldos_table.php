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
        Schema::create('devolucion_saldos', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha_devolucion')->nullable();
            $table->text('detalle_devolucion')->nullable();
            $table->decimal('monto', 10, 2)->nullable();
            $table->integer('causa_id')->comment('id de la tabla causa');
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
        Schema::dropIfExists('devolucion_saldos');
    }
};
