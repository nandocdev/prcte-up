<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\WorkChangesRequestedByViex;
use App\Notifications\WorkChangesRequestedByViexNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener: Enviar Notificación de Correcciones Solicitadas por VIEX
 *
 * Escucha el evento WorkChangesRequestedByViex y envía notificaciones
 * al profesor responsable informándole de las correcciones requeridas.
 *
 * @package App\Listeners
 */
class SendWorkChangesRequestedByViexNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Manejar el evento.
     *
     * @param WorkChangesRequestedByViex $event El evento disparado
     * @return void
     */
    public function handle(WorkChangesRequestedByViex $event): void
    {
        $work = $event->work;
        $viexAdmin = $event->viexAdmin;
        $comments = $event->comments;

        // Eager loading de relaciones necesarias
        $work->load(['responsibleUser', 'organizationalUnit']);

        Log::info('Procesando notificaciones de correcciones solicitadas por VIEX', [
            'work_id' => $work->getKey(),
            'work_title' => $work->getAttribute('title'),
            'viex_admin_id' => $viexAdmin->getKey(),
            'viex_admin_name' => $viexAdmin->getAttribute('name'),
        ]);

        // Notificar al Profesor Responsable
        $professor = $work->responsibleUser;
        if ($professor) {
            $professor->notify(new WorkChangesRequestedByViexNotification(
                $work,
                $comments,
                $viexAdmin
            ));

            Log::info('Notificación de correcciones enviada a Profesor Responsable', [
                'work_id' => $work->getKey(),
                'professor_id' => $professor->getKey(),
                'professor_name' => $professor->getAttribute('name'),
                'comments_length' => strlen($comments),
            ]);
        } else {
            Log::error('No se encontró profesor responsable para notificar de correcciones VIEX', [
                'work_id' => $work->getKey(),
            ]);
        }
    }

    /**
     * Manejar un fallo del listener.
     *
     * @param WorkChangesRequestedByViex $event El evento que falló
     * @param \Throwable $exception La excepción lanzada
     * @return void
     */
    public function failed(WorkChangesRequestedByViex $event, \Throwable $exception): void
    {
        Log::error('Error al enviar notificaciones de correcciones VIEX', [
            'work_id' => $event->work->getKey(),
            'viex_admin_id' => $event->viexAdmin->getKey(),
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}