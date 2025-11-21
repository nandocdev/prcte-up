<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\WorkChangesRequestedByDeanDirector;
use App\Models\User;
use App\Notifications\WorkChangesRequestedByDeanDirectorNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener: Enviar Notificación de Correcciones Solicitadas por Decano/Director
 *
 * Escucha el evento WorkChangesRequestedByDeanDirector y envía notificaciones
 * al coordinador informándole de las correcciones requeridas por el decano/director.
 *
 * @package App\Listeners
 */
class SendWorkChangesRequestedByDeanDirectorNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Manejar el evento.
     *
     * @param WorkChangesRequestedByDeanDirector $event El evento disparado
     * @return void
     */
    public function handle(WorkChangesRequestedByDeanDirector $event): void
    {
        $work = $event->work;
        $deanDirector = $event->deanDirector;
        $comments = $event->comments;

        // Eager loading de relaciones necesarias
        $work->load(['responsibleUser', 'organizationalUnit']);

        Log::info('Procesando notificaciones de correcciones solicitadas por decano/director', [
            'work_id' => $work->getKey(),
            'work_title' => $work->getAttribute('title'),
            'dean_director_id' => $deanDirector->getKey(),
            'dean_director_name' => $deanDirector->getAttribute('name'),
        ]);

        // Notificar al Coordinador de la unidad
        $coordinator = $this->findCoordinator($work);
        if ($coordinator) {
            $coordinator->notify(new WorkChangesRequestedByDeanDirectorNotification(
                $work,
                $comments,
                $deanDirector
            ));

            Log::info('Notificación de correcciones enviada a Coordinador', [
                'work_id' => $work->getKey(),
                'coordinator_id' => $coordinator->getKey(),
                'coordinator_name' => $coordinator->getAttribute('name'),
                'comments_length' => strlen($comments),
            ]);
        } else {
            Log::error('No se encontró coordinador para notificar de correcciones solicitadas por decano/director', [
                'work_id' => $work->getKey(),
                'organizational_unit_id' => $work->getAttribute('organizational_unit_id'),
            ]);
        }
    }

    /**
     * Manejar un fallo del listener.
     *
     * @param WorkChangesRequestedByDeanDirector $event El evento que falló
     * @param \Throwable $exception La excepción lanzada
     * @return void
     */
    public function failed(WorkChangesRequestedByDeanDirector $event, \Throwable $exception): void
    {
        Log::error('Error al enviar notificaciones de correcciones solicitadas por decano/director', [
            'work_id' => $event->work->getKey(),
            'dean_director_id' => $event->deanDirector->getKey(),
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    /**
     * Buscar coordinador de la unidad asociada al trabajo.
     */
    private function findCoordinator($work): ?User
    {
        return User::whereHas('roles', function ($query) {
            $query->where('name', 'coordinador_extension');
        })
            ->where('main_organizational_unit_id', $work->getAttribute('organizational_unit_id'))
            ->first();
    }
}