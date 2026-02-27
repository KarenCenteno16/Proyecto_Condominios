<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResidentesController; 
use App\Http\Controllers\DashboardController;
use App\Events\Mensaje; 
use Illuminate\Http\Request;

Route::get('/residentes', [ResidentesController::class, 'index']);
Route::put('/residentes/{id}', [ResidentesController::class, 'update']);
Route::delete('/residentes/{id}', [ResidentesController::class, 'destroy']);
Route::get('/departamentos', [ResidentesController::class, 'getDepartamentos']);
Route::get('/dashboard-stats', [DashboardController::class, 'stats']); 

// ruta para (WebSocket)
Route::post('/enviar-mensaje', function (Request $request) {
    // valida que el mensaje no llegue vacío
    $request->validate(['texto' => 'required|string']);

    // se dispara el evento en app/Events/Mensaje.php
    broadcast(new Mensaje($request->texto))->toOthers(); 

    return response()->json([
        'ok' => true, 
        'mensaje' => $request->texto
    ]);
});