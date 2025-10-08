<?php

namespace App\Listeners;

use App\Events\WorkSubmitted;
use App\Notifications\WorkSubmittedForReview;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener que envía notificación al coordinador cuando se submite un trabajo
 * CU04: Enviar trabajo a coordinador - componente de listener
 */
class SendWorkSubmittedNotification implements ShouldQueue {
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WorkSubmitted $event): void {
        $work = $event->work;
        $submittedBy = $event->submittedBy;

        Log::info('Procesando notificación de trabajo enviado', [
            'work_id' => $work->getKey(),
            'submitted_by' => $submittedBy->getKey(),
            'organizational_unit_id' => $work->getAttribute('organizational_unit_id')
        ]);

        try {
            // Encontrar el coordinador de extensión de la unidad organizacional
            $coordinator = $this->findCoordinator($work);

            if ($coordinator) {
                // Enviar notificación al coordinador
                $coordinator->notify(new WorkSubmittedForReview($work));

                Log::info('Notificación enviada exitosamente', [
                    'work_id' => $work->getKey(),
                    'coordinator_id' => $coordinator->getKey(),
                    'coordinator_name' => $coordinator->name
                ]);
            } else {
                Log::warning('No se encontró coordinador para la unidad organizacional', [
                    'work_id' => $work->getKey(),
                    'organizational_unit_id' => $work->getAttribute('organizational_unit_id')
                ]);

                // TODO: Implementar fallback - enviar notificación a administrador
            }

        } catch (\Exception $e) {
            Log::error('Error al enviar notificación de trabajo enviado', [
                'work_id' => $work->getKey(),
                'error' => $e->getMessage()
            ]);

            // Re-lanzar excepción para que la cola pueda reintentar
            throw $e;
        }
    }        /**
             * Encontrar el coordinador de extensión para una unidad organizacional
             */
    private function findCoordinator($work): ?User {
        return User::whereHas('roles', function ($query) {
            $query->where('name', 'coordinador_extension');
        })
            ->where('main_organizational_unit_id', $work->getAttribute('organizational_unit_id'))
            ->first();
    }
}
