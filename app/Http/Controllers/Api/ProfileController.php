<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function miPerfil(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'nombre' => $user->nombre,
                    'apellido' => $user->apellido,
                    'email' => $user->email,
                    'avatar' => asset('storage/' . $user->avatar),
                    'biografia' => $user->biografia,
                    'peso_kg' => $user->peso_kg,
                    'altura_cm' => $user->altura_cm,
                    'fecha_nacimiento' => $user->fecha_nacimiento,
                    'rol' => $user->rol,
                    'estado' => $user->estado,
                    'creado_en' => $user->created_at,
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener perfil',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function verPerfil($userId)
    {
        try {
            $user = User::find($userId);

            if (!$user || $user->esta_baneado) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'nombre' => $user->nombre,
                    'apellido' => $user->apellido,
                    'avatar' => asset('storage/' . $user->avatar),
                    'biografia' => $user->biografia,
                    'fecha_nacimiento' => $user->fecha_nacimiento,
                    'rol' => $user->rol,
                    'creado_en' => $user->created_at,
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener perfil',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function editarPerfil(UpdateProfileRequest $request)
    {
        try {
            $user = $request->user();
            $validated = $request->validated();

            $user->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado correctamente',
                'data' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'nombre' => $user->nombre,
                    'apellido' => $user->apellido,
                    'email' => $user->email,
                    'avatar' => asset('storage/' . $user->avatar),
                    'biografia' => $user->biografia,
                    'peso_kg' => $user->peso_kg,
                    'altura_cm' => $user->altura_cm,
                    'fecha_nacimiento' => $user->fecha_nacimiento,
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar perfil',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function cambiarContrasena(ChangePasswordRequest $request)
    {
        try {
            $user = $request->user();
            $validated = $request->validated();

            if (!Hash::check($validated['password_actual'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contraseña actual incorrecta'
                ], 401);
            }

            $user->update([
                'password' => Hash::make($validated['password_nueva'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada correctamente'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar contraseña',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}