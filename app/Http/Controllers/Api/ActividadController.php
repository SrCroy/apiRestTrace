<?php

namespace App\Http\Controllers\Api;

use App\Helpers\GpsCalculator;
use App\Http\Controllers\Controller;
use App\Http\Requests\EnviarGpsRequest;
use App\Http\Requests\IniciarActividadRequest;
use App\Models\actividades;
use App\Models\amistades;
use App\Models\puntosGps;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    // ═══════════════════════════════════════════════════════════════
    // 1. POST /api/actividades/iniciar
    // ═══════════════════════════════════════════════════════════════
    public function iniciar(IniciarActividadRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = $request->user();

            // Si se especifica ruta_id, verificar que sea pública o del usuario
            if (!empty($validated['ruta_id'])) {
                $ruta = \App\Models\rutas::find($validated['ruta_id']);
                if ($ruta && $ruta->privacidad === 'privado' && $ruta->usuario_id !== $user->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes acceso a esta ruta'
                    ], 403);
                }
            }

            $actividad = actividades::create([
                'titulo'       => $validated['titulo'],
                'tipo_deporte' => $validated['tipo_deporte'],
                'dificultad'   => $validated['dificultad'],
                'privacidad'   => $validated['privacidad'],
                'ruta_id'      => $validated['ruta_id'] ?? null,
                'id_usuario'   => $user->id,
                'estado'       => 'en_progreso',
                'iniciada_en'  => Carbon::now(),
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'actividad_id' => $actividad->id,
                    'estado'       => $actividad->estado,
                    'iniciada_en'  => $actividad->iniciada_en,
                ]
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar actividad',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 2. POST /api/actividades/{actividadId}/gps
    // ═══════════════════════════════════════════════════════════════
    public function enviarGps(EnviarGpsRequest $request, $actividadId)
    {
        try {
            $user = $request->user();
            $actividad = actividades::where('id', $actividadId)
                ->where('id_usuario', $user->id)
                ->first();

            if (!$actividad) {
                return response()->json([
                    'success' => false,
                    'message' => 'Actividad no encontrada'
                ], 404);
            }

            if ($actividad->estado !== 'en_progreso') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden enviar puntos GPS a actividades en progreso'
                ], 422);
            }

            $validated = $request->validated();
            $puntosData = [];

            foreach ($validated['puntos'] as $punto) {
                $puntosData[] = [
                    'actividad_id'  => $actividad->id,
                    'latitud'       => $punto['latitud'],
                    'longitud'      => $punto['longitud'],
                    'altitud_m'     => $punto['altitud_m'] ?? null,
                    'velocidad_ms'  => $punto['velocidad_ms'] ?? null,
                    'precision_m'   => $punto['precision_m'] ?? null,
                    'secuencia'     => $punto['secuencia'],
                    'registrado_en' => $punto['registrado_en'],
                ];
            }

            puntosGps::insert($puntosData);

            $totalPuntos = puntosGps::where('actividad_id', $actividad->id)->count();

            return response()->json([
                'success'      => true,
                'message'      => count($puntosData) . ' puntos GPS registrados',
                'total_puntos' => $totalPuntos,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar puntos GPS',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 3. POST /api/actividades/{actividadId}/pausar
    // ═══════════════════════════════════════════════════════════════
    public function pausar(Request $request, $actividadId)
    {
        try {
            $user = $request->user();
            $actividad = actividades::where('id', $actividadId)
                ->where('id_usuario', $user->id)
                ->first();

            if (!$actividad) {
                return response()->json([
                    'success' => false,
                    'message' => 'Actividad no encontrada'
                ], 404);
            }

            if ($actividad->estado !== 'en_progreso') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden pausar actividades en progreso'
                ], 422);
            }

            if ($actividad->pausada_en) {
                return response()->json([
                    'success' => false,
                    'message' => 'La actividad ya está pausada'
                ], 422);
            }

            $actividad->update(['pausada_en' => Carbon::now()]);

            return response()->json([
                'success' => true,
                'message' => 'Actividad pausada',
                'estado'  => 'en_progreso',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al pausar actividad',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 4. POST /api/actividades/{actividadId}/reanudar
    // ═══════════════════════════════════════════════════════════════
    public function reanudar(Request $request, $actividadId)
    {
        try {
            $user = $request->user();
            $actividad = actividades::where('id', $actividadId)
                ->where('id_usuario', $user->id)
                ->first();

            if (!$actividad) {
                return response()->json([
                    'success' => false,
                    'message' => 'Actividad no encontrada'
                ], 404);
            }

            if ($actividad->estado !== 'en_progreso') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden reanudar actividades en progreso'
                ], 422);
            }

            if (!$actividad->pausada_en) {
                return response()->json([
                    'success' => false,
                    'message' => 'La actividad no está pausada'
                ], 422);
            }

            // Calcular segundos de pausa y acumular
            $pausaInicio = Carbon::parse($actividad->pausada_en);
            $segundosPausa = Carbon::now()->diffInSeconds($pausaInicio);
            $pausaAcumulada = ($actividad->duracion_pausa_segundos ?? 0) + $segundosPausa;

            $actividad->update([
                'duracion_pausa_segundos' => $pausaAcumulada,
                'pausada_en'              => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Actividad reanudada',
                'estado'  => 'en_progreso',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reanudar actividad',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 5. POST /api/actividades/{actividadId}/finalizar
    // ═══════════════════════════════════════════════════════════════
    public function finalizar(Request $request, $actividadId)
    {
        try {
            $user = $request->user();
            $actividad = actividades::where('id', $actividadId)
                ->where('id_usuario', $user->id)
                ->first();

            if (!$actividad) {
                return response()->json([
                    'success' => false,
                    'message' => 'Actividad no encontrada'
                ], 404);
            }

            if (!in_array($actividad->estado, ['en_progreso'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden finalizar actividades en progreso'
                ], 422);
            }

            // Si está pausada, acumular la pausa pendiente
            if ($actividad->pausada_en) {
                $pausaInicio = Carbon::parse($actividad->pausada_en);
                $segundosPausa = Carbon::now()->diffInSeconds($pausaInicio);
                $actividad->duracion_pausa_segundos = ($actividad->duracion_pausa_segundos ?? 0) + $segundosPausa;
                $actividad->pausada_en = null;
            }

            // Obtener puntos GPS
            $puntos = $actividad->puntosGps()->orderBy('secuencia')->get();

            if ($puntos->count() < 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'La actividad es muy corta. Se necesitan al menos 5 puntos GPS para completar.'
                ], 422);
            }

            // Calcular métricas
            $metricas = GpsCalculator::calcularMetricas(
                $puntos,
                $user->peso_kg,
                $actividad->tipo_deporte,
                $actividad->duracion_pausa_segundos ?? 0
            );

            // Actualizar actividad
            $actividad->update(array_merge($metricas, [
                'estado'       => 'completada',
                'finalizada_en' => Carbon::now(),
            ]));

            // Incrementar veces_usada si se basó en una ruta
            if ($actividad->ruta_id) {
                $actividad->ruta()->increment('veces_usada');
            }

            // TODO: Disparar Observer de logros

            return response()->json([
                'success' => true,
                'message' => 'Actividad completada',
                'data' => [
                    'id'                      => $actividad->id,
                    'titulo'                  => $actividad->titulo,
                    'distancia_km'            => $actividad->distancia_km,
                    'duracion_seg'            => $actividad->duracion_seg,
                    'calorias'                => $actividad->calorias,
                    'desnivel_positivo_m'     => $actividad->desnivel_positivo_m,
                    'desnivel_negativo_m'     => $actividad->desnivel_negativo_m,
                    'ritmo_promedio'          => $actividad->ritmo_promedio,
                    'velocidad_promedio_kmh'  => $actividad->velocidad_promedio_kmh,
                    'estado'                  => $actividad->estado,
                    'finalizada_en'           => $actividad->finalizada_en,
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al finalizar actividad',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 6. POST /api/actividades/{actividadId}/descartar
    // ═══════════════════════════════════════════════════════════════
    public function descartar(Request $request, $actividadId)
    {
        try {
            $user = $request->user();
            $actividad = actividades::where('id', $actividadId)
                ->where('id_usuario', $user->id)
                ->first();

            if (!$actividad) {
                return response()->json([
                    'success' => false,
                    'message' => 'Actividad no encontrada'
                ], 404);
            }

            if ($actividad->estado !== 'en_progreso') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden descartar actividades en progreso'
                ], 422);
            }

            // Eliminar puntos GPS
            $actividad->puntosGps()->delete();

            // Marcar como descartada
            $actividad->update(['estado' => 'descartada']);

            return response()->json([
                'success' => true,
                'message' => 'Actividad descartada',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al descartar actividad',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 7. GET /api/actividades
    // ═══════════════════════════════════════════════════════════════
    public function listar(Request $request)
    {
        try {
            $user = $request->user();
            $query = actividades::where('id_usuario', $user->id)
                ->whereNotIn('estado', ['eliminada', 'descartada']);

            // Filtros
            if ($request->has('tipo_deporte')) {
                $query->where('tipo_deporte', $request->tipo_deporte);
            }
            if ($request->has('estado')) {
                $query->where('estado', $request->estado);
            }
            if ($request->has('privacidad')) {
                $query->where('privacidad', $request->privacidad);
            }

            $total = $query->count();

            // Paginación
            $limit  = $request->input('limit', 10);
            $offset = $request->input('offset', 0);

            $actividades = $query->orderBy('created_at', 'desc')
                ->limit($limit)
                ->offset($offset)
                ->get()
                ->map(function ($act) {
                    return [
                        'id'             => $act->id,
                        'titulo'         => $act->titulo,
                        'tipo_deporte'   => $act->tipo_deporte,
                        'distancia_km'   => $act->distancia_km,
                        'duracion_seg'   => $act->duracion_seg,
                        'calorias'       => $act->calorias,
                        'privacidad'     => $act->privacidad,
                        'estado'         => $act->estado,
                        'finalizada_en'  => $act->finalizada_en,
                    ];
                });

            $pagina = (int) floor($offset / max($limit, 1)) + 1;

            return response()->json([
                'success' => true,
                'data'    => $actividades,
                'total'   => $total,
                'pagina'  => $pagina,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al listar actividades',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 8. GET /api/actividades/{actividadId}
    // ═══════════════════════════════════════════════════════════════
    public function detalle(Request $request, $actividadId)
    {
        try {
            $user = $request->user();
            $actividad = actividades::with(['usuario', 'puntosGps' => function ($q) {
                $q->orderBy('secuencia');
            }])->find($actividadId);

            if (!$actividad || $actividad->estado === 'eliminada') {
                return response()->json([
                    'success' => false,
                    'message' => 'Actividad no encontrada'
                ], 404);
            }

            // Verificar privacidad
            if ($actividad->id_usuario !== $user->id) {
                if ($actividad->privacidad === 'privado') {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes acceso a esta actividad'
                    ], 403);
                }

                if ($actividad->privacidad === 'amigos') {
                    $esAmigo = amistades::where('estado', 'aceptada')
                        ->where(function ($q) use ($user, $actividad) {
                            $q->where(function ($q2) use ($user, $actividad) {
                                $q2->where('solicitante_id', $user->id)
                                   ->where('receptor_id', $actividad->id_usuario);
                            })->orWhere(function ($q2) use ($user, $actividad) {
                                $q2->where('solicitante_id', $actividad->id_usuario)
                                   ->where('receptor_id', $user->id);
                            });
                        })->exists();

                    if (!$esAmigo) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No tienes acceso a esta actividad'
                        ], 403);
                    }
                }
            }

            $puntosGps = $actividad->puntosGps->map(function ($punto) {
                return [
                    'latitud'       => $punto->latitud,
                    'longitud'      => $punto->longitud,
                    'altitud_m'     => $punto->altitud_m,
                    'velocidad_ms'  => $punto->velocidad_ms,
                    'secuencia'     => $punto->secuencia,
                    'registrado_en' => $punto->registrado_en,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'id'                       => $actividad->id,
                    'titulo'                   => $actividad->titulo,
                    'tipo_deporte'             => $actividad->tipo_deporte,
                    'dificultad'               => $actividad->dificultad,
                    'privacidad'               => $actividad->privacidad,
                    'distancia_km'             => $actividad->distancia_km,
                    'duracion_seg'             => $actividad->duracion_seg,
                    'duracion_pausa_segundos'  => $actividad->duracion_pausa_segundos,
                    'calorias'                 => $actividad->calorias,
                    'desnivel_positivo_m'      => $actividad->desnivel_positivo_m,
                    'desnivel_negativo_m'      => $actividad->desnivel_negativo_m,
                    'ritmo_promedio'           => $actividad->ritmo_promedio,
                    'velocidad_promedio_kmh'   => $actividad->velocidad_promedio_kmh,
                    'inicio_lat'               => $actividad->inicio_lat,
                    'inicio_lng'               => $actividad->inicio_lng,
                    'final_lat'                => $actividad->final_lat,
                    'final_lng'                => $actividad->final_lng,
                    'nombre_lugar'             => $actividad->nombre_lugar,
                    'estado'                   => $actividad->estado,
                    'iniciada_en'              => $actividad->iniciada_en,
                    'finalizada_en'            => $actividad->finalizada_en,
                    'usuario' => [
                        'id'       => $actividad->usuario->id,
                        'username' => $actividad->usuario->username,
                        'avatar'   => asset('storage/' . $actividad->usuario->avatar),
                    ],
                    'puntos_gps' => $puntosGps,
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener detalle de actividad',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 9. DELETE /api/actividades/{actividadId}
    // ═══════════════════════════════════════════════════════════════
    public function eliminar(Request $request, $actividadId)
    {
        try {
            $user = $request->user();
            $actividad = actividades::where('id', $actividadId)
                ->where('id_usuario', $user->id)
                ->first();

            if (!$actividad) {
                return response()->json([
                    'success' => false,
                    'message' => 'Actividad no encontrada'
                ], 404);
            }

            if ($actividad->estado === 'eliminada') {
                return response()->json([
                    'success' => false,
                    'message' => 'La actividad ya fue eliminada'
                ], 422);
            }

            $actividad->update(['estado' => 'eliminada']);

            return response()->json([
                'success' => true,
                'message' => 'Actividad eliminada',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar actividad',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
