<?php

namespace App\Listeners;

use App\Events\WorkPublicationAuthorized;
use App\Notifications\WorkPublicationAuthorizedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Listener que envía notificación a VIEX cuando se autoriza publicación
 * CU06: Autorizar Publicación de Resultados
 */
class SendPublicationAuthorizedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WorkPublicationAuthorized $event): void
    {
        $work = $event->work;
        $authorizedBy = $event->authorizedBy;
        $isAuthorized = $event->isAuthorized;

        Log::info($isAuthorized ? 'Procesando notificación de autorización de publicación' : 'Procesando notificación de revocación de publicación', [
            'work_id' => $work->getKey(),
            'authorized_by' => $authorizedBy->getKey(),
            'is_authorized' => $isAuthorized
        ]);

        try {
            // Obtener email de VIEX desde configuración
            $viexEmail = config('work_types.viex_projects_email', 'viexproyectos@up.ac.pa');

            // Enviar notificación anónima al email de VIEX
            Notification::route('mail', $viexEmail)
                ->notify(new WorkPublicationAuthorizedNotification($work, $isAuthorized));

            Log::info('Notificación de autorización enviada a VIEX', [
                'work_id' => $work->getKey(),
                'viex_email' => $viexEmail,
                'is_authorized' => $isAuthorized
            ]);

        } catch (\Exception $e) {
            Log::error('Error al enviar notificación de autorización a VIEX', [
                'work_id' => $work->getKey(),
                'error' => $e->getMessage()
            ]);

            // Re-lanzar excepción para que la cola pueda reintentar
            throw $e;
        }
    }
}
