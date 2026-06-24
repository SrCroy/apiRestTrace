<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\logros;
use App\Models\usuarios_logros;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class LogroController extends Controller
{
    // ═══════════════════════════════════════════════════════════════
    // 1. GET /api/logros — Catálogo completo de logros disponibles
    // ═══════════════════════════════════════════════════════════════
    public function catalogo(Request $request)
    {
        try {
            $user = $request->user(); // Puede ser null (ruta pública)

            // IDs de logros obtenidos por el usuario (si está autenticado)
            $logrosObtenidos = [];
            if ($user) {
                $logrosObtenidos = usuarios_logros::where('usuario_id', $user->id)
                    ->pluck('obtenido_en', 'logro_id')
                    ->toArray();
            }

            $todosLogros = logros::orderBy('id')->get()->map(function ($logro) use ($user, $logrosObtenidos) {
                $data = [
                    'id'               => $logro->id,
                    'clave'            => $logro->clave,
                    'nombre'           => $logro->nombre,
                    'descripcion'      => $logro->descripcion,
                    'icono'            => $logro->icono,
                    'tipo_disparador'  => $logro->tipo_disparador,
                    'valor_disparador' => $logro->valor_disparador,
                    'tipo_deporte'     => $logro->tipo_deporte,
                ];

                // Si el usuario está autenticado, indicar si ya lo obtuvo
                if ($user) {
                    $data['obtenido']    = isset($logrosObtenidos[$logro->id]);
                    $data['obtenido_en'] = $logrosObtenidos[$logro->id] ?? null;
                }

                return $data;
            });

            return response()->json([
                'success' => true,
                'data'    => $todosLogros,
                'total'   => $todosLogros->count(),
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener catálogo de logros',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 2. GET /api/logros/mis-logros — Logros del usuario autenticado
    // ═══════════════════════════════════════════════════════════════
    public function misLogros(Request $request)
    {
        try {
            $user = $request->user();

            $totalDisponibles = logros::count();

            $misLogros = usuarios_logros::where('usuario_id', $user->id)
                ->with('logro')
                ->orderBy('obtenido_en', 'desc')
                ->get()
                ->map(function ($ul) {
                    return [
                        'id'               => $ul->logro->id,
                        'clave'            => $ul->logro->clave,
                        'nombre'           => $ul->logro->nombre,
                        'descripcion'      => $ul->logro->descripcion,
                        'icono'            => $ul->logro->icono,
                        'tipo_disparador'  => $ul->logro->tipo_disparador,
                        'valor_disparador' => $ul->logro->valor_disparador,
                        'tipo_deporte'     => $ul->logro->tipo_deporte,
                        'obtenido_en'      => $ul->obtenido_en,
                    ];
                });

            $totalObtenidos = $misLogros->count();

            return response()->json([
                'success' => true,
                'data'    => $misLogros,
                'resumen' => [
                    'total_disponibles' => $totalDisponibles,
                    'total_obtenidos'   => $totalObtenidos,
                    'porcentaje'        => $totalDisponibles > 0
                        ? round(($totalObtenidos / $totalDisponibles) * 100, 1)
                        : 0,
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener mis logros',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 3. GET /api/logros/usuario/{userId} — Logros de un usuario
    // ═══════════════════════════════════════════════════════════════
    public function logrosDeUsuario(Request $request, $userId)
    {
        try {
            $usuario = User::find($userId);

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            $totalDisponibles = logros::count();

            $logrosUsuario = usuarios_logros::where('usuario_id', $userId)
                ->with('logro')
                ->orderBy('obtenido_en', 'desc')
                ->get()
                ->map(function ($ul) {
                    return [
                        'id'               => $ul->logro->id,
                        'clave'            => $ul->logro->clave,
                        'nombre'           => $ul->logro->nombre,
                        'descripcion'      => $ul->logro->descripcion,
                        'icono'            => $ul->logro->icono,
                        'tipo_deporte'     => $ul->logro->tipo_deporte,
                        'obtenido_en'      => $ul->obtenido_en,
                    ];
                });

            $totalObtenidos = $logrosUsuario->count();

            return response()->json([
                'success' => true,
                'data'    => $logrosUsuario,
                'usuario' => [
                    'id'       => $usuario->id,
                    'username' => $usuario->username,
                    'avatar'   => $usuario->avatar ? asset('storage/' . $usuario->avatar) : null,
                ],
                'resumen' => [
                    'total_disponibles' => $totalDisponibles,
                    'total_obtenidos'   => $totalObtenidos,
                    'porcentaje'        => $totalDisponibles > 0
                        ? round(($totalObtenidos / $totalDisponibles) * 100, 1)
                        : 0,
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener logros del usuario',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
