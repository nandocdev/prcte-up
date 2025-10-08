<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\WorkChangesRequestedByCoordinator;
use App\Notifications\WorkChangesRequestedByCoordinatorNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener: Enviar Notificación de Subsanaciones Solicitadas
 *
 * Escucha el evento WorkChangesRequestedByCoordinator y envía notificaciones
 * al profesor responsable informándole de las subsanaciones requeridas.
 *
 * @package App\Listeners
 */
class SendWorkChangesRequestedByCoordinatorNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Manejar el evento.
     *
     * @param WorkChangesRequestedByCoordinator $event El evento disparado
     * @return void
     */
    public function handle(WorkChangesRequestedByCoordinator $event): void
    {
        $work = $event->work;
        $coordinator = $event->coordinator;
        $comments = $event->comments;

        // Eager loading de relaciones necesarias
        $work->load(['responsibleUser', 'organizationalUnit']);

        Log::info('Procesando notificaciones de subsanaciones solicitadas', [
            'work_id' => $work->getKey(),
            'work_title' => $work->getAttribute('title'),
            'coordinator_id' => $coordinator->getKey(),
            'coordinator_name' => $coordinator->getAttribute('name'),
        ]);

        // Notificar al Profesor Responsable
        $professor = $work->responsibleUser;
        if ($professor) {
            $professor->notify(new WorkChangesRequestedByCoordinatorNotification(
                $work,
                $comments,
                $coordinator
            ));

            Log::info('Notificación de subsanaciones enviada a Profesor Responsable', [
                'work_id' => $work->getKey(),
                'professor_id' => $professor->getKey(),
                'professor_name' => $professor->getAttribute('name'),
                'comments_length' => strlen($comments),
            ]);
        } else {
            Log::error('No se encontró profesor responsable para notificar de subsanaciones', [
                'work_id' => $work->getKey(),
            ]);
        }
    }

    /**
     * Manejar un fallo del listener.
     *
     * @param WorkChangesRequestedByCoordinator $event El evento que falló
     * @param \Throwable $exception La excepción lanzada
     * @return void
     */
    public function failed(WorkChangesRequestedByCoordinator $event, \Throwable $exception): void
    {
        Log::error('Error al enviar notificaciones de subsanaciones', [
            'work_id' => $event->work->getKey(),
            'coordinator_id' => $event->coordinator->getKey(),
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
