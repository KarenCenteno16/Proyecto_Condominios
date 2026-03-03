<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResidentesController; 
use App\Http\Controllers\DashboardController;
use App\Events\Mensaje; 
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;


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

