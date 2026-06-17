<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'index'])->name('login.index');
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Descomenta estas cuando ya tengas los controladores listos:
    // Route::get('/usuarios', [UserController::class, 'index'])->name('admin.usuarios.index');
    // Route::get('/rutas', [RutaController::class, 'index'])->name('admin.rutas.index');
    // Route::get('/actividades', [ActividadController::class, 'index'])->name('admin.actividades.index');
    
});

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');