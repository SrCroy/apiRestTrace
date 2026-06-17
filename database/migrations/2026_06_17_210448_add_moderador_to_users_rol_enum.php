<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Agrega 'moderador' al CHECK constraint de la columna 'rol' en users.
     */
    public function up(): void
    {
        // Eliminar el CHECK constraint actual
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_rol_check');

        // Crear el nuevo CHECK constraint con 'moderador' incluido
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_rol_check CHECK (rol::text = ANY (ARRAY['usuario'::text, 'admin'::text, 'moderador'::text]))");
    }

    /**
     * Revertir: quitar 'moderador' del constraint.
     */
    public function down(): void
    {
        // Primero cambiar cualquier 'moderador' a 'usuario' para no violar el constraint
        DB::statement("UPDATE users SET rol = 'usuario' WHERE rol = 'moderador'");

        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_rol_check');
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_rol_check CHECK (rol::text = ANY (ARRAY['usuario'::text, 'admin'::text]))");
    }
};
