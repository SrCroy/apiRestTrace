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
        Schema::create('reacciones', function (Blueprint $table) {
            $table->id();
            $table->string('reaccionable_tipo', 100);
            $table->unsignedBigInteger('reaccionable_id');
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('creado_en')->nullable();

            $table->unique(['usuario_id', 'reaccionable_tipo', 'reaccionable_id']);
            $table->index(['reaccionable_tipo', 'reaccionable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reacciones');
    }
};
