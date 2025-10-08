<?php

namespace App\Events;

use App\Models\WorkOfExtension;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento disparado cuando un profesor autoriza/revoca la publicación de su trabajo
 * CU06: Autorizar Publicación de Resultados
 */
class WorkPublicationAuthorized
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public WorkOfExtension $work;
    public User $authorizedBy;
    public bool $isAuthorized;

    /**
     * Create a new event instance.
     *
     * @param WorkOfExtension $work El trabajo
     * @param User $authorizedBy Usuario que autoriza/revoca
     * @param bool $isAuthorized True = autorizado, False = revocado
     */
    public function __construct(WorkOfExtension $work, User $authorizedBy, bool $isAuthorized)
    {
        $this->work = $work;
        $this->authorizedBy = $authorizedBy;
        $this->isAuthorized = $isAuthorized;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
