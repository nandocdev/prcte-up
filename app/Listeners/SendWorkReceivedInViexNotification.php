<?php

namespace App\Listeners;

use App\Events\WorkReceivedInViex;
use App\Models\User;
use App\Notifications\WorkReceivedInViexNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Listener: Send Work Received In Viex Notification
 *
 * Escucha el evento WorkReceivedInViex y notifica a todos
 * los administradores VIEX sobre el nuevo trabajo recibido.
 */
class SendWorkReceivedInViexNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param WorkReceivedInViex $event
     * @return void
     */
    public function handle(WorkReceivedInViex $event): void
    {
        // Obtener todos los usuarios con rol viex_admin
        $viexAdmins = User::role('viex_admin')->get();

        // Notificar a todos los administradores VIEX
        Notification::send(
            $viexAdmins,
            new WorkReceivedInViexNotification($event->work, $event->viexAdmin)
        );
    }

    /**
     * Handle a job failure.
     *
     * @param WorkReceivedInViex $event
     * @param \Throwable $exception
     * @return void
     */
    public function failed(WorkReceivedInViex $event, \Throwable $exception): void
    {
        Log::error('Failed to send WorkReceivedInViex notification', [
            'work_id' => $event->work->id,
            'viex_admin_id' => $event->viexAdmin->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
