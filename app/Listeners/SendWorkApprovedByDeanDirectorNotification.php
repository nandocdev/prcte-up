<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\WorkApprovedByDeanDirector;
use App\Notifications\WorkApprovedByDeanDirectorNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener: Enviar Notificación de Aprobación por Decano/Director
 *
 * Escucha el evento WorkApprovedByDeanDirector y envía notificaciones por email
 * y base de datos a los destinatarios relevantes.
 *
 * @package App\Listeners
 */
class SendWorkApprovedByDeanDirectorNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Manejar el evento.
     *
     * @param WorkApprovedByDeanDirector $event El evento disparado
     * @return void
     */
    public function handle(WorkApprovedByDeanDirector $event): void
    {
        $work = $event->work;
        $deanDirector = $event->deanDirector;
        $comments = $event->comments;

        // Eager loading de relaciones necesarias
        $work->load(['organizationalUnit.parent', 'responsibleUser']);

        Log::info('Procesando notificaciones de aprobación por decano/director', [
            'work_id' => $work->getKey(),
            'work_title' => $work->getAttribute('title'),
            'dean_director_id' => $deanDirector->getKey(),
            'dean_director_name' => $deanDirector->getAttribute('name'),
        ]);

        // 1. Notificar al Equipo VIEX
        $viexUsers = $this->findViexUsers();
        foreach ($viexUsers as $viexUser) {
            $viexUser->notify(new WorkApprovedByDeanDirectorNotification($work, $comments, 'viex'));
            Log::info('Notificación enviada a usuario VIEX', [
                'work_id' => $work->getKey(),
                'viex_user_id' => $viexUser->getKey(),
                'viex_user_name' => $viexUser->getAttribute('name'),
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
            $professor->notify(new WorkApprovedByDeanDirectorNotification($work, $comments, 'professor'));
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
     * Buscar usuarios con rol VIEX.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\User>
     */
    private function findViexUsers()
    {
        return \App\Models\User::role('viex')->get();
    }

    /**
     * Manejar un fallo del listener.
     *
     * @param WorkApprovedByDeanDirector $event El evento que falló
     * @param \Throwable $exception La excepción lanzada
     * @return void
     */
    public function failed(WorkApprovedByDeanDirector $event, \Throwable $exception): void
    {
        Log::error('Error al enviar notificaciones de aprobación por decano/director', [
            'work_id' => $event->work->getKey(),
            'dean_director_id' => $event->deanDirector->getKey(),
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}