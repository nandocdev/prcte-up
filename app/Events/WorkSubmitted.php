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
 * CU05: Subsanar trabajo rechazado - reutilizado para reenvíos
 */
class WorkSubmitted {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public WorkOfExtension $work;
    public User $submittedBy;
    public bool $isResubmission;

    /**
     * Create a new event instance.
     *
     * @param WorkOfExtension $work El trabajo enviado/reenviado
     * @param User $submittedBy Usuario que envía
     * @param bool $isResubmission True si es un reenvío después de rechazo
     */
    public function __construct(WorkOfExtension $work, User $submittedBy, bool $isResubmission = false)
    {
        $this->work = $work;
        $this->submittedBy = $submittedBy;
        $this->isResubmission = $isResubmission;
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
