<?php

namespace App\Listeners;

use App\Events\WorkApprovedByViex;
use App\Notifications\WorkApprovedByViexNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener: Send Work Approved By Viex Notification
 *
 * Escucha el evento WorkApprovedByViex y notifica al profesor
 * responsable que su trabajo ha sido aprobado.
 */
class SendWorkApprovedByViexNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param WorkApprovedByViex $event
     * @return void
     */
    public function handle(WorkApprovedByViex $event): void
    {
        // Obtener el usuario responsable del trabajo
        $responsibleUser = $event->work->responsibleUser;

        // Notificar al profesor responsable
        $responsibleUser->notify(
            new WorkApprovedByViexNotification(
                $event->work,
                $event->approver,
                $event->comments
            )
        );

        // También notificar a todos los participantes del trabajo
        foreach ($event->work->participants as $participant) {
            if ($participant->user_id !== $responsibleUser->id) {
                $participant->user->notify(
                    new WorkApprovedByViexNotification(
                        $event->work,
                        $event->approver,
                        $event->comments
                    )
                );
            }
        }
    }

    /**
     * Handle a job failure.
     *
     * @param WorkApprovedByViex $event
     * @param \Throwable $exception
     * @return void
     */
    public function failed(WorkApprovedByViex $event, \Throwable $exception): void
    {
        Log::error('Failed to send WorkApprovedByViex notification', [
            'work_id' => $event->work->id,
            'approver_id' => $event->approver->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
