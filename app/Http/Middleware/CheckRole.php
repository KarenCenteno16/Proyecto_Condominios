<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        // obtiene usuario que intenta entrar
        $user = $request->user();

        // si el middleware pide 'admin' pero el usuario no lo es, bloqueamos
        if ($role === 'admin' && !$user->admin) {
            return response()->json(['mensaje' => 'No tienes permisos de administrador'], 403);
        }
        return $next($request);
    }
}
