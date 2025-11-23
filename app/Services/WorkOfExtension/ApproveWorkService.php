<?php

namespace App\Services\WorkOfExtension;

use App\Models\WorkOfExtension;
use App\Models\User;
use App\Models\WorkStatus;
use App\Events\WorkApprovedByCoordinator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para manejar aprobaciones de trabajos de extensión
 * Maneja aprobaciones de coordinador que envían directamente a VIEX
 */
class ApproveWorkService
{
    /**
     * Aprobar trabajo por coordinador y enviarlo directamente a VIEX
     * CU08: Avalar y remitir directamente a VIEX
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

            // Cambiar directamente a estado "Enviado a VIEX"
            $approvedStatus = WorkStatus::where('name', 'Enviado a VIEX')->firstOrFail();

            $work->changeStatus($approvedStatus, $coordinator, $comments ?? 'Trabajo aprobado por el coordinador de extensión y enviado directamente a VIEX.');

            Log::info('Trabajo aprobado por coordinador y enviado a VIEX', [
                'work_id' => $work->getKey(),
                'coordinator_id' => $coordinator->getKey(),
                'new_status' => 'Enviado a VIEX'
            ]);

            // Disparar evento para notificar a VIEX y Profesor
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
}