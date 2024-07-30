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
        Schema::create('paquetes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200)->comment('nombre del paquete');
            $table->decimal('precio',10,2)->nullable()->comment('monto de costo del paquete');
            $table->integer('cantidad_mes')->nullable()->comment('cantidad de meses de duracion del paquete');
            $table->integer('cantidad_causas')->nullable()->comment('cantidad de causas que puede almacenar este paquete');
            $table->text('descripcion')->nullable()->comment('descripcion del paquete');
            $table->timestamp('fecha_creacion')->nullable()->comment('fecha y hora que se creo el paquete');
            $table->integer('usuario_id')->comment('id del usuario que creo el paquete');
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
        Schema::dropIfExists('paquetes');
    }
};
