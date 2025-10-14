<?php

namespace App\Listeners;

use App\Events\WorkCertifiedByViex;
use App\Notifications\WorkCertifiedByViexNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener: Send Work Certified By Viex Notification
 *
 * Escucha el evento WorkCertifiedByViex y notifica al profesor
 * responsable que su trabajo ha sido certificado directamente.
 */
class SendWorkCertifiedByViexNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param WorkCertifiedByViex $event
     * @return void
     */
    public function handle(WorkCertifiedByViex $event): void
    {
        // Obtener el usuario responsable del trabajo
        $responsibleUser = $event->work->responsibleUser;

        // Notificar al profesor responsable
        $responsibleUser->notify(
            new WorkCertifiedByViexNotification(
                $event->work,
                $event->certifier,
                $event->comments
            )
        );

        // También notificar a todos los participantes del trabajo
        foreach ($event->work->participants as $participant) {
            if ($participant->user_id !== $responsibleUser->id) {
                $participant->user->notify(
                    new WorkCertifiedByViexNotification(
                        $event->work,
                        $event->certifier,
                        $event->comments
                    )
                );
            }
        }

        Log::info('Notificación de certificación enviada', [
            'work_id' => $event->work->id,
            'certifier_id' => $event->certifier->id,
            'responsible_user_id' => $responsibleUser->id,
        ]);
    }

    /**
     * Handle a job failure.
     *
     * @param WorkCertifiedByViex $event
     * @param \Throwable $exception
     * @return void
     */
    public function failed(WorkCertifiedByViex $event, \Throwable $exception): void
    {
        Log::error('Failed to send WorkCertifiedByViex notification', [
            'work_id' => $event->work->id,
            'certifier_id' => $event->certifier->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}