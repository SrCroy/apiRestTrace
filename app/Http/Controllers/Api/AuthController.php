<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use DB;
use Dotenv\Exception\ValidationException;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Testing\Fluent\Concerns\Has;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string|unique:users,username',
                'nombre' => 'required|string',
                'apellido' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
            ]);

            DB::beginTransaction();

            $user = User::create([
                'username' => $request->username,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado con éxito',
                'data' => [
                    'user' => $user
                ]
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el usuario',
                'errors' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }



    public function getAllUsers()
    {
        $users = User::all();

        return response()->json([
            'success' => true,
            'message' => 'Todos usuarios registrado en la api',
            'user' => $users
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $users = User::where('email', $request->email)->first();

        if (!$users || !Hash::check($request->password, $users->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales invalidas'
            ], 401);
        }

        $token = $users->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'data' => [
                'token' => $token,
                'user' => $users
            ]
        ]);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Sesion terminada',
        ]);
    }

    public function obtenerNombresUsuarios()
    {
        $users = User::pluck('nombre');
        return response()->json([
            'users' => $users
        ]);
    }
}
