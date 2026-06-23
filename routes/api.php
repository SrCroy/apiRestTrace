<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/nombres', [AuthController::class, 'obtenerNombresUsuarios']);

// Rutas protegidas (requieren autenticación)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/getAllUsers', [AuthController::class, 'getAllUsers']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/avatar/update', [AuthController::class, 'updateAvatar']);
});