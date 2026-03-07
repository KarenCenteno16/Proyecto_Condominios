<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // Usar Now para evitar colas
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NuevaNotificacion implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notificacion;

    public function __construct($notificacion)
    {
        $this->notificacion = $notificacion;
    }

    public function broadcastOn()
    {
        // IMPORTANTE: Asegúrate de que usuario_id sea un número limpio
        return new Channel('notificaciones-user-' . $this->notificacion->usuario_id);
    }

    public function broadcastAs()
    {
        return 'NuevaNotificacion';
    }
}