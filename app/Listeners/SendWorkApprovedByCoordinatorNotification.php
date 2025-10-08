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

        // 1. Notificar al Decano/Director
        $dean = $this->findDean($work);
        if ($dean) {
            $dean->notify(new WorkApprovedByCoordinatorNotification($work, $comments, 'dean'));
            Log::info('Notificación enviada a Decano/Director', [
                'work_id' => $work->getKey(),
                'dean_id' => $dean->getKey(),
                'dean_name' => $dean->getAttribute('name'),
            ]);
        } else {
            Log::warning('No se encontró Decano/Director para notificar', [
                'work_id' => $work->getKey(),
                'organizational_unit_id' => $work->getAttribute('organizational_unit_id'),
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
     * Buscar al Decano/Director de la unidad organizacional del trabajo.
     *
     * Busca primero en la unidad organizacional padre. Si no hay usuario
     * con rol 'decano_director', busca en la propia unidad.
     *
     * @param \App\Models\WorkOfExtension $work El trabajo aprobado
     * @return \App\Models\User|null El Decano/Director o null si no se encuentra
     */
    private function findDean($work): ?\App\Models\User
    {
        $organizationalUnit = $work->organizationalUnit;

        if (!$organizationalUnit) {
            return null;
        }

        // Intentar buscar en la unidad padre (Facultad)
        $parentUnit = $organizationalUnit->parent;
        if ($parentUnit) {
            $dean = $parentUnit->users()->role('decano_director')->first();
            if ($dean) {
                return $dean;
            }
        }

        // Si no hay usuario en la unidad padre, buscar en la unidad actual
        return $organizationalUnit->users()->role('decano_director')->first();
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
