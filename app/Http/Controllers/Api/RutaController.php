<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActualizarRutaRequest;
use App\Http\Requests\CrearRutaRequest;
use App\Models\amistades;
use App\Models\rutas;
use App\Models\RutaPuntoGps;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RutaController extends Controller
{
    // ═══════════════════════════════════════════════════════════════
    // 1. POST /api/rutas
    // ═══════════════════════════════════════════════════════════════
    public function crear(CrearRutaRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = $request->user();

            $miniaturaPath = null;
            if ($request->hasFile('miniatura')) {
                $file = $request->file('miniatura');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $miniaturaPath = $file->storeAs('rutas/miniaturas', $filename, 'public');
            }

            $ruta = rutas::create([
                'nombre'              => $validated['nombre'],
                'descripcion'         => $validated['descripcion'] ?? null,
                'distancia_km'        => $validated['distancia_km'],
                'desnivel_positivo_m' => $validated['desnivel_positivo_m'] ?? null,
                'dificultad'          => $validated['dificultad'],
                'privacidad'          => $validated['privacidad'],
                'tipo_deporte'        => $validated['tipo_deporte'],
                'miniatura'           => $miniaturaPath,
                'usuario_id'          => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id'           => $ruta->id,
                    'nombre'       => $ruta->nombre,
                    'distancia_km' => $ruta->distancia_km,
                    'veces_usada'  => $ruta->veces_usada,
                    'usuario_id'   => $ruta->usuario_id,
                ]
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear ruta',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 2. POST /api/rutas/{rutaId}/puntos-gps
    // ═══════════════════════════════════════════════════════════════
    public function guardarPuntosGps(Request $request, $rutaId)
    {
        try {
            $user = $request->user();
            $ruta = rutas::where('id', $rutaId)
                ->where('usuario_id', $user->id)
                ->first();

            if (!$ruta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ruta no encontrada o no tienes permiso'
                ], 404);
            }

            $request->validate([
                'puntos'              => 'required|array|min:1',
                'puntos.*.latitud'    => 'required|numeric|between:-90,90',
                'puntos.*.longitud'   => 'required|numeric|between:-180,180',
                'puntos.*.altitud_m'  => 'nullable|numeric',
                'puntos.*.secuencia'  => 'required|integer|min:1',
            ]);

            // Eliminar puntos anteriores si existen (reemplazar trazado)
            $ruta->puntosGps()->delete();

            $puntosData = [];
            foreach ($request->puntos as $punto) {
                $puntosData[] = [
                    'ruta_id'    => $ruta->id,
                    'latitud'    => $punto['latitud'],
                    'longitud'   => $punto['longitud'],
                    'altitud_m'  => $punto['altitud_m'] ?? null,
                    'secuencia'  => $punto['secuencia'],
                ];
            }

            RutaPuntoGps::insert($puntosData);

            return response()->json([
                'success' => true,
                'message' => count($puntosData) . ' puntos de ruta guardados',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors'  => $e->errors()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar puntos GPS de ruta',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 3. GET /api/rutas (público)
    // ═══════════════════════════════════════════════════════════════
    public function listar(Request $request)
    {
        try {
            $query = rutas::with('usuario');

            // Sin auth → solo públicas; con auth → públicas + propias + amigos
            $user = $request->user();
            if ($user) {
                // IDs de amigos aceptados
                $amigosIds = $this->obtenerIdsAmigos($user->id);

                $query->where(function ($q) use ($user, $amigosIds) {
                    $q->where('privacidad', 'publico')
                      ->orWhere('usuario_id', $user->id)
                      ->orWhere(function ($q2) use ($amigosIds) {
                          $q2->where('privacidad', 'amigos')
                             ->whereIn('usuario_id', $amigosIds);
                      });
                });
            } else {
                $query->where('privacidad', 'publico');
            }

            // Filtros
            if ($request->has('tipo_deporte')) {
                $query->where('tipo_deporte', $request->tipo_deporte);
            }
            if ($request->has('dificultad')) {
                $query->where('dificultad', $request->dificultad);
            }

            // Ordenamiento
            $ordenar = $request->input('ordenar', 'created_at');
            $ordenesValidos = ['veces_usada', 'created_at', 'distancia_km'];
            if (in_array($ordenar, $ordenesValidos)) {
                $query->orderBy($ordenar, 'desc');
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $total = $query->count();

            // Paginación
            $limit  = $request->input('limit', 20);
            $offset = $request->input('offset', 0);

            $rutas = $query->limit($limit)
                ->offset($offset)
                ->get()
                ->map(function ($ruta) {
                    return [
                        'id'           => $ruta->id,
                        'nombre'       => $ruta->nombre,
                        'distancia_km' => $ruta->distancia_km,
                        'dificultad'   => $ruta->dificultad,
                        'veces_usada'  => $ruta->veces_usada,
                        'tipo_deporte' => $ruta->tipo_deporte,
                        'miniatura'    => $ruta->miniatura ? asset('storage/' . $ruta->miniatura) : null,
                        'usuario' => [
                            'username' => $ruta->usuario->username,
                            'avatar'   => asset('storage/' . $ruta->usuario->avatar),
                        ],
                    ];
                });

            return response()->json([
                'success' => true,
                'data'    => $rutas,
                'total'   => $total,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al listar rutas',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 4. GET /api/rutas/{rutaId}
    // ═══════════════════════════════════════════════════════════════
    public function detalle(Request $request, $rutaId)
    {
        try {
            $ruta = rutas::with(['usuario', 'puntosGps' => function ($q) {
                $q->orderBy('secuencia');
            }])->find($rutaId);

            if (!$ruta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ruta no encontrada'
                ], 404);
            }

            // Verificar privacidad
            $user = $request->user();

            if ($ruta->privacidad === 'privado') {
                if (!$user || $ruta->usuario_id !== $user->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes acceso a esta ruta'
                    ], 403);
                }
            }

            if ($ruta->privacidad === 'amigos') {
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes acceso a esta ruta'
                    ], 403);
                }

                if ($ruta->usuario_id !== $user->id) {
                    $esAmigo = amistades::where('estado', 'aceptada')
                        ->where(function ($q) use ($user, $ruta) {
                            $q->where(function ($q2) use ($user, $ruta) {
                                $q2->where('solicitante_id', $user->id)
                                   ->where('receptor_id', $ruta->usuario_id);
                            })->orWhere(function ($q2) use ($user, $ruta) {
                                $q2->where('solicitante_id', $ruta->usuario_id)
                                   ->where('receptor_id', $user->id);
                            });
                        })->exists();

                    if (!$esAmigo) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No tienes acceso a esta ruta'
                        ], 403);
                    }
                }
            }

            $puntosGps = $ruta->puntosGps->map(function ($punto) {
                return [
                    'latitud'    => $punto->latitud,
                    'longitud'   => $punto->longitud,
                    'altitud_m'  => $punto->altitud_m,
                    'secuencia'  => $punto->secuencia,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'id'                  => $ruta->id,
                    'nombre'              => $ruta->nombre,
                    'descripcion'         => $ruta->descripcion,
                    'distancia_km'        => $ruta->distancia_km,
                    'desnivel_positivo_m' => $ruta->desnivel_positivo_m,
                    'dificultad'          => $ruta->dificultad,
                    'privacidad'          => $ruta->privacidad,
                    'tipo_deporte'        => $ruta->tipo_deporte,
                    'veces_usada'         => $ruta->veces_usada,
                    'miniatura'           => $ruta->miniatura ? asset('storage/' . $ruta->miniatura) : null,
                    'usuario' => [
                        'id'       => $ruta->usuario->id,
                        'username' => $ruta->usuario->username,
                        'avatar'   => asset('storage/' . $ruta->usuario->avatar),
                    ],
                    'puntos_gps' => $puntosGps,
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener detalle de ruta',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 5. PUT /api/rutas/{rutaId}
    // ═══════════════════════════════════════════════════════════════
    public function actualizar(ActualizarRutaRequest $request, $rutaId)
    {
        try {
            $user = $request->user();
            $ruta = rutas::where('id', $rutaId)
                ->where('usuario_id', $user->id)
                ->first();

            if (!$ruta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ruta no encontrada o no tienes permiso'
                ], 404);
            }

            $validated = $request->validated();
            $ruta->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Ruta actualizada',
                'data' => [
                    'id'                  => $ruta->id,
                    'nombre'              => $ruta->nombre,
                    'descripcion'         => $ruta->descripcion,
                    'distancia_km'        => $ruta->distancia_km,
                    'desnivel_positivo_m' => $ruta->desnivel_positivo_m,
                    'dificultad'          => $ruta->dificultad,
                    'privacidad'          => $ruta->privacidad,
                    'tipo_deporte'        => $ruta->tipo_deporte,
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar ruta',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 6. DELETE /api/rutas/{rutaId}
    // ═══════════════════════════════════════════════════════════════
    public function eliminar(Request $request, $rutaId)
    {
        try {
            $user = $request->user();
            $ruta = rutas::where('id', $rutaId)
                ->where('usuario_id', $user->id)
                ->first();

            if (!$ruta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ruta no encontrada o no tienes permiso'
                ], 404);
            }

            // Eliminar miniatura del storage si existe
            if ($ruta->miniatura) {
                Storage::disk('public')->delete($ruta->miniatura);
            }

            // Eliminar puntos GPS de la ruta
            $ruta->puntosGps()->delete();

            // Eliminar la ruta (cascade eliminará las relaciones FK)
            $ruta->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ruta eliminada',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar ruta',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // Helper privado: obtener IDs de amigos aceptados
    // ═══════════════════════════════════════════════════════════════
    private function obtenerIdsAmigos(int $userId): array
    {
        $amigos1 = amistades::where('estado', 'aceptada')
            ->where('solicitante_id', $userId)
            ->pluck('receptor_id')
            ->toArray();

        $amigos2 = amistades::where('estado', 'aceptada')
            ->where('receptor_id', $userId)
            ->pluck('solicitante_id')
            ->toArray();

        return array_merge($amigos1, $amigos2);
    }
}
