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
        Schema::create('actividades', function (Blueprint $table){
            $table->id();
            $table->string('titulo', 500);
            $table->enum('tipo_deporte', [
                'carrera',
                'caminata',
                'ciclismo',
                'natacion',
                'senderismo',
                'montanismo',
                'otro'
            ])->default('caminata');
            $table->integer('dificultad')->default(1);
            $table->enum('privacidad', ['publico', 'amigos', 'privado'])->default('publico');
            $table->decimal('distancia_km', 8, 2)->nullable();
            $table->decimal('duracion_seg', 10, 2)->nullable();
            $table->decimal('duracion_pausa_segundos', 10, 2)->nullable();
            $table->decimal('calorias', 8, 2)->nullable();
            $table->decimal('desnivel_positivo_m', 8, 2)->nullable();
            $table->decimal('desnivel_negativo_m', 8, 2)->nullable();
            $table->decimal('ritmo_promedio', 8, 2)->nullable();
            $table->decimal('ritmo_maximo', 8, 2)->nullable();
            $table->decimal('velocidad_promedio_kmh', 8, 2)->nullable();
            $table->decimal('velocidad_maxima_kmh', 8, 2)->nullable();
            $table->decimal('inicio_lat', 10, 7)->nullable();
            $table->decimal('inicio_lng', 10, 7)->nullable();
            $table->decimal('final_lat', 10, 7)->nullable();
            $table->decimal('final_lng', 10, 7)->nullable();
            $table->string('nombre_lugar', 100)->nullable();
            $table->enum('estado', ['en_progreso', 'completada', 'descartada'])->default('en_progreso');
            $table->timestamp('iniciada_en')->nullable();
            $table->timestamp('finalizada_en')->nullable();
            $table->foreignId('ruta_id')->nullable()->constrained('rutas')->nullOnDelete();
            $table->foreignId('id_usuario')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
