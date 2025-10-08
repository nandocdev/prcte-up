<?php

namespace App\Events;

use App\Models\WorkEvaluation;
use App\Models\WorkOfExtension;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event: Evaluación Enviada
 *
 * Se dispara cuando un evaluador envía su evaluación
 * completa de un trabajo.
 */
class EvaluationSubmitted
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param WorkOfExtension $work El trabajo evaluado
     * @param WorkEvaluation $evaluation La evaluación enviada
     */
    public function __construct(
        public WorkOfExtension $work,
        public WorkEvaluation $evaluation
    ) {
    }
}
