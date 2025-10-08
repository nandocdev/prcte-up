<?php

namespace App\Listeners;

use App\Events\WorkRejectedByViex;
use App\Notifications\WorkRejectedByViexNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener: Send Work Rejected By Viex Notification
 *
 * Escucha el evento WorkRejectedByViex y notifica al profesor
 * responsable que su trabajo no ha sido aprobado.
 */
class SendWorkRejectedByViexNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param WorkRejectedByViex $event
     * @return void
     */
    public function handle(WorkRejectedByViex $event): void
    {
        // Obtener el usuario responsable del trabajo
        $responsibleUser = $event->work->responsibleUser;

        // Notificar al profesor responsable
        $responsibleUser->notify(
            new WorkRejectedByViexNotification(
                $event->work,
                $event->rejector,
                $event->reason
            )
        );

        // También notificar a todos los participantes del trabajo
        foreach ($event->work->participants as $participant) {
            if ($participant->user_id !== $responsibleUser->id) {
                $participant->user->notify(
                    new WorkRejectedByViexNotification(
                        $event->work,
                        $event->rejector,
                        $event->reason
                    )
                );
            }
        }
    }

    /**
     * Handle a job failure.
     *
     * @param WorkRejectedByViex $event
     * @param \Throwable $exception
     * @return void
     */
    public function failed(WorkRejectedByViex $event, \Throwable $exception): void
    {
        Log::error('Failed to send WorkRejectedByViex notification', [
            'work_id' => $event->work->id,
            'rejector_id' => $event->rejector->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
