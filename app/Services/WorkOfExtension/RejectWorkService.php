<?php

namespace App\Services\WorkOfExtension;

use App\Models\WorkOfExtension;
use App\Models\User;
use App\Models\WorkStatus;
use App\Events\WorkRejectedByCoordinator;
use App\Events\WorkRejectedByDeanDirector;
use App\Events\WorkChangesRequestedByCoordinator;
use App\Events\WorkChangesRequestedByDeanDirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para manejar rechazos y solicitudes de cambios de trabajos de extensión
 * Maneja tanto rechazos como solicitudes de correcciones del coordinador y decano
 */
class RejectWorkService
{
    /**
     * Solicitar subsanaciones al profesor desde coordinador
     * CU07: Solicitar subsanaciones al profesor
     */
    public function requestChangesFromCoordinator(WorkOfExtension $work, User $coordinator, string $comments): WorkOfExtension
    {
        DB::beginTransaction();

        try {
            // Estados válidos: Enviado a Coordinador o En Revisión Coordinador
            $validStatuses = ['Enviado a Coordinador', 'En Revisión Coordinador'];
            $currentStatusName = $work->currentStatus->getAttribute('name');

            if (!in_array($currentStatusName, $validStatuses)) {
                throw new \InvalidArgumentException(
                    "El trabajo no está en el estado correcto para solicitar cambios. Estado actual: {$currentStatusName}"
                );
            }

            // Cambiar a estado "Devuelto para Corrección"
            $changesStatus = WorkStatus::where('name', 'Devuelto para Corrección')->firstOrFail();

            $work->changeStatus($changesStatus, $coordinator, $comments);

            // Marcar como borrador para que el profesor pueda editar
            $work->update(['is_draft' => '1']);

            Log::info('Subsanaciones solicitadas por coordinador', [
                'work_id' => $work->getKey(),
                'coordinator_id' => $coordinator->getKey(),
                'new_status' => 'Devuelto para Corrección'
            ]);

            // Disparar evento para notificar al profesor
            WorkChangesRequestedByCoordinator::dispatch($work, $coordinator, $comments);

            DB::commit();

            return $work->fresh();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al solicitar cambios por coordinador', [
                'work_id' => $work->getKey(),
                'coordinator_id' => $coordinator->getKey(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Rechazar trabajo definitivamente desde coordinador
     * CU08: Rechazar trabajo por coordinador
     */
    public function rejectByCoordinator(WorkOfExtension $work, User $coordinator, string $comments): WorkOfExtension
    {
        DB::beginTransaction();

        try {
            $currentStatus = $work->currentStatus->getAttribute('name');

            if (!in_array($currentStatus, ['Enviado a Coordinador', 'En Revisión Coordinador'])) {
                throw new \InvalidArgumentException(
                    "El trabajo no está en el estado correcto para ser rechazado. Estado actual: {$currentStatus}"
                );
            }

            // Cambiar a estado "Rechazado por Coordinador"
            $rejectedStatus = WorkStatus::where('name', 'Rechazado por Coordinador')->firstOrFail();

            $work->changeStatus($rejectedStatus, $coordinator, $comments);

            // Marcar como borrador para que el profesor pueda editar
            $work->update(['is_draft' => '1']);

            Log::info('Trabajo rechazado por coordinador', [
                'work_id' => $work->getKey(),
                'coordinator_id' => $coordinator->getKey(),
                'reason' => $comments
            ]);

            // Disparar evento para notificar al profesor del rechazo
            WorkRejectedByCoordinator::dispatch($work, $coordinator, $comments);

            DB::commit();

            return $work->fresh();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al rechazar trabajo por coordinador', [
                'work_id' => $work->getKey(),
                'coordinator_id' => $coordinator->getKey(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Solicitar correcciones desde decano/director
     * CU10: Devolver trabajo al coordinador con observaciones
     */
    public function requestChangesFromDeanDirector(WorkOfExtension $work, User $deanDirector, string $comments): WorkOfExtension
    {
        DB::beginTransaction();

        try {
            if ($work->currentStatus->getAttribute('name') !== 'Enviado a Decano/Director') {
                throw new \InvalidArgumentException('El trabajo no está en el estado correcto para solicitar correcciones.');
            }

            // Cambiar a estado "Rechazado por Decano/Director"
            $changesStatus = WorkStatus::where('name', 'Rechazado por Decano/Director')->firstOrFail();

            $work->changeStatus($changesStatus, $deanDirector, $comments);

            // Marcar como borrador para que el profesor pueda editar
            $work->update(['is_draft' => '1']);

            Log::info('Correcciones solicitadas por decano/director', [
                'work_id' => $work->getKey(),
                'dean_director_id' => $deanDirector->getKey(),
                'new_status' => 'Rechazado por Decano/Director'
            ]);

            // TODO: Disparar evento para notificar al coordinador
            WorkChangesRequestedByDeanDirector::dispatch($work, $deanDirector, $comments);

            DB::commit();

            return $work->fresh();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al solicitar cambios por decano/director', [
                'work_id' => $work->getKey(),
                'dean_director_id' => $deanDirector->getKey(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Rechazar trabajo definitivamente desde decano/director
     * CU11: Rechazar trabajo por decano/director
     */
    public function rejectByDeanDirector(WorkOfExtension $work, User $deanDirector, string $comments): WorkOfExtension
    {
        DB::beginTransaction();

        try {
            $currentStatus = $work->currentStatus->getAttribute('name');

            if (!in_array($currentStatus, ['Enviado a Decano/Director', 'En Revisión Decano/Director'])) {
                throw new \InvalidArgumentException('El trabajo no está en el estado correcto para ser rechazado por el decano/director.');
            }

            // Cambiar a estado "Rechazado por Decano/Director"
            $rejectedStatus = WorkStatus::where('name', 'Rechazado por Decano/Director')->firstOrFail();

            $work->changeStatus($rejectedStatus, $deanDirector, $comments);

            // Marcar como borrador para que el profesor pueda editar
            $work->update(['is_draft' => '1']);

            Log::info('Trabajo rechazado definitivamente por decano/director', [
                'work_id' => $work->getKey(),
                'dean_director_id' => $deanDirector->getKey(),
                'reason' => $comments
            ]);

            WorkRejectedByDeanDirector::dispatch($work, $deanDirector, $comments);

            DB::commit();

            return $work->fresh();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al rechazar trabajo por decano/director', [
                'work_id' => $work->getKey(),
                'dean_director_id' => $deanDirector->getKey(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}