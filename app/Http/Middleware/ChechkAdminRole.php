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
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->rol === 'admin') {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Acceso no autorizado'
            ]);
        }

        return redirect('/')->with('error', 'No tienes los permisos de administrador');
    }
}
