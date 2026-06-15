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
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id();
            $table->string('comentable_tipo', 100);
            $table->unsignedBigInteger('comentable_id');
            $table->text('cuerpo');
            $table->enum('estado', ['activo', 'eliminado'])->default('activo');
            $table->foreignId('padre_id')->nullable()->constrained('comentarios')->nullOnDelete();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['comentable_tipo', 'comentable_id']);
            $table->index('padre_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};
