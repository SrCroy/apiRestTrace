<?php

use App\Http\Controllers\Api\ActividadController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GrupoController;
use App\Http\Controllers\Api\LogroController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PublicacionController;
use App\Http\Controllers\Api\RutaController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/nombres', [AuthController::class, 'obtenerNombresUsuarios']);
Route::get('/perfil/{userId}', [ProfileController::class, 'verPerfil']);

// Rutas públicas (Módulo 4)
Route::get('/rutas', [RutaController::class, 'listar']);
Route::get('/rutas/{rutaId}', [RutaController::class, 'detalle']);

// Rutas públicas (Módulo 5)
Route::get('/logros', [LogroController::class, 'catalogo']);
Route::get('/logros/usuario/{userId}', [LogroController::class, 'logrosDeUsuario']);

// Rutas protegidas (requieren autenticación)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/getAllUsers', [AuthController::class, 'getAllUsers']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/avatar/update', [AuthController::class, 'updateAvatar']);

    // Perfil
    Route::get('/perfil', [ProfileController::class, 'miPerfil']);
    Route::put('/perfil/editar', [ProfileController::class, 'editarPerfil']);
    Route::post('/perfil/cambiar-contraseña', [ProfileController::class, 'cambiarContrasena']);

    // ─── Módulo 3: Actividades y GPS ─────────────────────────
    Route::post('/actividades/iniciar', [ActividadController::class, 'iniciar']);
    Route::post('/actividades/{actividadId}/gps', [ActividadController::class, 'enviarGps']);
    Route::post('/actividades/{actividadId}/pausar', [ActividadController::class, 'pausar']);
    Route::post('/actividades/{actividadId}/reanudar', [ActividadController::class, 'reanudar']);
    Route::post('/actividades/{actividadId}/finalizar', [ActividadController::class, 'finalizar']);
    Route::post('/actividades/{actividadId}/descartar', [ActividadController::class, 'descartar']);
    Route::get('/actividades', [ActividadController::class, 'listar']);
    Route::get('/actividades/{actividadId}', [ActividadController::class, 'detalle']);
    Route::delete('/actividades/{actividadId}', [ActividadController::class, 'eliminar']);

    // ─── Módulo 4: Rutas (protegidas) ────────────────────────
    Route::post('/rutas', [RutaController::class, 'crear']);
    Route::post('/rutas/{rutaId}/puntos-gps', [RutaController::class, 'guardarPuntosGps']);
    Route::put('/rutas/{rutaId}', [RutaController::class, 'actualizar']);
    Route::delete('/rutas/{rutaId}', [RutaController::class, 'eliminar']);

    // ─── Módulo 5: Logros Globales (protegidas) ──────────────
    Route::get('/logros/mis-logros', [LogroController::class, 'misLogros']);

    // ─── Módulo 6: Muro (Publicaciones) ─────────────────────────
    Route::get('/publicaciones', [PublicacionController::class, 'index']);
    Route::post('/publicaciones', [PublicacionController::class, 'store']);
    Route::get('/publicaciones/{id}', [PublicacionController::class, 'show']);
    Route::put('/publicaciones/{id}', [PublicacionController::class, 'update']);
    Route::delete('/publicaciones/{id}', [PublicacionController::class, 'destroy']);
    Route::post('/publicaciones/{id}/reaccion', [PublicacionController::class, 'toggleReaccion']);
    Route::post('/publicaciones/{id}/comentario', [PublicacionController::class, 'comentar']);

    // ─── Módulo 7: Grupos ────────────────────────────────────────
    Route::get('/grupos', [GrupoController::class, 'index']);
    Route::post('/grupos', [GrupoController::class, 'store']);
    Route::get('/grupos/{id}', [GrupoController::class, 'show']);
    Route::post('/grupos/{id}/unirse', [GrupoController::class, 'unirse']);
    Route::post('/grupos/{id}/salir', [GrupoController::class, 'salir']);
    Route::get('/grupos/{id}/publicaciones', [GrupoController::class, 'publicaciones']);
    Route::post('/grupos/{id}/publicaciones', [GrupoController::class, 'publicar']);
    Route::post('/grupos/{grupoId}/publicaciones/{pubId}/reaccion', [GrupoController::class, 'toggleReaccionGrupo']);
    Route::post('/grupos/{grupoId}/publicaciones/{pubId}/comentario', [GrupoController::class, 'comentarGrupo']);
});