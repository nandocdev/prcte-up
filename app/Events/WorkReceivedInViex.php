<?php

namespace App\Events;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event: Trabajo Recibido en VIEX
 *
 * Se dispara cuando un trabajo es recibido en VIEX desde
 * el Decano/Director para iniciar el proceso de evaluación.
 */
class WorkReceivedInViex
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param WorkOfExtension $work El trabajo recibido
     * @param User $viexAdmin El administrador VIEX que recibe
     */
    public function __construct(
        public WorkOfExtension $work,
        public User $viexAdmin
    ) {
    }
}
