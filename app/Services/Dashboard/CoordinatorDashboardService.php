<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\WorkOfExtension;
use App\Services\Authorization\WorkAuthorizationService;

/**
 * Servicio para manejar la lógica del dashboard de coordinadores
 * Centraliza queries y cálculos de estadísticas para el dashboard
 */
class CoordinatorDashboardService
{
    private WorkAuthorizationService $authorizationService;

    public function __construct(WorkAuthorizationService $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }

    /**
     * Obtener todos los datos necesarios para el dashboard del coordinador
     */
    public function getDashboardData(User $user): array
    {
        return [
            'pendingWorks' => $this->getPendingWorksForCoordinator($user),
            'recentWorks' => $this->getRecentWorksForCoordinator($user),
            'statistics' => $this->getCoordinatorStatistics($user),
            'user' => $user
        ];
    }

    /**
     * Obtener trabajos pendientes de revisión para el coordinador
     */
    public function getPendingWorksForCoordinator(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return WorkOfExtension::where('organizational_unit_id', $user->getAttribute('main_organizational_unit_id'))
            ->whereHas('currentStatus', function ($query) {
                $query->whereIn('name', ['En Revisión Coordinador', 'Enviado a Coordinador']);
            })
            ->with(['workType', 'responsibleUser', 'currentStatus'])
            ->orderBy('submitted_at', 'asc')
            ->get();
    }

    /**
     * Obtener trabajos recientes procesados por el coordinador
     */
    public function getRecentWorksForCoordinator(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return WorkOfExtension::where('organizational_unit_id', $user->getAttribute('main_organizational_unit_id'))
            ->whereHas('statusHistory', function ($query) use ($user) {
                $query->where('changed_by_user_id', $user->getKey());
            })
            ->with(['workType', 'responsibleUser', 'currentStatus'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtener estadísticas para el dashboard del coordinador
     */
    public function getCoordinatorStatistics(User $user): array
    {
        $unitId = $user->getAttribute('main_organizational_unit_id');

        return [
            'pending' => WorkOfExtension::where('organizational_unit_id', $unitId)
                ->whereHas('currentStatus', function ($query) {
                    $query->where('name', 'En Revisión Coordinador');
                })
                ->count(),

            'approved_this_month' => WorkOfExtension::where('organizational_unit_id', $unitId)
                ->whereHas('statusHistory', function ($query) use ($user) {
                    $query->where('changed_by_user_id', $user->getKey())
                        ->whereHas('toStatus', function ($q) {
                            $q->where('name', 'Enviado a Decano/Director');
                        })
                        ->where('created_at', '>=', now()->startOfMonth());
                })
                ->count(),

            'changes_requested_this_month' => WorkOfExtension::where('organizational_unit_id', $unitId)
                ->whereHas('statusHistory', function ($query) use ($user) {
                    $query->where('changed_by_user_id', $user->getKey())
                        ->whereHas('toStatus', function ($q) {
                            $q->where('name', 'Requiere Subsanaciones');
                        })
                        ->where('created_at', '>=', now()->startOfMonth());
                })
                ->count(),

            'total_unit_works' => WorkOfExtension::where('organizational_unit_id', $unitId)->count()
        ];
    }

    /**
     * Verificar si el coordinador puede revisar un trabajo específico
     */
    public function canCoordinatorReviewWork(User $coordinator, WorkOfExtension $work): bool
    {
        return $this->authorizationService->canCoordinatorReviewWork($coordinator, $work);
    }

    /**
     * Verificar si el trabajo puede ser aprobado
     */
    public function canApproveWork(WorkOfExtension $work): bool
    {
        return $this->authorizationService->canCoordinatorApproveWork($work);
    }

    /**
     * Verificar si se pueden solicitar cambios al trabajo
     */
    public function canRequestChanges(WorkOfExtension $work): bool
    {
        return $this->authorizationService->canCoordinatorRequestChanges($work);
    }
}