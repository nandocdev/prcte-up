<?php

namespace App\Events;

use App\Models\User;
use App\Models\WorkEvaluator;
use App\Models\WorkOfExtension;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event: Evaluador Asignado
 *
 * Se dispara cuando un evaluador es asignado a un trabajo
 * por el administrador VIEX.
 */
class EvaluatorAssigned
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param WorkOfExtension $work El trabajo a evaluar
     * @param WorkEvaluator $workEvaluator El registro de asignación
     * @param User $assignedBy El usuario que hizo la asignación
     */
    public function __construct(
        public WorkOfExtension $work,
        public WorkEvaluator $workEvaluator,
        public User $assignedBy
    ) {
    }
}
