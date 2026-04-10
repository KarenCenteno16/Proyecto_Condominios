<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\ResidentesController; 
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ReporteController;
use App\Models\Usuario;
use Illuminate\Auth\Events\Verified;

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación y Verificación
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    // 1. Buscamos al usuario
    $user = Usuario::findOrFail($id);

    // 2. Validamos seguridad del hash
    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['res' => false, 'mensaje' => 'Link inválido'], 403);
    }

    // 3. Si ya está verificado, mandamos directo a React
    if ($user->hasVerifiedEmail()) {
        return redirect('http://localhost:5173/?verified=1');
    }

    // 4. Marcamos verificación
    if ($user->markEmailAsVerified()) {
        event(new Verified($user));
    }

    // 5. Redirigimos al puerto 5173 (donde está tu Vite/React)
    return redirect('http://localhost:5173/?verified=1');
})->name('verification.verify');

/*
|--------------------------------------------------------------------------
| Rutas de Residentes y Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/residentes', [ResidentesController::class, 'index']);
Route::put('/residentes/{id}', [ResidentesController::class, 'update']);
Route::delete('/residentes/{id}', [ResidentesController::class, 'destroy']);
Route::get('/departamentos', [ResidentesController::class, 'getDepartamentos']);
Route::get('/dashboard-stats', [DashboardController::class, 'stats']); 


/*
|--------------------------------------------------------------------------
| Rutas de Chat
|--------------------------------------------------------------------------
*/

Route::get('/usuarios-chat', [ChatController::class, 'usuariosChat']);
Route::get('/mensajes/{remitente}/{destinatario}', [ChatController::class, 'obtenerMensajes']);
Route::post('/enviar-mensaje', [ChatController::class, 'enviarMensaje']);


/*
|--------------------------------------------------------------------------
| Rutas de Notificaciones
|--------------------------------------------------------------------------
*/

Route::get('/notificaciones/{id}', function($id) {
    return App\Models\Notificacion::where('usuario_id', $id)
        ->where('leido', false)
        ->latest()
        ->get();
});

Route::post('/notificaciones/leer/{id}', function($id) {
    App\Models\Notificacion::where('id', $id)->update(['leido' => true]);
    return response()->json(['res' => true]);
});


/*
|--------------------------------------------------------------------------
| Rutas de Reportes
|--------------------------------------------------------------------------
*/

Route::get('/reportes', [ReporteController::class, 'index']);
Route::post('/reportes', [ReporteController::class, 'store']);
Route::delete('/reportes/{id}', [ReporteController::class, 'destroy']);
Route::put('/reportes/{id}', [ReporteController::class, 'update']);

// Dejamos solo una ruta para reportes por usuario (la más específica)
Route::get('/reportes/usuario/{id}', [ReporteController::class, 'reportesPorUsuario']);