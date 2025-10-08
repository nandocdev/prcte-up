<?php

namespace App\Events;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event: Trabajo Aprobado por VIEX
 *
 * Se dispara cuando VIEX aprueba finalmente un trabajo
 * después de revisar las evaluaciones.
 */
class WorkApprovedByViex
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param WorkOfExtension $work El trabajo aprobado
     * @param User $approver El administrador VIEX que aprueba
     * @param string|null $comments Comentarios de aprobación
     */
    public function __construct(
        public WorkOfExtension $work,
        public User $approver,
        public ?string $comments = null
    ) {
    }
}
