<?php

namespace App\Services\WorkOfExtension;

use App\Models\WorkOfExtension;
use App\Models\User;
use App\Models\WorkStatus;
use App\Events\WorkApprovedByCoordinator;
use App\Events\WorkApprovedByDeanDirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para manejar aprobaciones de trabajos de extensión
 * Maneja tanto aprobaciones de coordinador como de decano/director
 */
class ApproveWorkService
{
    /**
     * Aprobar trabajo por coordinador y enviarlo a Decano/Director
     * CU08: Avalar y remitir a Decano/Director
     */
    public function approveByCoordinator(WorkOfExtension $work, User $coordinator, ?string $comments = null): WorkOfExtension
    {
        DB::beginTransaction();

        try {
            // Estados válidos: Enviado a Coordinador o En Revisión Coordinador
            $validStatuses = ['Enviado a Coordinador', 'En Revisión Coordinador'];
            $currentStatusName = $work->currentStatus->getAttribute('name');

            if (!in_array($currentStatusName, $validStatuses)) {
                throw new \InvalidArgumentException(
                    "El trabajo no está en el estado correcto para ser aprobado. Estado actual: {$currentStatusName}"
                );
            }

            // Cambiar a estado "Enviado a Decano/Director"
            $approvedStatus = WorkStatus::where('name', 'Enviado a Decano/Director')->firstOrFail();

            $work->changeStatus($approvedStatus, $coordinator, $comments ?? 'Trabajo aprobado por el coordinador de extensión.');

            Log::info('Trabajo aprobado por coordinador', [
                'work_id' => $work->getKey(),
                'coordinator_id' => $coordinator->getKey(),
                'new_status' => 'Enviado a Decano/Director'
            ]);

            // Disparar evento para notificar al Decano/Director y Profesor
            WorkApprovedByCoordinator::dispatch($work, $coordinator, $comments);

            DB::commit();

            return $work->fresh();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al aprobar trabajo por coordinador', [
                'work_id' => $work->getKey(),
                'coordinator_id' => $coordinator->getKey(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Aprobar trabajo por decano/director y enviarlo a VIEX
     * CU11: Aprobar y tramitar a VIEX
     */
    public function approveByDeanDirector(WorkOfExtension $work, User $deanDirector, ?string $comments = null): WorkOfExtension
    {
        DB::beginTransaction();

        try {
            if ($work->currentStatus->getAttribute('name') !== 'Enviado a Decano/Director') {
                throw new \InvalidArgumentException('El trabajo no está en el estado correcto para ser aprobado por el decano/director.');
            }

            // Cambiar a estado "Enviado a VIEX"
            $approvedStatus = WorkStatus::where('name', 'Enviado a VIEX')->firstOrFail();

            $work->changeStatus($approvedStatus, $deanDirector, $comments ?? 'Trabajo aprobado por el decano/director.');

            Log::info('Trabajo aprobado por decano/director', [
                'work_id' => $work->getKey(),
                'dean_director_id' => $deanDirector->getKey(),
                'new_status' => 'Enviado a VIEX'
            ]);

            // TODO: Disparar evento para notificar a VIEX
            WorkApprovedByDeanDirector::dispatch($work, $deanDirector, $comments);

            DB::commit();

            return $work->fresh();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al aprobar trabajo por decano/director', [
                'work_id' => $work->getKey(),
                'dean_director_id' => $deanDirector->getKey(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}