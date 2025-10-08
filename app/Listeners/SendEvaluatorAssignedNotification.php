<?php

namespace App\Listeners;

use App\Events\EvaluatorAssigned;
use App\Notifications\EvaluatorAssignedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener: Send Evaluator Assigned Notification
 *
 * Escucha el evento EvaluatorAssigned y notifica al evaluador
 * que ha sido asignado para evaluar un trabajo.
 */
class SendEvaluatorAssignedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param EvaluatorAssigned $event
     * @return void
     */
    public function handle(EvaluatorAssigned $event): void
    {
        // Obtener el evaluador asignado
        $evaluator = $event->workEvaluator->evaluator;

        // Notificar al evaluador
        $evaluator->notify(
            new EvaluatorAssignedNotification(
                $event->work,
                $event->workEvaluator,
                $event->assignedBy
            )
        );
    }

    /**
     * Handle a job failure.
     *
     * @param EvaluatorAssigned $event
     * @param \Throwable $exception
     * @return void
     */
    public function failed(EvaluatorAssigned $event, \Throwable $exception): void
    {
        Log::error('Failed to send EvaluatorAssigned notification', [
            'work_id' => $event->work->id,
            'evaluator_id' => $event->workEvaluator->evaluator_user_id,
            'assigned_by_id' => $event->assignedBy->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
