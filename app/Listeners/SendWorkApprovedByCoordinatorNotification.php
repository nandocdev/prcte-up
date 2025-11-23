<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\WorkApprovedByCoordinator;
use App\Notifications\WorkApprovedByCoordinatorNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener: Enviar Notificación de Aprobación por Coordinador
 *
 * Escucha el evento WorkApprovedByCoordinator y envía notificaciones por email
 * y base de datos a los destinatarios relevantes.
 *
 * @package App\Listeners
 */
class SendWorkApprovedByCoordinatorNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Manejar el evento.
     *
     * @param WorkApprovedByCoordinator $event El evento disparado
     * @return void
     */
    public function handle(WorkApprovedByCoordinator $event): void
    {
        $work = $event->work;
        $coordinator = $event->coordinator;
        $comments = $event->comments;

        // Eager loading de relaciones necesarias
        $work->load(['organizationalUnit.parent', 'responsibleUser']);

        Log::info('Procesando notificaciones de aprobación por coordinador', [
            'work_id' => $work->getKey(),
            'work_title' => $work->getAttribute('title'),
            'coordinator_id' => $coordinator->getKey(),
            'coordinator_name' => $coordinator->getAttribute('name'),
        ]);

        // 1. Notificar a usuarios VIEX
        $viexUsers = $this->findViexUsers();
        foreach ($viexUsers as $viexUser) {
            $viexUser->notify(new WorkApprovedByCoordinatorNotification($work, $comments, 'viex'));
            Log::info('Notificación enviada a usuario VIEX', [
                'work_id' => $work->getKey(),
                'viex_id' => $viexUser->getKey(),
                'viex_name' => $viexUser->getAttribute('name'),
            ]);
        }

        if ($viexUsers->isEmpty()) {
            Log::warning('No se encontraron usuarios VIEX para notificar', [
                'work_id' => $work->getKey(),
            ]);
        }

        // 2. Notificar al Profesor Responsable
        $professor = $work->responsibleUser;
        if ($professor) {
            $professor->notify(new WorkApprovedByCoordinatorNotification($work, $comments, 'professor'));
            Log::info('Notificación enviada a Profesor Responsable', [
                'work_id' => $work->getKey(),
                'professor_id' => $professor->getKey(),
                'professor_name' => $professor->getAttribute('name'),
            ]);
        } else {
            Log::error('No se encontró profesor responsable para notificar', [
                'work_id' => $work->getKey(),
            ]);
        }
    }

    /**
     * Buscar usuarios VIEX para notificar.
     *
     * Busca todos los usuarios con rol 'viex' que estén activos.
     *
     * @return \Illuminate\Database\Eloquent\Collection Usuarios VIEX
     */
    private function findViexUsers()
    {
        return \App\Models\User::role('viex')->active()->get();
    }

    /**
     * Manejar un fallo del listener.
     *
     * @param WorkApprovedByCoordinator $event El evento que falló
     * @param \Throwable $exception La excepción lanzada
     * @return void
     */
    public function failed(WorkApprovedByCoordinator $event, \Throwable $exception): void
    {
        Log::error('Error al enviar notificaciones de aprobación por coordinador', [
            'work_id' => $event->work->getKey(),
            'coordinator_id' => $event->coordinator->getKey(),
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
