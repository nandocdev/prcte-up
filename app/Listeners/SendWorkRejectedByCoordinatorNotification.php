<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\WorkRejectedByCoordinator;
use App\Notifications\WorkRejectedByCoordinatorNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener: Enviar Notificación de Rechazo por Coordinador
 *
 * Escucha el evento WorkRejectedByCoordinator y envía notificaciones
 * al profesor responsable informándole del rechazo y las razones.
 *
 * @package App\Listeners
 */
class SendWorkRejectedByCoordinatorNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Manejar el evento.
     *
     * @param WorkRejectedByCoordinator $event El evento disparado
     * @return void
     */
    public function handle(WorkRejectedByCoordinator $event): void
    {
        $work = $event->work;
        $coordinator = $event->coordinator;
        $reason = $event->reason;

        // Eager loading de relaciones necesarias
        $work->load(['responsibleUser', 'organizationalUnit']);

        Log::info('Procesando notificaciones de rechazo por coordinador', [
            'work_id' => $work->getKey(),
            'work_title' => $work->getAttribute('title'),
            'coordinator_id' => $coordinator->getKey(),
            'coordinator_name' => $coordinator->getAttribute('name'),
        ]);

        // Notificar al Profesor Responsable
        $professor = $work->responsibleUser;
        if ($professor) {
            $professor->notify(new WorkRejectedByCoordinatorNotification(
                $work,
                $reason,
                $coordinator
            ));

            Log::info('Notificación de rechazo enviada a Profesor Responsable', [
                'work_id' => $work->getKey(),
                'professor_id' => $professor->getKey(),
                'professor_name' => $professor->getAttribute('name'),
                'reason_length' => strlen($reason),
            ]);
        } else {
            Log::error('No se encontró profesor responsable para notificar de rechazo', [
                'work_id' => $work->getKey(),
            ]);
        }
    }

    /**
     * Manejar un fallo del listener.
     *
     * @param WorkRejectedByCoordinator $event El evento que falló
     * @param \Throwable $exception La excepción lanzada
     * @return void
     */
    public function failed(WorkRejectedByCoordinator $event, \Throwable $exception): void
    {
        Log::error('Error al enviar notificaciones de rechazo por coordinador', [
            'work_id' => $event->work->getKey(),
            'coordinator_id' => $event->coordinator->getKey(),
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
