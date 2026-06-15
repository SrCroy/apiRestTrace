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
        Schema::create('logros', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 100)->unique();
            $table->string('nombre', 150);
            $table->string('descripcion', 255)->nullable();
            $table->string('icono')->nullable();
            $table->enum('tipo_disparador', [
                'distancia_total',
                'distancia_unica',
                'dias_racha',
                'conteo_actividades',
                'desnivel_total'
            ])->nullable();
            $table->decimal('valor_disparador', 10, 2)->nullable();
            $table->enum('tipo_deporte', [
                'carrera',
                'caminata',
                'ciclismo',
                'natacion',
                'senderismo',
                'montanismo',
                'otro'
            ])->nullable();
            $table->timestamp('creado_en')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logros');
    }
};
