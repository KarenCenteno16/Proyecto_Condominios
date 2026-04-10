<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Esto asegura que Laravel no intente redireccionar a una vista 'login'
        // cuando detecta que la petición viene de una API
        $middleware->redirectTo(
            guests: '/api/login',
            users: '/home'
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Esto fuerza a Laravel a responder siempre en JSON si la ruta empieza por /api
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }
            return $request->expectsJson();
        });
    })->create();