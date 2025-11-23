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
    public function getDashboardData(User $user, array $filters = []): array
    {
        return [
            'pendingWorks' => $this->getPendingWorksForCoordinator($user, $filters),
            'recentWorks' => $this->getRecentWorksForCoordinator($user, $filters),
            'statistics' => $this->getCoordinatorStatistics($user),
            'user' => $user,
            'filters' => $filters,
            'availableFilters' => $this->getAvailableFilters($user)
        ];
    }

    /**
     * Obtener trabajos pendientes de revisión para el coordinador
     */
    public function getPendingWorksForCoordinator(User $user, array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = WorkOfExtension::where('organizational_unit_id', $user->getAttribute('main_organizational_unit_id'))
            ->whereHas('currentStatus', function ($query) {
                $query->whereIn('name', ['En Revisión Coordinador', 'Enviado a Coordinador']);
            })
            ->with(['workType', 'responsibleUser', 'currentStatus']);

        $this->applyFilters($query, $filters);

        return $query->orderBy('submitted_at', 'asc')->get();
    }

    /**
     * Obtener trabajos recientes procesados por el coordinador
     */
    public function getRecentWorksForCoordinator(User $user, array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = WorkOfExtension::where('organizational_unit_id', $user->getAttribute('main_organizational_unit_id'))
            ->whereHas('statusHistory', function ($query) use ($user) {
                $query->where('changed_by_user_id', $user->getKey());
            })
            ->with(['workType', 'responsibleUser', 'currentStatus']);

        $this->applyFilters($query, $filters);

        return $query->orderBy('updated_at', 'desc')->limit(10)->get();
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

    /**
     * Verificar si el trabajo puede ser rechazado
     */
    public function canRejectWork(WorkOfExtension $work): bool
    {
        return $this->authorizationService->canCoordinatorRejectWork($work);
    }

    /**
     * Aplicar filtros a la consulta de trabajos
     */
    private function applyFilters(\Illuminate\Database\Eloquent\Builder $query, array $filters): void
    {
        // Filtro por tipo de trabajo
        if (!empty($filters['work_type_id'])) {
            $query->where('work_type_id', $filters['work_type_id']);
        }

        // Filtro por profesor
        if (!empty($filters['professor_id'])) {
            $query->where('responsible_user_id', $filters['professor_id']);
        }

        // Filtro por fecha desde
        if (!empty($filters['date_from'])) {
            $query->where('submitted_at', '>=', $filters['date_from'] . ' 00:00:00');
        }

        // Filtro por fecha hasta
        if (!empty($filters['date_to'])) {
            $query->where('submitted_at', '<=', $filters['date_to'] . ' 23:59:59');
        }

        // Filtro por estado (solo para trabajos recientes)
        if (!empty($filters['status'])) {
            $query->whereHas('currentStatus', function ($q) use ($filters) {
                $q->where('name', $filters['status']);
            });
        }

        // Búsqueda por título
        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }
    }

    /**
     * Obtener opciones disponibles para los filtros
     */
    public function getAvailableFilters(User $user): array
    {
        $unitId = $user->getAttribute('main_organizational_unit_id');

        return [
            'work_types' => \App\Models\WorkType::where('is_active', true)->get(),
            'professors' => User::whereHas('workOfExtensions', function ($query) use ($unitId) {
                $query->where('organizational_unit_id', $unitId);
            })->distinct()->get(['id', 'name']),
            'statuses' => \App\Models\WorkStatus::whereIn('name', [
                'En Revisión Coordinador',
                'Enviado a Coordinador',
                'Enviado a Decano/Director',
                'Requiere Subsanaciones',
                'Certificado'
            ])->get()
        ];
    }
}