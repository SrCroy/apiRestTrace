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
        Schema::create('rutas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 500);
            $table->string('descripcion', 100)->nullable();
            $table->decimal('distancia_km', 8, 2)->nullable();
            $table->decimal('desnivel_positivo_m', 8, 2)->nullable();
            $table->integer('dificultad')->default(1);
            $table->enum('privacidad', ['publico', 'amigos', 'privado'])->default('publico');
            $table->enum('tipo_deporte', [
                'carrera',
                'caminata',
                'ciclismo',
                'natacion',
                'senderismo',
                'montanismo',
                'otro'
            ])->default('caminata');
            $table->integer('veces_usada')->default(0);
            $table->string('miniatura', 500)->nullable();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rutas');
    }
};
