<?php

namespace App\Events;

use App\Models\WorkOfExtension;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento disparado cuando un trabajo de extensión es enviado para revisión
 * CU04: Enviar trabajo a coordinador - componente de evento
 */
class WorkSubmitted {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public WorkOfExtension $work;
    public User $submittedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(WorkOfExtension $work, User $submittedBy) {
        $this->work = $work;
        $this->submittedBy = $submittedBy;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
