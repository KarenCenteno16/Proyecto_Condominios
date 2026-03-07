<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mensaje;
use App\Models\Usuario;
use App\Models\Notificacion; 
use App\Events\NuevoMensaje;
use App\Events\NuevaNotificacion;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function usuariosChat() {
        return DB::table('usuarios')
            ->join('departamentos', 'usuarios.id', '=', 'departamentos.id') 
            ->select('usuarios.*', 'departamentos.depa', 'departamentos.moroso')
            ->get();
    }

    public function obtenerMensajes($remitente, $destinatario) {
        return Mensaje::where(function($q) use ($remitente, $destinatario) {
            $q->where('remitente', $remitente)->where('destinatario', $destinatario);
        })->orWhere(function($q) use ($remitente, $destinatario) {
            $q->where('remitente', $destinatario)->where('destinatario', $remitente);
        })->orderBy('created_at', 'asc')->get();
    }

    public function enviarMensaje(Request $request) {
        try {
            $destinatarioId = intval($request->destinatario_id);
            $remitenteId = intval($request->remitente_id);

            // 1. Buscamos al remitente usando la misma lógica que tu función usuariosChat()
            // Usamos select con alias para evitar el error de columnas duplicadas
            $remitenteInfo = DB::table('usuarios')
                ->join('departamentos', 'usuarios.id', '=', 'departamentos.id')
                ->where('usuarios.id', $remitenteId)
                ->select('usuarios.id as user_id', 'departamentos.depa')
                ->first();

            // Si por alguna razón no lo encuentra en departamentos, ponemos un genérico
            $nombreAMostrar = ($remitenteInfo && isset($remitenteInfo->depa)) 
                ? "Depto " . $remitenteInfo->depa 
                : "Usuario " . $remitenteId;

            // 2. Crea el mensaje en la DB
            $mensaje = Mensaje::create([
                'remitente'    => $remitenteId,
                'destinatario' => $destinatarioId,
                'mensaje'      => $request->texto,
                'fecha'        => now(),
            ]);

            // 3. Creamos la notificación
            $notif = Notificacion::create([
                'usuario_id' => $destinatarioId,
                'tipo'       => 'mensaje',
                'titulo'     => 'Mensaje de ' . $nombreAMostrar,
                'mensaje'    => $request->texto,
                'url'        => '/chat',
                'leido'      => false,
            ]);

            // 4. Disparar eventos
            try {
                broadcast(new NuevoMensaje($mensaje));
                broadcast(new NuevaNotificacion($notif));
            } catch (\Exception $e) {
                Log::warning("WebSocket error: " . $e->getMessage());
            }

            return response()->json(['res' => true, 'mensaje' => $mensaje]);

        } catch (\Exception $e) {
            // Esto escribirá el error REAL en storage/logs/laravel.log
            Log::error("Error en enviarMensaje: " . $e->getMessage());
            return response()->json([
                'res' => false, 
                'error' => "Error interno del servidor",
                'detalle' => $e->getMessage() // Esto te dirá el error en la consola de React
            ], 500);
        }
    }
}