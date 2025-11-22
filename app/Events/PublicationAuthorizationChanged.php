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
 * Evento disparado cuando cambia la autorización de publicación de un trabajo
 * CU06: Autorizar Publicación de Resultados - componente de evento
 */
class PublicationAuthorizationChanged {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public WorkOfExtension $work;
    public User $changedBy;
    public bool $isAuthorized;

    /**
     * Create a new event instance.
     *
     * @param WorkOfExtension $work El trabajo cuya autorización cambió
     * @param User $changedBy Usuario que realizó el cambio
     * @param bool $isAuthorized True si se autorizó, false si se revocó
     */
    public function __construct(WorkOfExtension $work, User $changedBy, bool $isAuthorized)
    {
        $this->work = $work;
        $this->changedBy = $changedBy;
        $this->isAuthorized = $isAuthorized;
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