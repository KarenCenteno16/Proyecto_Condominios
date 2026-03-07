<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // Usamos Now para envío inmediato
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NuevoMensaje implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $mensaje;

    public function __construct($mensaje)
    {
        // Asegúrate de que $mensaje sea un objeto o un array con los datos
        $this->mensaje = $mensaje;
    }

    public function broadcastOn(): array
    {
        // Canal público donde ambos (Admin y User) están escuchando
        return [new Channel('chat-canal')];
    }

    public function broadcastAs()
    {
        return 'NuevoMensaje'; 
    }

    /**
     * Especificamos exactamente qué datos queremos enviar al frontend
     */
    public function broadcastWith(): array
    {
        return [
            'mensaje' => $this->mensaje
        ];
    }
}