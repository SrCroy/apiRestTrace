<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\amistades;
use App\Models\comentarios;
use App\Models\publicacion_archivo;
use App\Models\publicaciones;
use App\Models\reacciones;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicacionController extends Controller
{
    // ═══════════════════════════════════════════════════════════════
    // 1. GET /api/publicaciones  —  Feed / Muro
    // ═══════════════════════════════════════════════════════════════
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            // Obtener IDs de amigos
            $amigosIds = $this->obtenerIdsAmigos($user->id);

            $query = publicaciones::where('estado', 'activo')
                ->where(function ($q) use ($user, $amigosIds) {
                    // Publicaciones propias
                    $q->where('usuario_id', $user->id)
                      // Publicaciones públicas de cualquier usuario
                      ->orWhere('privacidad', 'publico')
                      // Publicaciones de amigos con privacidad "amigos"
                      ->orWhere(function ($q2) use ($amigosIds) {
                          $q2->whereIn('usuario_id', $amigosIds)
                             ->where('privacidad', 'amigos');
                      });
                });

            // Filtros opcionales
            if ($request->has('usuario_id')) {
                $query->where('usuario_id', $request->usuario_id);
            }

            $total = $query->count();

            // Paginación
            $limit  = $request->input('limit', 15);
            $offset = $request->input('offset', 0);

            $publicaciones = $query
                ->with(['usuario', 'archivos', 'actividad', 'ruta'])
                ->withCount(['reacciones', 'comentarios'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->offset($offset)
                ->get()
                ->map(function ($pub) use ($user) {
                    return $this->formatearPublicacion($pub, $user->id);
                });

            $pagina = (int) floor($offset / max($limit, 1)) + 1;

            return response()->json([
                'success' => true,
                'data'    => $publicaciones,
                'total'   => $total,
                'pagina'  => $pagina,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener publicaciones',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 2. POST /api/publicaciones  —  Crear publicación
    // ═══════════════════════════════════════════════════════════════
    public function store(Request $request)
    {
        try {
            $request->validate([
                'contenido'    => 'nullable|string|max:5000',
                'privacidad'   => 'in:publico,amigos,privado',
                'actividad_id' => 'nullable|integer|exists:actividades,id',
                'ruta_id'      => 'nullable|integer|exists:rutas,id',
                'archivos'     => 'nullable|array|max:10',
                'archivos.*'   => 'file|mimes:jpg,jpeg,png,gif,mp4,mov|max:20480',
            ]);

            $user = $request->user();

            // Validar que al menos haya contenido o archivos
            if (!$request->contenido && !$request->hasFile('archivos') && !$request->actividad_id && !$request->ruta_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'La publicación debe tener contenido, archivos, una actividad o una ruta'
                ], 422);
            }

            $publicacion = publicaciones::create([
                'contenido'    => $request->contenido,
                'privacidad'   => $request->input('privacidad', 'publico'),
                'estado'       => 'activo',
                'usuario_id'   => $user->id,
                'actividad_id' => $request->actividad_id,
                'ruta_id'      => $request->ruta_id,
            ]);

            // Subir archivos adjuntos
            if ($request->hasFile('archivos')) {
                foreach ($request->file('archivos') as $index => $archivo) {
                    $extension = strtolower($archivo->getClientOriginalExtension());
                    $tipo = in_array($extension, ['mp4', 'mov']) ? 'video' : 'foto';

                    $path = $archivo->store('publicaciones/' . $publicacion->id, 'public');

                    publicacion_archivo::create([
                        'url'            => $path,
                        'tipo'           => $tipo,
                        'orden'          => $index,
                        'publicacion_id' => $publicacion->id,
                    ]);
                }
            }

            // Recargar con relaciones
            $publicacion->load(['usuario', 'archivos', 'actividad', 'ruta']);
            $publicacion->loadCount(['reacciones', 'comentarios']);

            return response()->json([
                'success' => true,
                'message' => 'Publicación creada',
                'data'    => $this->formatearPublicacion($publicacion, $user->id),
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear publicación',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 3. GET /api/publicaciones/{id}  —  Detalle
    // ═══════════════════════════════════════════════════════════════
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();

            $publicacion = publicaciones::with([
                'usuario', 'archivos', 'actividad', 'ruta',
                'comentarios' => function ($q) {
                    $q->where('estado', 'activo')
                      ->whereNull('padre_id')
                      ->with(['usuario', 'respuestas' => function ($q2) {
                          $q2->where('estado', 'activo')->with('usuario');
                      }])
                      ->orderBy('created_at', 'desc');
                },
            ])
            ->withCount(['reacciones', 'comentarios'])
            ->find($id);

            if (!$publicacion || $publicacion->estado !== 'activo') {
                return response()->json([
                    'success' => false,
                    'message' => 'Publicación no encontrada'
                ], 404);
            }

            // Verificar acceso por privacidad
            if (!$this->tieneAcceso($publicacion, $user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes acceso a esta publicación'
                ], 403);
            }

            $data = $this->formatearPublicacion($publicacion, $user->id);

            // Agregar comentarios al detalle
            $data['comentarios'] = $publicacion->comentarios->map(function ($com) {
                return [
                    'id'         => $com->id,
                    'cuerpo'     => $com->cuerpo,
                    'created_at' => $com->created_at,
                    'usuario'    => [
                        'id'       => $com->usuario->id,
                        'username' => $com->usuario->username,
                        'avatar'   => $com->usuario->avatar ? asset('storage/' . $com->usuario->avatar) : null,
                    ],
                    'respuestas' => $com->respuestas->map(function ($resp) {
                        return [
                            'id'         => $resp->id,
                            'cuerpo'     => $resp->cuerpo,
                            'created_at' => $resp->created_at,
                            'usuario'    => [
                                'id'       => $resp->usuario->id,
                                'username' => $resp->usuario->username,
                                'avatar'   => $resp->usuario->avatar ? asset('storage/' . $resp->usuario->avatar) : null,
                            ],
                        ];
                    }),
                ];
            });

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener publicación',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 4. PUT /api/publicaciones/{id}  —  Editar publicación
    // ═══════════════════════════════════════════════════════════════
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'contenido'  => 'nullable|string|max:5000',
                'privacidad' => 'in:publico,amigos,privado',
            ]);

            $user = $request->user();
            $publicacion = publicaciones::where('id', $id)
                ->where('usuario_id', $user->id)
                ->where('estado', 'activo')
                ->first();

            if (!$publicacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Publicación no encontrada'
                ], 404);
            }

            $publicacion->update($request->only(['contenido', 'privacidad']));

            $publicacion->load(['usuario', 'archivos', 'actividad', 'ruta']);
            $publicacion->loadCount(['reacciones', 'comentarios']);

            return response()->json([
                'success' => true,
                'message' => 'Publicación actualizada',
                'data'    => $this->formatearPublicacion($publicacion, $user->id),
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar publicación',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 5. DELETE /api/publicaciones/{id}  —  Eliminar publicación
    // ═══════════════════════════════════════════════════════════════
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();
            $publicacion = publicaciones::where('id', $id)
                ->where('usuario_id', $user->id)
                ->where('estado', 'activo')
                ->first();

            if (!$publicacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Publicación no encontrada'
                ], 404);
            }

            $publicacion->update(['estado' => 'eliminado']);

            return response()->json([
                'success' => true,
                'message' => 'Publicación eliminada',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar publicación',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 6. POST /api/publicaciones/{id}/reaccion  —  Like / Unlike
    // ═══════════════════════════════════════════════════════════════
    public function toggleReaccion(Request $request, $id)
    {
        try {
            $user = $request->user();
            $publicacion = publicaciones::where('id', $id)
                ->where('estado', 'activo')
                ->first();

            if (!$publicacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Publicación no encontrada'
                ], 404);
            }

            $reaccionExistente = reacciones::where('usuario_id', $user->id)
                ->where('reaccionable_tipo', publicaciones::class)
                ->where('reaccionable_id', $publicacion->id)
                ->first();

            if ($reaccionExistente) {
                $reaccionExistente->delete();
                $totalReacciones = $publicacion->reacciones()->count();

                return response()->json([
                    'success'          => true,
                    'message'          => 'Reacción eliminada',
                    'reaccionado'      => false,
                    'total_reacciones' => $totalReacciones,
                ]);
            }

            reacciones::create([
                'reaccionable_tipo' => publicaciones::class,
                'reaccionable_id'   => $publicacion->id,
                'usuario_id'        => $user->id,
                'creado_en'         => Carbon::now(),
            ]);

            $totalReacciones = $publicacion->reacciones()->count();

            return response()->json([
                'success'          => true,
                'message'          => 'Reacción agregada',
                'reaccionado'      => true,
                'total_reacciones' => $totalReacciones,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar reacción',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 7. POST /api/publicaciones/{id}/comentario  —  Comentar
    // ═══════════════════════════════════════════════════════════════
    public function comentar(Request $request, $id)
    {
        try {
            $request->validate([
                'cuerpo'   => 'required|string|max:2000',
                'padre_id' => 'nullable|integer|exists:comentarios,id',
            ]);

            $user = $request->user();
            $publicacion = publicaciones::where('id', $id)
                ->where('estado', 'activo')
                ->first();

            if (!$publicacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Publicación no encontrada'
                ], 404);
            }

            $comentario = comentarios::create([
                'comentable_tipo' => publicaciones::class,
                'comentable_id'   => $publicacion->id,
                'cuerpo'          => $request->cuerpo,
                'estado'          => 'activo',
                'padre_id'        => $request->padre_id,
                'usuario_id'      => $user->id,
            ]);

            $comentario->load('usuario');

            return response()->json([
                'success' => true,
                'message' => 'Comentario agregado',
                'data'    => [
                    'id'         => $comentario->id,
                    'cuerpo'     => $comentario->cuerpo,
                    'padre_id'   => $comentario->padre_id,
                    'created_at' => $comentario->created_at,
                    'usuario'    => [
                        'id'       => $comentario->usuario->id,
                        'username' => $comentario->usuario->username,
                        'avatar'   => $comentario->usuario->avatar ? asset('storage/' . $comentario->usuario->avatar) : null,
                    ],
                ],
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar comentario',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // HELPERS PRIVADOS
    // ═══════════════════════════════════════════════════════════════

    /**
     * Obtener IDs de amigos aceptados del usuario.
     */
    private function obtenerIdsAmigos(int $userId): array
    {
        $comSolicitante = amistades::where('solicitante_id', $userId)
            ->where('estado', 'aceptada')
            ->pluck('receptor_id');

        $comoReceptor = amistades::where('receptor_id', $userId)
            ->where('estado', 'aceptada')
            ->pluck('solicitante_id');

        return $comSolicitante->merge($comoReceptor)->unique()->values()->toArray();
    }

    /**
     * Verificar si el usuario tiene acceso a la publicación.
     */
    private function tieneAcceso(publicaciones $pub, int $userId): bool
    {
        if ($pub->usuario_id === $userId) return true;
        if ($pub->privacidad === 'publico') return true;
        if ($pub->privacidad === 'privado') return false;

        // privacidad = 'amigos'
        $amigosIds = $this->obtenerIdsAmigos($userId);
        return in_array($pub->usuario_id, $amigosIds);
    }

    /**
     * Formatear una publicación para la respuesta JSON.
     */
    private function formatearPublicacion(publicaciones $pub, int $userId): array
    {
        $yaReacciono = reacciones::where('usuario_id', $userId)
            ->where('reaccionable_tipo', publicaciones::class)
            ->where('reaccionable_id', $pub->id)
            ->exists();

        $data = [
            'id'               => $pub->id,
            'contenido'        => $pub->contenido,
            'privacidad'       => $pub->privacidad,
            'created_at'       => $pub->created_at,
            'total_reacciones' => $pub->reacciones_count ?? 0,
            'total_comentarios'=> $pub->comentarios_count ?? 0,
            'ya_reacciono'     => $yaReacciono,
            'usuario'          => [
                'id'       => $pub->usuario->id,
                'username' => $pub->usuario->username,
                'nombre'   => $pub->usuario->nombre,
                'apellido' => $pub->usuario->apellido,
                'avatar'   => $pub->usuario->avatar ? asset('storage/' . $pub->usuario->avatar) : null,
            ],
            'archivos' => $pub->archivos->map(function ($archivo) {
                return [
                    'id'   => $archivo->id,
                    'url'  => asset('storage/' . $archivo->url),
                    'tipo' => $archivo->tipo,
                ];
            }),
        ];

        // Incluir actividad vinculada si existe
        if ($pub->actividad) {
            $data['actividad'] = [
                'id'           => $pub->actividad->id,
                'titulo'       => $pub->actividad->titulo,
                'tipo_deporte' => $pub->actividad->tipo_deporte,
                'distancia_km' => $pub->actividad->distancia_km,
                'duracion_seg' => $pub->actividad->duracion_seg,
                'calorias'     => $pub->actividad->calorias,
            ];
        }

        // Incluir ruta vinculada si existe
        if ($pub->ruta) {
            $data['ruta'] = [
                'id'           => $pub->ruta->id,
                'nombre'       => $pub->ruta->nombre,
                'distancia_km' => $pub->ruta->distancia_km,
                'dificultad'   => $pub->ruta->dificultad,
            ];
        }

        return $data;
    }
}
