<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\WorkOfExtension;
use App\Services\Authorization\WorkAuthorizationService;

/**
 * Servicio para manejar la lógica del dashboard de decanos/directores
 * Centraliza queries y cálculos de estadísticas para el dashboard
 */
class DeanDirectorDashboardService
{
    private WorkAuthorizationService $authorizationService;

    public function __construct(WorkAuthorizationService $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }

    /**
     * Obtener todos los datos necesarios para el dashboard del decano/director
     */
    public function getDashboardData(User $user): array
    {
        return [
            'pendingWorks' => $this->getPendingWorksForDeanDirector($user),
            'statistics' => $this->getDeanDirectorStatistics($user),
            'recentWorks' => $this->getRecentWorksProcessedByDeanDirector($user),
            'user' => $user
        ];
    }

    /**
     * Obtener trabajos pendientes de revisión para el decano/director
     */
    public function getPendingWorksForDeanDirector(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return WorkOfExtension::where('organizational_unit_id', $user->getAttribute('main_organizational_unit_id'))
            ->whereHas('currentStatus', function ($query) {
                $query->where('name', 'Enviado a Decano/Director');
            })
            ->with(['workType', 'responsibleUser', 'currentStatus'])
            ->orderBy('updated_at', 'asc') // Más antiguos primero
            ->get();
    }

    /**
     * Obtener trabajos procesados recientemente por el decano/director
     */
    public function getRecentWorksProcessedByDeanDirector(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return WorkOfExtension::where('organizational_unit_id', $user->getAttribute('main_organizational_unit_id'))
            ->whereHas('statusHistory', function ($query) use ($user) {
                $query->where('changed_by_user_id', $user->getKey())
                    ->where('created_at', '>=', now()->subDays(30));
            })
            ->with(['workType', 'responsibleUser', 'currentStatus'])
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Obtener estadísticas para el dashboard del decano/director
     */
    public function getDeanDirectorStatistics(User $user): array
    {
        $unitId = $user->getAttribute('main_organizational_unit_id');

        return [
            'pending' => WorkOfExtension::where('organizational_unit_id', $unitId)
                ->whereHas('currentStatus', function ($query) {
                    $query->where('name', 'Enviado a Decano/Director');
                })
                ->count(),

            'approved_this_month' => WorkOfExtension::where('organizational_unit_id', $unitId)
                ->whereHas('statusHistory', function ($query) use ($user) {
                    $query->where('changed_by_user_id', $user->getKey())
                        ->whereHas('status', function ($statusQuery) {
                            $statusQuery->where('name', 'Enviado a VIEX');
                        })
                        ->where('created_at', '>=', now()->startOfMonth());
                })
                ->count(),

            'changes_requested_this_month' => WorkOfExtension::where('organizational_unit_id', $unitId)
                ->whereHas('statusHistory', function ($query) use ($user) {
                    $query->where('changed_by_user_id', $user->getKey())
                        ->whereHas('status', function ($statusQuery) {
                            $statusQuery->where('name', 'Rechazado por Decano/Director');
                        })
                        ->where('created_at', '>=', now()->startOfMonth());
                })
                ->count(),

            'total_unit_works' => WorkOfExtension::where('organizational_unit_id', $unitId)
                ->whereHas('currentStatus', function ($query) {
                    $query->whereNotIn('name', ['Borrador']);
                })
                ->count(),
        ];
    }

    /**
     * Verificar si el decano/director puede revisar un trabajo específico
     */
    public function canDeanDirectorReviewWork(User $deanDirector, WorkOfExtension $work): bool
    {
        return $this->authorizationService->canDeanDirectorReviewWork($deanDirector, $work);
    }

    /**
     * Verificar si el trabajo puede ser aprobado
     */
    public function canApproveWork(WorkOfExtension $work): bool
    {
        return $this->authorizationService->canDeanDirectorApproveWork($work);
    }

    /**
     * Verificar si se pueden solicitar cambios al trabajo
     */
    public function canRequestChanges(WorkOfExtension $work): bool
    {
        return $this->authorizationService->canDeanDirectorRequestChanges($work);
    }
}