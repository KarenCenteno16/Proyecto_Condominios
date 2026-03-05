<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NuevoReporteEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notificacion;
    public $adminId;

    /**
     * Create a new event instance.
     */
    public function __construct($notificacion, $adminId)
    {
        // CORRECCIÓN: Se agregó el $ antes de this
        $this->notificacion = $notificacion;
        $this->adminId = $adminId;
    }

    /**
     * El canal en el que el evento debe transmitir.
     */
    public function broadcastOn()
    {
        // El admin escucha en su canal personalizado: notificaciones-user-ID
        return new Channel('notificaciones-user-' . $this->adminId);
    }

    /**
     * El nombre del evento que React (Echo) está esperando.
     */
    public function broadcastAs()
    {
        return 'NuevaNotificacion';
    }
}