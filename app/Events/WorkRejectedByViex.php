<?php

namespace App\Events;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event: Trabajo Rechazado por VIEX
 *
 * Se dispara cuando VIEX rechaza un trabajo después
 * de revisar las evaluaciones.
 */
class WorkRejectedByViex
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param WorkOfExtension $work El trabajo rechazado
     * @param User $rejector El administrador VIEX que rechaza
     * @param string $reason Motivo del rechazo
     */
    public function __construct(
        public WorkOfExtension $work,
        public User $rejector,
        public string $reason
    ) {
    }
}
