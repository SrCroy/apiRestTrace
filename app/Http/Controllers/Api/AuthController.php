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
    public function register(LoginRequest $request){
        try {
            $date = $request->validated();

            DB::beginTransaction();
            $user = User::create($date);

            $user['password'] = Hash::make($user['password']);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado con exito',
                'user' => $user
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'messaje' => 'Error de validacion',
                'error' => $e->errors(),
            ], 422);
        } catch (\Exception $e){
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el usuario',
                'error' => config('app.debug') ? $e->getMessage() : null
            ]);
        }
    }

    

    public function getAllUsers(){
        $users = User::all();

        return response()->json([
            'success' => true,
            'message' => 'Todos usuarios registrado en la api',
            'user' => $users
        ]);
    }
    
    public function login(Request $request){
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
            'token' => 'Bearer '. $token,
            'user' => $users
        ]);
    }


    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Sesion terminada',
        ]);
    }

    public function obtenerNombresUsuarios(){
        $users = User::pluck('nombre');
        return response()->json([
            'users' => $users
        ]);
    }
}
