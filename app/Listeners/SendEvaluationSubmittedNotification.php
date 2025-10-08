<?php

namespace App\Listeners;

use App\Events\EvaluationSubmitted;
use App\Models\User;
use App\Notifications\EvaluationSubmittedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Listener: Send Evaluation Submitted Notification
 *
 * Escucha el evento EvaluationSubmitted y notifica a los
 * administradores VIEX que una evaluación ha sido completada.
 */
class SendEvaluationSubmittedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param EvaluationSubmitted $event
     * @return void
     */
    public function handle(EvaluationSubmitted $event): void
    {
        // Obtener todos los usuarios con rol viex_admin
        $viexAdmins = User::role('viex_admin')->get();

        // Notificar a todos los administradores VIEX
        Notification::send(
            $viexAdmins,
            new EvaluationSubmittedNotification($event->work, $event->evaluation)
        );
    }

    /**
     * Handle a job failure.
     *
     * @param EvaluationSubmitted $event
     * @param \Throwable $exception
     * @return void
     */
    public function failed(EvaluationSubmitted $event, \Throwable $exception): void
    {
        Log::error('Failed to send EvaluationSubmitted notification', [
            'work_id' => $event->work->id,
            'evaluation_id' => $event->evaluation->id,
            'evaluator_id' => $event->evaluation->evaluator_user_id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
