<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChechkAdminRole
{
    /**
     * Handle an incoming request.
     * Permite acceso a usuarios con rol 'admin' o 'moderador'.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && in_array(Auth::user()->rol, ['admin', 'moderador'])) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Acceso no autorizado'
            ]);
        }

        return redirect('/')->with('error', 'No tienes los permisos necesarios para acceder al panel.');
    }
}

