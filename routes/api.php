<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\ResidentesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ReporteController;

use App\Models\Usuario;
use Illuminate\Auth\Events\Verified;

//rutas publicas
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);

// recuperacion publicas
Route::post('/forgot-password', [AuthController::class, 'sendResetCode']);
Route::post('/reset-password', [AuthController::class, 'resetPasswordWithCode']);

//para verificar el correo
Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    $user = Usuario::findOrFail($id);

    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json([
            'res' => false,
            'mensaje' => 'Link inválido'
        ], 403);
    }

    if ($user->hasVerifiedEmail()) {
        return redirect('http://localhost:5173/?verified=1');
    }

    if ($user->markEmailAsVerified()) {
        event(new Verified($user));
    }

    return redirect('http://localhost:5173/?verified=1');
})->name('verification.verify');

//rutas que estan protegidas
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/update-password', [AuthController::class, 'updatePassword']);

    // Residentes
    Route::get('/residentes', [ResidentesController::class, 'index']);
    Route::put('/residentes/{id}', [ResidentesController::class, 'update']);
    Route::delete('/residentes/{id}', [ResidentesController::class, 'destroy']);
    Route::get('/departamentos', [ResidentesController::class, 'getDepartamentos']);

    // Dashboard
    Route::get('/dashboard-stats', [DashboardController::class, 'stats']);

    // Chat
    Route::get('/usuarios-chat', [ChatController::class, 'usuariosChat']);
    Route::get('/mensajes/{remitente}/{destinatario}', [ChatController::class, 'obtenerMensajes']);
    Route::post('/enviar-mensaje', [ChatController::class, 'enviarMensaje']);

    // Notificaciones
    Route::get('/notificaciones/{id}', function ($id) {
        return \App\Models\Notificacion::where('usuario_id', $id)
            ->where('leido', false)
            ->latest()
            ->get();
    });

    Route::post('/notificaciones/leer/{id}', function ($id) {
        \App\Models\Notificacion::where('id', $id)
            ->update(['leido' => true]);

        return response()->json(['res' => true]);
    });

    // Reportes
    Route::get('/reportes', [ReporteController::class, 'index']);
    Route::post('/reportes', [ReporteController::class, 'store']);
    Route::delete('/reportes/{id}', [ReporteController::class, 'destroy']);
    Route::put('/reportes/{id}', [ReporteController::class, 'update']);
    Route::get('/reportes/usuario/{id}', [ReporteController::class, 'reportesPorUsuario']);
});