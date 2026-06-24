<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ─── Tabla rutas_puntos_gps ──────────────────────────────
        Schema::create('rutas_puntos_gps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruta_id')->constrained('rutas')->cascadeOnDelete();
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->decimal('altitud_m', 8, 2)->nullable();
            $table->integer('secuencia');
        });

        // ─── Agregar campo pausada_en a actividades ──────────────
        Schema::table('actividades', function (Blueprint $table) {
            $table->timestamp('pausada_en')->nullable();
        });

        // ─── Agregar 'eliminada' al enum estado (PostgreSQL CHECK constraint) ───
        DB::statement("ALTER TABLE actividades DROP CONSTRAINT actividades_estado_check");
        DB::statement("ALTER TABLE actividades ADD CONSTRAINT actividades_estado_check CHECK (((estado)::text = ANY ((ARRAY['en_progreso'::character varying, 'completada'::character varying, 'descartada'::character varying, 'eliminada'::character varying])::text[])))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rutas_puntos_gps');

        Schema::table('actividades', function (Blueprint $table) {
            $table->dropColumn('pausada_en');
        });

        DB::statement("ALTER TABLE actividades DROP CONSTRAINT IF EXISTS actividades_estado_check");
        DB::statement("ALTER TABLE actividades ADD CONSTRAINT actividades_estado_check CHECK (((estado)::text = ANY ((ARRAY['en_progreso'::character varying, 'completada'::character varying, 'descartada'::character varying])::text[])))");
    }
};
