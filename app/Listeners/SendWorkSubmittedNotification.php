<?php

namespace App\Listeners;

use App\Events\WorkSubmitted;
use App\Notifications\WorkSubmittedForReview;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\Models\WorkStatusHistory;

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
                try {
                    // Enviar notificación al coordinador
                    $coordinator->notify(new WorkSubmittedForReview($work));

                    Log::info('Notificación enviada exitosamente', [
                        'work_id' => $work->getKey(),
                        'coordinator_id' => $coordinator->getKey(),
                        'coordinator_name' => $coordinator->name
                    ]);

                    // Registrar trazabilidad en historial (opcional: puede ajustarse a una tabla específica)
                    WorkStatusHistory::create([
                        'work_of_extension_id' => $work->getKey(),
                        'from_status_id' => $work->getAttribute('current_status_id'),
                        'to_status_id' => $work->getAttribute('current_status_id'),
                        'changed_by_user_id' => $submittedBy->getKey(),
                        'comments' => 'Notificaci\u00f3n enviada al coordinador (ID: ' . $coordinator->getKey() . ')'
                    ]);

                } catch (\Exception $e) {
                    Log::error('Error al enviar notificación al coordinador', [
                        'work_id' => $work->getKey(),
                        'coordinator_id' => $coordinator->getKey(),
                        'error' => $e->getMessage()
                    ]);

                    // Re-lanzar para que la cola pueda reintentar
                    throw $e;
                }
            } else {
                Log::warning('No se encontró coordinador para la unidad organizacional', [
                    'work_id' => $work->getKey(),
                    'organizational_unit_id' => $work->getAttribute('organizational_unit_id')
                ]);

                // Fallback: notificar a super_admins
                $admins = User::role('super_admin')->get();

                if ($admins->isNotEmpty()) {
                    foreach ($admins as $admin) {
                        try {
                            $admin->notify(new WorkSubmittedForReview($work));

                            WorkStatusHistory::create([
                                'work_of_extension_id' => $work->getKey(),
                                'from_status_id' => $work->getAttribute('current_status_id'),
                                'to_status_id' => $work->getAttribute('current_status_id'),
                                'changed_by_user_id' => $submittedBy->getKey(),
                                'comments' => 'Fallback: notificación enviada a super_admin (ID: ' . $admin->getKey() . ')'
                            ]);

                            Log::info('Fallback: notificación enviada a super_admin', [
                                'work_id' => $work->getKey(),
                                'admin_id' => $admin->getKey()
                            ]);

                        } catch (\Exception $e) {
                            Log::error('Error al enviar fallback de notificación a super_admin', [
                                'work_id' => $work->getKey(),
                                'admin_id' => $admin->getKey(),
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                } else {
                    Log::error('No hay super_admins configurados para fallback de notificación', [
                        'work_id' => $work->getKey()
                    ]);
                }
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
