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
        Schema::create('publicacion_archivos', function (Blueprint $table) {
            $table->id();
            $table->string('url', 500);
            $table->enum('tipo', ['foto', 'video']);
            $table->integer('orden')->default(0);
            $table->foreignId('publicacion_id')->constrained('publicaciones')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicacion_archivos');
    }
};
