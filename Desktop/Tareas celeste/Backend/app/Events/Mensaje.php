<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // Usar Now para que sea instantáneo
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Mensaje implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $texto;

    public function __construct($texto)
    {
        $this->texto = $texto;
    }

    public function broadcastOn(): array
    {
        // Canal donde React escucha
        return [new Channel('chat-canal')];
    }

    public function broadcastAs(): string
    {
        //  evento para Laravel Echo
        return 'NuevoMensaje';
    }

    
}

