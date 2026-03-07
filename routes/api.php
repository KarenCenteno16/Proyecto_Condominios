<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResidentesController; 
use App\Http\Controllers\DashboardController;
use App\Events\Mensaje; 
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ReporteController;


// Esta es la ruta que te falta y está causando el 404
Route::get('/reportes/usuario/{id}', [ReporteController::class, 'getPorUsuario']);

Route::post('/login', [AuthController::class, 'login']);

Route::get('/residentes', [ResidentesController::class, 'index']);
Route::put('/residentes/{id}', [ResidentesController::class, 'update']);
Route::delete('/residentes/{id}', [ResidentesController::class, 'destroy']);
Route::get('/departamentos', [ResidentesController::class, 'getDepartamentos']);
Route::get('/dashboard-stats', [DashboardController::class, 'stats']); 



Route::get('/usuarios-chat', [ChatController::class, 'usuariosChat']);
Route::get('/mensajes/{remitente}/{destinatario}', [ChatController::class, 'obtenerMensajes']);
Route::post('/enviar-mensaje', [ChatController::class, 'enviarMensaje']);

Route::get('/usuarios-chat', [ChatController::class, 'usuariosChat']);

// Listado de notificaciones del usuario
Route::get('/notificaciones/{id}', function($id) {
    return App\Models\Notificacion::where('usuario_id', $id)
        ->where('leido', false)
        ->latest()
        ->get();
});

// Marcar como leída
Route::post('/notificaciones/leer/{id}', function($id) {
    App\Models\Notificacion::where('id', $id)->update(['leido' => true]);
    return response()->json(['res' => true]);
});

Route::get('/reportes', [ReporteController::class, 'index']);
Route::post('/reportes', [ReporteController::class, 'store']);
Route::delete('/reportes/{id}', [ReporteController::class, 'destroy']);
Route::put('/reportes/{id}', [ReporteController::class, 'update']);


Route::get('/reportes/usuario/{id}', [ReporteController::class, 'reportesPorUsuario']);

// // ruta para (WebSocket)
// Route::post('/enviar-mensaje', function (Request $request) {
//     // valida que el mensaje no llegue vacío
//     $request->validate(['texto' => 'required|string']);

//     // se dispara el evento en app/Events/Mensaje.php
//     broadcast(new Mensaje($request->texto))->toOthers(); 

//     return response()->json([
//         'ok' => true, 
//         'mensaje' => $request->texto
//     ]);
// });

