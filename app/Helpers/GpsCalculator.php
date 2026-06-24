<?php

namespace App\Helpers;

class GpsCalculator
{
    /**
     * Factores de calorías por tipo de deporte
     */
    private const FACTORES_CALORIAS = [
        'carrera'     => 1.0,
        'caminata'    => 0.6,
        'ciclismo'    => 0.8,
        'natacion'    => 1.2,
        'senderismo'  => 1.1,
        'montanismo'  => 1.1,
        'otro'        => 0.8,
    ];

    /**
     * Calcula la distancia entre dos puntos GPS usando la fórmula de Haversine
     *
     * @param float $lat1 Latitud punto 1
     * @param float $lng1 Longitud punto 1
     * @param float $lat2 Latitud punto 2
     * @param float $lng2 Longitud punto 2
     * @return float Distancia en kilómetros
     */
    public static function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $radioTierra = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * asin(sqrt($a));

        return $radioTierra * $c;
    }

    /**
     * Calcula todas las métricas de una actividad a partir de sus puntos GPS
     *
     * @param \Illuminate\Support\Collection $puntos Colección de puntos GPS ordenados por secuencia
     * @param float|null $pesoUsuario Peso del usuario en kg (null = 70 kg default)
     * @param string $tipoDeporte Tipo de deporte
     * @param float $pausaSegundos Segundos totales de pausa acumulados
     * @return array Métricas calculadas
     */
    public static function calcularMetricas($puntos, ?float $pesoUsuario, string $tipoDeporte, float $pausaSegundos = 0): array
    {
        $distanciaTotal = 0;
        $desnivelPositivo = 0;
        $desnivelNegativo = 0;
        $velocidadMaxima = 0;

        $puntosArray = $puntos->sortBy('secuencia')->values();
        $totalPuntos = $puntosArray->count();

        if ($totalPuntos < 2) {
            return [
                'distancia_km'          => 0,
                'duracion_seg'          => 0,
                'calorias'              => 0,
                'desnivel_positivo_m'   => 0,
                'desnivel_negativo_m'   => 0,
                'ritmo_promedio'        => 0,
                'velocidad_promedio_kmh' => 0,
                'velocidad_maxima_kmh'  => 0,
                'inicio_lat'            => $puntosArray->first()->latitud ?? null,
                'inicio_lng'            => $puntosArray->first()->longitud ?? null,
                'final_lat'             => $puntosArray->last()->latitud ?? null,
                'final_lng'             => $puntosArray->last()->longitud ?? null,
            ];
        }

        // Recorrer puntos consecutivos
        for ($i = 1; $i < $totalPuntos; $i++) {
            $anterior = $puntosArray[$i - 1];
            $actual   = $puntosArray[$i];

            // Distancia Haversine
            $distanciaTotal += self::haversine(
                (float) $anterior->latitud,
                (float) $anterior->longitud,
                (float) $actual->latitud,
                (float) $actual->longitud
            );

            // Desnivel
            if ($actual->altitud_m !== null && $anterior->altitud_m !== null) {
                $diferencia = (float) $actual->altitud_m - (float) $anterior->altitud_m;
                if ($diferencia > 0) {
                    $desnivelPositivo += $diferencia;
                } else {
                    $desnivelNegativo += abs($diferencia);
                }
            }

            // Velocidad máxima (convertir m/s a km/h)
            if ($actual->velocidad_ms !== null) {
                $velKmh = (float) $actual->velocidad_ms * 3.6;
                if ($velKmh > $velocidadMaxima) {
                    $velocidadMaxima = $velKmh;
                }
            }
        }

        // Duración: diferencia entre primer y último punto menos pausas
        $primerPunto = $puntosArray->first();
        $ultimoPunto = $puntosArray->last();

        $tiempoTotal = 0;
        if ($primerPunto->registrado_en && $ultimoPunto->registrado_en) {
            $inicio = \Carbon\Carbon::parse($primerPunto->registrado_en);
            $fin    = \Carbon\Carbon::parse($ultimoPunto->registrado_en);
            $tiempoTotal = max(0, $fin->diffInSeconds($inicio) - $pausaSegundos);
        }

        // Calorías
        $peso   = $pesoUsuario ?? 70;
        $factor = self::FACTORES_CALORIAS[$tipoDeporte] ?? 0.8;
        $calorias = $peso * $distanciaTotal * $factor;

        // Ritmo promedio (min/km)
        $ritmoPromedio = 0;
        if ($distanciaTotal > 0 && $tiempoTotal > 0) {
            $ritmoPromedio = ($tiempoTotal / 60) / $distanciaTotal;
        }

        // Velocidad promedio (km/h)
        $velocidadPromedio = 0;
        if ($tiempoTotal > 0) {
            $velocidadPromedio = $distanciaTotal / ($tiempoTotal / 3600);
        }

        return [
            'distancia_km'           => round($distanciaTotal, 2),
            'duracion_seg'           => round($tiempoTotal, 2),
            'calorias'               => round($calorias, 2),
            'desnivel_positivo_m'    => round($desnivelPositivo, 2),
            'desnivel_negativo_m'    => round($desnivelNegativo, 2),
            'ritmo_promedio'         => round($ritmoPromedio, 2),
            'velocidad_promedio_kmh' => round($velocidadPromedio, 2),
            'velocidad_maxima_kmh'   => round($velocidadMaxima, 2),
            'inicio_lat'             => $primerPunto->latitud,
            'inicio_lng'             => $primerPunto->longitud,
            'final_lat'              => $ultimoPunto->latitud,
            'final_lng'              => $ultimoPunto->longitud,
        ];
    }
}
