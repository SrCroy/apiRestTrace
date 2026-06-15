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
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->string('reportable_tipo', 100);
            $table->unsignedBigInteger('reportable_id');
            $table->string('motivo', 255);
            $table->text('detalles')->nullable();
            $table->enum('estado', ['pendiente', 'revisado', 'descartado'])->default('pendiente');
            $table->timestamp('revisado_en')->nullable();
            $table->foreignId('reportador_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('revisado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['reportable_tipo', 'reportable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
