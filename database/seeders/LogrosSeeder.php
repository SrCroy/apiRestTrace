<?php

namespace Database\Seeders;

use App\Models\logros;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LogrosSeeder extends Seeder
{
    /**
     * Catálogo de logros globales predefinidos.
     *
     * Cada logro se identifica por su 'clave' (única).
     * Si la clave ya existe, no se duplica (upsert por clave).
     */
    public function run(): void
    {
        $ahora = Carbon::now();

        $logros = [
            // ─── Conteo de actividades (genéricos) ───────────
            [
                'clave'            => 'primera_actividad',
                'nombre'           => 'Primera Actividad',
                'descripcion'      => 'Completa tu primera actividad deportiva',
                'icono'            => '🎯',
                'tipo_disparador'  => 'conteo_actividades',
                'valor_disparador' => 1,
                'tipo_deporte'     => null,
                'creado_en'        => $ahora,
            ],
            [
                'clave'            => '10_actividades',
                'nombre'           => 'Deportista Constante',
                'descripcion'      => 'Completa 10 actividades deportivas',
                'icono'            => '💪',
                'tipo_disparador'  => 'conteo_actividades',
                'valor_disparador' => 10,
                'tipo_deporte'     => null,
                'creado_en'        => $ahora,
            ],
            [
                'clave'            => '50_actividades',
                'nombre'           => 'Medio Centenario',
                'descripcion'      => 'Completa 50 actividades deportivas',
                'icono'            => '🔥',
                'tipo_disparador'  => 'conteo_actividades',
                'valor_disparador' => 50,
                'tipo_deporte'     => null,
                'creado_en'        => $ahora,
            ],
            [
                'clave'            => '100_actividades',
                'nombre'           => 'Centenario',
                'descripcion'      => 'Completa 100 actividades deportivas',
                'icono'            => '👑',
                'tipo_disparador'  => 'conteo_actividades',
                'valor_disparador' => 100,
                'tipo_deporte'     => null,
                'creado_en'        => $ahora,
            ],

            // ─── Conteo de actividades (por deporte) ─────────
            [
                'clave'            => 'primera_carrera',
                'nombre'           => 'Primera Carrera',
                'descripcion'      => 'Completa tu primera carrera',
                'icono'            => '🏃',
                'tipo_disparador'  => 'conteo_actividades',
                'valor_disparador' => 1,
                'tipo_deporte'     => 'carrera',
                'creado_en'        => $ahora,
            ],
            [
                'clave'            => 'primer_ciclismo',
                'nombre'           => 'Primer Pedaleo',
                'descripcion'      => 'Completa tu primera actividad de ciclismo',
                'icono'            => '🚴',
                'tipo_disparador'  => 'conteo_actividades',
                'valor_disparador' => 1,
                'tipo_deporte'     => 'ciclismo',
                'creado_en'        => $ahora,
            ],

            // ─── Distancia total acumulada ───────────────────
            [
                'clave'            => 'primer_km',
                'nombre'           => 'Primer Kilómetro',
                'descripcion'      => 'Acumula tu primer kilómetro recorrido',
                'icono'            => '📏',
                'tipo_disparador'  => 'distancia_total',
                'valor_disparador' => 1,
                'tipo_deporte'     => null,
                'creado_en'        => $ahora,
            ],
            [
                'clave'            => '10_km_total',
                'nombre'           => '10K Acumulados',
                'descripcion'      => 'Acumula 10 kilómetros recorridos en total',
                'icono'            => '🛤️',
                'tipo_disparador'  => 'distancia_total',
                'valor_disparador' => 10,
                'tipo_deporte'     => null,
                'creado_en'        => $ahora,
            ],
            [
                'clave'            => '50_km_total',
                'nombre'           => '50K Acumulados',
                'descripcion'      => 'Acumula 50 kilómetros recorridos en total',
                'icono'            => '🗺️',
                'tipo_disparador'  => 'distancia_total',
                'valor_disparador' => 50,
                'tipo_deporte'     => null,
                'creado_en'        => $ahora,
            ],
            [
                'clave'            => '100_km_total',
                'nombre'           => 'Cien Kilómetros',
                'descripcion'      => 'Acumula 100 kilómetros recorridos en total',
                'icono'            => '🏅',
                'tipo_disparador'  => 'distancia_total',
                'valor_disparador' => 100,
                'tipo_deporte'     => null,
                'creado_en'        => $ahora,
            ],
            [
                'clave'            => '500_km_total',
                'nombre'           => 'Medio Millar',
                'descripcion'      => 'Acumula 500 kilómetros recorridos en total',
                'icono'            => '🌍',
                'tipo_disparador'  => 'distancia_total',
                'valor_disparador' => 500,
                'tipo_deporte'     => null,
                'creado_en'        => $ahora,
            ],
            [
                'clave'            => '1000_km_total',
                'nombre'           => 'Mil Kilómetros',
                'descripcion'      => 'Acumula 1,000 kilómetros recorridos en total',
                'icono'            => '🚀',
                'tipo_disparador'  => 'distancia_total',
                'valor_disparador' => 1000,
                'tipo_deporte'     => null,
                'creado_en'        => $ahora,
            ],

            // ─── Distancia en una sola actividad ─────────────
            [
                'clave'            => '5_km_unica',
                'nombre'           => '5K de un Tirón',
                'descripcion'      => 'Recorre 5 kilómetros en una sola actividad',
                'icono'            => '⚡',
                'tipo_disparador'  => 'distancia_unica',
                'valor_disparador' => 5,
                'tipo_deporte'     => null,
                'creado_en'        => $ahora,
            ],
            [
                'clave'            => '10_km_unica',
                'nombre'           => '10K de un Tirón',
                'descripcion'      => 'Recorre 10 kilómetros en una sola actividad',
                'icono'            => '💨',
                'tipo_disparador'  => 'distancia_unica',
                'valor_disparador' => 10,
                'tipo_deporte'     => null,
                'creado_en'        => $ahora,
            ],
            [
                'clave'            => '21_km_unica',
                'nombre'           => 'Media Maratón',
                'descripcion'      => 'Completa una media maratón (21.1 km) corriendo',
                'icono'            => '🏃‍♂️',
                'tipo_disparador'  => 'distancia_unica',
                'valor_disparador' => 21.10,
                'tipo_deporte'     => 'carrera',
                'creado_en'        => $ahora,
            ],
            [
                'clave'            => '42_km_unica',
                'nombre'           => 'Maratón Completa',
                'descripcion'      => 'Completa una maratón (42.2 km) corriendo',
                'icono'            => '🏆',
                'tipo_disparador'  => 'distancia_unica',
                'valor_disparador' => 42.20,
                'tipo_deporte'     => 'carrera',
                'creado_en'        => $ahora,
            ],
            [
                'clave'            => '100_km_unica',
                'nombre'           => 'Ultra Trail',
                'descripcion'      => 'Recorre 100 kilómetros en una sola actividad',
                'icono'            => '🦸',
                'tipo_disparador'  => 'distancia_unica',
                'valor_disparador' => 100,
                'tipo_deporte'     => null,
                'creado_en'        => $ahora,
            ],

            // ─── Racha de días consecutivos ──────────────────
            [
                'clave'            => 'racha_3_dias',
                'nombre'           => 'Racha de 3 Días',
                'descripcion'      => 'Realiza actividades 3 días consecutivos',
                'icono'            => '📆',
                'tipo_disparador'  => 'dias_racha',
                'valor_disparador' => 3,
                'tipo_deporte'     => null,
                'creado_en'        => $ahora,
            ],
            [
                'clave'            => 'racha_7_dias',
                'nombre'           => 'Semana Perfecta',
                'descripcion'      => 'Realiza actividades 7 días consecutivos',
                'icono'            => '🌟',
                'tipo_disparador'  => 'dias_racha',
                'valor_disparador' => 7,
                'tipo_deporte'     => null,
                'creado_en'        => $ahora,
            ],
            [
                'clave'            => 'racha_30_dias',
                'nombre'           => 'Mes Imparable',
                'descripcion'      => 'Realiza actividades 30 días consecutivos',
                'icono'            => '🔱',
                'tipo_disparador'  => 'dias_racha',
                'valor_disparador' => 30,
                'tipo_deporte'     => null,
                'creado_en'        => $ahora,
            ],

            // ─── Desnivel positivo total ─────────────────────
            [
                'clave'            => '500_m_desnivel',
                'nombre'           => 'Medio Kilómetro Vertical',
                'descripcion'      => 'Acumula 500 metros de desnivel positivo en total',
                'icono'            => '⛰️',
                'tipo_disparador'  => 'desnivel_total',
                'valor_disparador' => 500,
                'tipo_deporte'     => null,
                'creado_en'        => $ahora,
            ],
            [
                'clave'            => '1000_m_desnivel',
                'nombre'           => 'Kilómetro Vertical',
                'descripcion'      => 'Acumula 1,000 metros de desnivel positivo en total',
                'icono'            => '🏔️',
                'tipo_disparador'  => 'desnivel_total',
                'valor_disparador' => 1000,
                'tipo_deporte'     => null,
                'creado_en'        => $ahora,
            ],
        ];

        foreach ($logros as $logro) {
            logros::firstOrCreate(
                ['clave' => $logro['clave']],
                $logro
            );
        }
    }
}
