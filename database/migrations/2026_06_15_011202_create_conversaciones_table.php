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
        Schema::create('conversaciones', function (Blueprint $table) {
            $table->id();
            $table->timestamp('ultimo_mensaje_en')->nullable();
            $table->foreignId('usuario_uno_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('usuario_dos_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('creado_en')->nullable();

            $table->unique(['usuario_uno_id', 'usuario_dos_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversaciones');
    }
};
