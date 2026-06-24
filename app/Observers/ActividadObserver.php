<?php

namespace App\Observers;

use App\Models\actividades;
use App\Models\logros;
use App\Models\usuarios_logros;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActividadObserver
{
    /**
     * Se dispara después de actualizar una actividad.
     * Evalúa logros globales cuando la actividad pasa a estado 'completada'.
     */
    public function updated(actividades $actividad): void
    {
        // Solo evaluar cuando el estado cambió a 'completada'
        if (!$actividad->wasChanged('estado') || $actividad->estado !== 'completada') {
            return;
        }

        $userId = $actividad->id_usuario;

        // Obtener IDs de logros que el usuario ya tiene
        $logrosObtenidosIds = usuarios_logros::where('usuario_id', $userId)
            ->pluck('logro_id')
            ->toArray();

        // Obtener logros pendientes (que aún no tiene)
        $logrosPendientes = logros::whereNotIn('id', $logrosObtenidosIds)->get();

        if ($logrosPendientes->isEmpty()) {
            return;
        }

        $ahora = Carbon::now();
        $nuevosLogros = [];

        foreach ($logrosPendientes as $logro) {
            if ($this->evaluar($logro, $actividad, $userId)) {
                $nuevosLogros[] = [
                    'usuario_id' => $userId,
                    'logro_id'   => $logro->id,
                    'obtenido_en' => $ahora,
                ];
            }
        }

        // Insertar todos los logros nuevos de una vez
        if (!empty($nuevosLogros)) {
            usuarios_logros::insert($nuevosLogros);
        }
    }

    /**
     * Evalúa si un logro se cumple para el usuario dado.
     */
    private function evaluar(logros $logro, actividades $actividad, int $userId): bool
    {
        // Si el logro tiene tipo_deporte, verificar que coincida con la actividad
        // (para logros de distancia_unica) o filtrar las actividades del usuario
        $tipoDeporte = $logro->tipo_deporte;

        return match ($logro->tipo_disparador) {
            'conteo_actividades' => $this->evaluarConteoActividades($logro, $userId, $tipoDeporte),
            'distancia_total'    => $this->evaluarDistanciaTotal($logro, $userId, $tipoDeporte),
            'distancia_unica'    => $this->evaluarDistanciaUnica($logro, $actividad, $tipoDeporte),
            'dias_racha'         => $this->evaluarDiasRacha($logro, $userId),
            'desnivel_total'     => $this->evaluarDesnivelTotal($logro, $userId, $tipoDeporte),
            default              => false,
        };
    }

    /**
     * conteo_actividades: ¿El usuario tiene N o más actividades completadas?
     */
    private function evaluarConteoActividades(logros $logro, int $userId, ?string $tipoDeporte): bool
    {
        $query = actividades::where('id_usuario', $userId)
            ->where('estado', 'completada');

        if ($tipoDeporte) {
            $query->where('tipo_deporte', $tipoDeporte);
        }

        return $query->count() >= $logro->valor_disparador;
    }

    /**
     * distancia_total: ¿La suma de km de todas las actividades completadas alcanza N?
     */
    private function evaluarDistanciaTotal(logros $logro, int $userId, ?string $tipoDeporte): bool
    {
        $query = actividades::where('id_usuario', $userId)
            ->where('estado', 'completada');

        if ($tipoDeporte) {
            $query->where('tipo_deporte', $tipoDeporte);
        }

        $totalKm = $query->sum('distancia_km');

        return $totalKm >= $logro->valor_disparador;
    }

    /**
     * distancia_unica: ¿La actividad recién completada alcanza N km?
     */
    private function evaluarDistanciaUnica(logros $logro, actividades $actividad, ?string $tipoDeporte): bool
    {
        // Si el logro es específico de un deporte, la actividad debe coincidir
        if ($tipoDeporte && $actividad->tipo_deporte !== $tipoDeporte) {
            return false;
        }

        return ($actividad->distancia_km ?? 0) >= $logro->valor_disparador;
    }

    /**
     * dias_racha: ¿El usuario tiene N días consecutivos con al menos 1 actividad?
     * Calcula la racha actual hacia atrás desde hoy.
     */
    private function evaluarDiasRacha(logros $logro, int $userId): bool
    {
        // Obtener fechas únicas de actividades completadas, ordenadas descendente
        $fechas = actividades::where('id_usuario', $userId)
            ->where('estado', 'completada')
            ->whereNotNull('finalizada_en')
            ->select(DB::raw('DATE(finalizada_en) as fecha'))
            ->distinct()
            ->orderBy('fecha', 'desc')
            ->pluck('fecha')
            ->toArray();

        if (empty($fechas)) {
            return false;
        }

        // Calcular racha desde hoy
        $racha = 1;
        $hoy = Carbon::today()->toDateString();

        // Si la fecha más reciente no es hoy, la racha actual es 0
        if ($fechas[0] !== $hoy) {
            return false;
        }

        for ($i = 1; $i < count($fechas); $i++) {
            $fechaActual = Carbon::parse($fechas[$i - 1]);
            $fechaAnterior = Carbon::parse($fechas[$i]);

            if ($fechaActual->diffInDays($fechaAnterior) === 1) {
                $racha++;
            } else {
                break; // Se rompió la racha
            }
        }

        return $racha >= $logro->valor_disparador;
    }

    /**
     * desnivel_total: ¿La suma de desnivel positivo de todas las actividades alcanza N?
     */
    private function evaluarDesnivelTotal(logros $logro, int $userId, ?string $tipoDeporte): bool
    {
        $query = actividades::where('id_usuario', $userId)
            ->where('estado', 'completada');

        if ($tipoDeporte) {
            $query->where('tipo_deporte', $tipoDeporte);
        }

        $totalDesnivel = $query->sum('desnivel_positivo_m');

        return $totalDesnivel >= $logro->valor_disparador;
    }
}
