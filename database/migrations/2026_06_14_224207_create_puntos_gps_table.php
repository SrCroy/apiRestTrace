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
        Schema::create('puntos_gps', function (Blueprint $table) {
            $table->id();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->decimal('altitud_m', 8, 2)->nullable();
            $table->decimal('velocidad_ms', 8, 2)->nullable();
            $table->decimal('precision_m', 8, 2)->nullable();
            $table->integer('secuencia');
            $table->timestamp('registrado_en');
            $table->foreignId('actividad_id')->constrained('actividades')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puntos_gps');
    }
};
