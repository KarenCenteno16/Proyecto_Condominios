<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mensaje;
use App\Models\Usuario;
use App\Events\NuevoMensaje;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    // public function usuariosChat() {
    //     return Usuario::all(); 
    // }

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

            $existe = DB::table('usuarios')->where('id', $destinatarioId)->exists();
            if (!$existe) {
                return response()->json(['res' => false, 'error' => "El usuario destino con ID $destinatarioId no existe."], 400);
            }

            $mensaje = Mensaje::create([
                'remitente'    => $remitenteId,
                'destinatario' => $destinatarioId,
                'mensaje'      => $request->texto,
                'fecha'        => now(),
            ]);

            try {
                broadcast(new NuevoMensaje($mensaje))->toOthers();
            } catch (\Exception $e) {
                Log::warning("WebSocket error: " . $e->getMessage());
            }

            return response()->json(['res' => true, 'mensaje' => $mensaje]);

        } catch (\Exception $e) {
            Log::error("Error Fatal: " . $e->getMessage());
            return response()->json(['res' => false, 'error' => $e->getMessage()], 500);
        }
    }
}