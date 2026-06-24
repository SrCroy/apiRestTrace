<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\comentarios;
use App\Models\grupos;
use App\Models\miembros_grupo;
use App\Models\publicaciones_grupo;
use App\Models\reacciones;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    // ═══════════════════════════════════════════════════════════════
    // 1. GET /api/grupos  —  Listar grupos
    // ═══════════════════════════════════════════════════════════════
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $query = grupos::query();

            // Filtros
            if ($request->has('buscar')) {
                $query->where('nombre', 'like', '%' . $request->buscar . '%');
            }
            if ($request->input('mis_grupos') === 'true') {
                $gruposIds = miembros_grupo::where('usuario_id', $user->id)
                    ->pluck('grupo_id');
                $query->whereIn('id', $gruposIds);
            }

            $total = $query->count();

            // Paginación
            $limit  = $request->input('limit', 15);
            $offset = $request->input('offset', 0);

            $grupos = $query
                ->with('propietario')
                ->withCount('miembros')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->offset($offset)
                ->get()
                ->map(function ($grupo) use ($user) {
                    $esMiembro = miembros_grupo::where('grupo_id', $grupo->id)
                        ->where('usuario_id', $user->id)
                        ->exists();

                    return [
                        'id'              => $grupo->id,
                        'nombre'          => $grupo->nombre,
                        'descripcion'     => $grupo->descripcion,
                        'avatar'          => $grupo->avatar ? asset('storage/' . $grupo->avatar) : null,
                        'privacidad'      => $grupo->privacidad,
                        'total_miembros'  => $grupo->miembros_count,
                        'es_miembro'      => $esMiembro,
                        'propietario'     => [
                            'id'       => $grupo->propietario->id,
                            'username' => $grupo->propietario->username,
                        ],
                        'created_at'      => $grupo->created_at,
                    ];
                });

            $pagina = (int) floor($offset / max($limit, 1)) + 1;

            return response()->json([
                'success' => true,
                'data'    => $grupos,
                'total'   => $total,
                'pagina'  => $pagina,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al listar grupos',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 2. GET /api/grupos/{id}  —  Detalle del grupo
    // ═══════════════════════════════════════════════════════════════
    public function show(Request $request, $id)
    {
        try {
            $user  = $request->user();
            $grupo = grupos::with(['propietario', 'miembros.usuario'])
                ->withCount('miembros')
                ->find($id);

            if (!$grupo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Grupo no encontrado'
                ], 404);
            }

            $miembro = miembros_grupo::where('grupo_id', $grupo->id)
                ->where('usuario_id', $user->id)
                ->first();

            // Si el grupo es privado y no es miembro, denegar acceso
            if ($grupo->privacidad === 'privado' && !$miembro) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes acceso a este grupo privado'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data'    => [
                    'id'             => $grupo->id,
                    'nombre'         => $grupo->nombre,
                    'descripcion'    => $grupo->descripcion,
                    'avatar'         => $grupo->avatar ? asset('storage/' . $grupo->avatar) : null,
                    'privacidad'     => $grupo->privacidad,
                    'total_miembros' => $grupo->miembros_count,
                    'es_miembro'     => $miembro !== null,
                    'mi_rol'         => $miembro?->rol,
                    'propietario'    => [
                        'id'       => $grupo->propietario->id,
                        'username' => $grupo->propietario->username,
                        'avatar'   => $grupo->propietario->avatar ? asset('storage/' . $grupo->propietario->avatar) : null,
                    ],
                    'miembros' => $grupo->miembros->map(function ($m) {
                        return [
                            'id'       => $m->usuario->id,
                            'username' => $m->usuario->username,
                            'avatar'   => $m->usuario->avatar ? asset('storage/' . $m->usuario->avatar) : null,
                            'rol'      => $m->rol,
                            'unido_en' => $m->unido_en,
                        ];
                    }),
                    'created_at' => $grupo->created_at,
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener grupo',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 3. POST /api/grupos  —  Crear grupo
    // ═══════════════════════════════════════════════════════════════
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre'      => 'required|string|max:150',
                'descripcion' => 'nullable|string|max:2000',
                'privacidad'  => 'in:publico,privado',
                'avatar'      => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            ]);

            $user = $request->user();

            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('grupos/avatars', 'public');
            }

            $grupo = grupos::create([
                'nombre'         => $request->nombre,
                'descripcion'    => $request->descripcion,
                'avatar'         => $avatarPath,
                'privacidad'     => $request->input('privacidad', 'publico'),
                'total_miembros' => 1,
                'propietario_id' => $user->id,
            ]);

            // El creador se agrega como miembro con rol propietario
            miembros_grupo::create([
                'grupo_id'   => $grupo->id,
                'usuario_id' => $user->id,
                'rol'        => 'propietario',
                'unido_en'   => Carbon::now(),
            ]);

            $grupo->load('propietario');

            return response()->json([
                'success' => true,
                'message' => 'Grupo creado',
                'data'    => [
                    'id'             => $grupo->id,
                    'nombre'         => $grupo->nombre,
                    'descripcion'    => $grupo->descripcion,
                    'avatar'         => $grupo->avatar ? asset('storage/' . $grupo->avatar) : null,
                    'privacidad'     => $grupo->privacidad,
                    'total_miembros' => $grupo->total_miembros,
                    'propietario'    => [
                        'id'       => $grupo->propietario->id,
                        'username' => $grupo->propietario->username,
                    ],
                ],
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear grupo',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 4. POST /api/grupos/{id}/unirse  —  Unirse a un grupo
    // ═══════════════════════════════════════════════════════════════
    public function unirse(Request $request, $id)
    {
        try {
            $user  = $request->user();
            $grupo = grupos::find($id);

            if (!$grupo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Grupo no encontrado'
                ], 404);
            }

            if ($grupo->privacidad === 'privado') {
                return response()->json([
                    'success' => false,
                    'message' => 'Este grupo es privado. Necesitas una invitación.'
                ], 403);
            }

            $yaMiembro = miembros_grupo::where('grupo_id', $grupo->id)
                ->where('usuario_id', $user->id)
                ->exists();

            if ($yaMiembro) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya eres miembro de este grupo'
                ], 422);
            }

            miembros_grupo::create([
                'grupo_id'   => $grupo->id,
                'usuario_id' => $user->id,
                'rol'        => 'miembro',
                'unido_en'   => Carbon::now(),
            ]);

            $grupo->increment('total_miembros');

            return response()->json([
                'success' => true,
                'message' => 'Te has unido al grupo',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al unirse al grupo',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 5. POST /api/grupos/{id}/salir  —  Salir de un grupo
    // ═══════════════════════════════════════════════════════════════
    public function salir(Request $request, $id)
    {
        try {
            $user  = $request->user();
            $grupo = grupos::find($id);

            if (!$grupo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Grupo no encontrado'
                ], 404);
            }

            if ($grupo->propietario_id === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'El propietario no puede abandonar el grupo. Transfiere la propiedad primero.'
                ], 422);
            }

            $miembro = miembros_grupo::where('grupo_id', $grupo->id)
                ->where('usuario_id', $user->id)
                ->first();

            if (!$miembro) {
                return response()->json([
                    'success' => false,
                    'message' => 'No eres miembro de este grupo'
                ], 422);
            }

            $miembro->delete();
            $grupo->decrement('total_miembros');

            return response()->json([
                'success' => true,
                'message' => 'Has salido del grupo',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al salir del grupo',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 6. GET /api/grupos/{id}/publicaciones  —  Publicaciones del grupo
    // ═══════════════════════════════════════════════════════════════
    public function publicaciones(Request $request, $id)
    {
        try {
            $user  = $request->user();
            $grupo = grupos::find($id);

            if (!$grupo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Grupo no encontrado'
                ], 404);
            }

            // Verificar acceso
            $esMiembro = miembros_grupo::where('grupo_id', $grupo->id)
                ->where('usuario_id', $user->id)
                ->exists();

            if ($grupo->privacidad === 'privado' && !$esMiembro) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes acceso a las publicaciones de este grupo'
                ], 403);
            }

            $total = publicaciones_grupo::where('grupo_id', $grupo->id)->count();

            $limit  = $request->input('limit', 15);
            $offset = $request->input('offset', 0);

            $publicaciones = publicaciones_grupo::where('grupo_id', $grupo->id)
                ->with(['usuario', 'actividad', 'ruta'])
                ->withCount(['reacciones', 'comentarios'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->offset($offset)
                ->get()
                ->map(function ($pub) use ($user) {
                    $yaReacciono = reacciones::where('usuario_id', $user->id)
                        ->where('reaccionable_tipo', publicaciones_grupo::class)
                        ->where('reaccionable_id', $pub->id)
                        ->exists();

                    $data = [
                        'id'                => $pub->id,
                        'contenido'         => $pub->contenido,
                        'created_at'        => $pub->created_at,
                        'total_reacciones'  => $pub->reacciones_count ?? 0,
                        'total_comentarios' => $pub->comentarios_count ?? 0,
                        'ya_reacciono'      => $yaReacciono,
                        'usuario'           => [
                            'id'       => $pub->usuario->id,
                            'username' => $pub->usuario->username,
                            'nombre'   => $pub->usuario->nombre,
                            'apellido' => $pub->usuario->apellido,
                            'avatar'   => $pub->usuario->avatar ? asset('storage/' . $pub->usuario->avatar) : null,
                        ],
                    ];

                    if ($pub->actividad) {
                        $data['actividad'] = [
                            'id'           => $pub->actividad->id,
                            'titulo'       => $pub->actividad->titulo,
                            'tipo_deporte' => $pub->actividad->tipo_deporte,
                            'distancia_km' => $pub->actividad->distancia_km,
                        ];
                    }

                    if ($pub->ruta) {
                        $data['ruta'] = [
                            'id'     => $pub->ruta->id,
                            'nombre' => $pub->ruta->nombre,
                        ];
                    }

                    return $data;
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
                'message' => 'Error al obtener publicaciones del grupo',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 7. POST /api/grupos/{id}/publicaciones  —  Publicar en grupo
    // ═══════════════════════════════════════════════════════════════
    public function publicar(Request $request, $id)
    {
        try {
            $request->validate([
                'contenido'    => 'required|string|max:5000',
                'actividad_id' => 'nullable|integer|exists:actividades,id',
                'ruta_id'      => 'nullable|integer|exists:rutas,id',
            ]);

            $user  = $request->user();
            $grupo = grupos::find($id);

            if (!$grupo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Grupo no encontrado'
                ], 404);
            }

            // Solo miembros pueden publicar
            $esMiembro = miembros_grupo::where('grupo_id', $grupo->id)
                ->where('usuario_id', $user->id)
                ->exists();

            if (!$esMiembro) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debes ser miembro del grupo para publicar'
                ], 403);
            }

            $publicacion = publicaciones_grupo::create([
                'contenido'    => $request->contenido,
                'ruta_id'      => $request->ruta_id,
                'actividad_id' => $request->actividad_id,
                'grupo_id'     => $grupo->id,
                'usuario_id'   => $user->id,
            ]);

            $publicacion->load('usuario');

            return response()->json([
                'success' => true,
                'message' => 'Publicación creada en el grupo',
                'data'    => [
                    'id'         => $publicacion->id,
                    'contenido'  => $publicacion->contenido,
                    'created_at' => $publicacion->created_at,
                    'usuario'    => [
                        'id'       => $publicacion->usuario->id,
                        'username' => $publicacion->usuario->username,
                        'avatar'   => $publicacion->usuario->avatar ? asset('storage/' . $publicacion->usuario->avatar) : null,
                    ],
                ],
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al publicar en grupo',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 8. POST /api/grupos/{grupoId}/publicaciones/{pubId}/reaccion
    // ═══════════════════════════════════════════════════════════════
    public function toggleReaccionGrupo(Request $request, $grupoId, $pubId)
    {
        try {
            $user = $request->user();

            $publicacion = publicaciones_grupo::where('id', $pubId)
                ->where('grupo_id', $grupoId)
                ->first();

            if (!$publicacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Publicación no encontrada en este grupo'
                ], 404);
            }

            $reaccionExistente = reacciones::where('usuario_id', $user->id)
                ->where('reaccionable_tipo', publicaciones_grupo::class)
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
                'reaccionable_tipo' => publicaciones_grupo::class,
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
    // 9. POST /api/grupos/{grupoId}/publicaciones/{pubId}/comentario
    // ═══════════════════════════════════════════════════════════════
    public function comentarGrupo(Request $request, $grupoId, $pubId)
    {
        try {
            $request->validate([
                'cuerpo'   => 'required|string|max:2000',
                'padre_id' => 'nullable|integer|exists:comentarios,id',
            ]);

            $user = $request->user();

            $publicacion = publicaciones_grupo::where('id', $pubId)
                ->where('grupo_id', $grupoId)
                ->first();

            if (!$publicacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Publicación no encontrada en este grupo'
                ], 404);
            }

            $comentario = comentarios::create([
                'comentable_tipo' => publicaciones_grupo::class,
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
}
