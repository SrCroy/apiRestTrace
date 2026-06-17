<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'index'])->name('login.index');
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Supervisión: Usuarios
    Route::get('/usuarios', [AdminController::class, 'usuarios'])->name('admin.usuarios.index');
    Route::get('/usuarios/baneados', [AdminController::class, 'baneados'])->name('admin.usuarios.baneados');
    Route::get('/usuarios/{id}', [AdminController::class, 'usuarioShow'])->name('admin.usuarios.show');
    Route::patch('/usuarios/{id}/banear', [AdminController::class, 'banear'])->name('admin.usuarios.banear');
    Route::patch('/usuarios/{id}/desbanear', [AdminController::class, 'desbanear'])->name('admin.usuarios.desbanear');
    Route::put('/usuarios/{id}/actualizar', [AdminController::class, 'actualizarUsuario'])->name('admin.usuarios.actualizar');
    Route::patch('/usuarios/{id}/cambiar-rol', [AdminController::class, 'cambiarRol'])->name('admin.usuarios.cambiarRol');
    Route::patch('/usuarios/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('admin.usuarios.resetPassword');
    Route::delete('/usuarios/{id}/eliminar', [AdminController::class, 'eliminarUsuario'])->name('admin.usuarios.eliminar');
    
    // Supervisión: Rutas
    Route::get('/rutas', [AdminController::class, 'rutas'])->name('admin.rutas.index');
    
    // Supervisión: Actividades
    Route::get('/actividades', [AdminController::class, 'actividades'])->name('admin.actividades.index');
    
    // Moderación: Reportes
    Route::get('/reportes', [AdminController::class, 'reportes'])->name('admin.reportes.index');
    Route::get('/reportes/{id}', [AdminController::class, 'reporteShow'])->name('admin.reportes.show');
    Route::patch('/reportes/{id}/resolver', [AdminController::class, 'reporteResolver'])->name('admin.reportes.resolver');
    Route::patch('/reportes/{id}/descartar', [AdminController::class, 'reporteDescartar'])->name('admin.reportes.descartar');
    
    // Configuración: Logros Personalizados
    Route::get('/logros', [AdminController::class, 'logros'])->name('admin.logros.index');
    Route::get('/logros/{id}', [AdminController::class, 'logroShow'])->name('admin.logros.show');
    Route::patch('/logros/{id}/aprobar', [AdminController::class, 'logroAprobar'])->name('admin.logros.aprobar');
    Route::patch('/logros/{id}/rechazar', [AdminController::class, 'logroRechazar'])->name('admin.logros.rechazar');
});

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');