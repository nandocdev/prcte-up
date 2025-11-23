<?php

namespace App\Repositories;

use App\Models\WorkOfExtension;
use App\Models\User;
use App\Models\OrganizationalUnit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Repositorio para consultas complejas de trabajos de extensión
 * Centraliza la lógica de consultas y estadísticas
 */
class WorkOfExtensionRepository
{
    /**
     * Obtener trabajos filtrados según el rol del usuario
     */
    public function getWorksForUser(User $user, array $filters = [], int $perPage = null): Collection|LengthAwarePaginator
    {
        $query = WorkOfExtension::query()
            ->with(['workType', 'currentStatus', 'organizationalUnit', 'responsibleUser'])
            ->orderBy('created_at', 'desc');

        // Aplicar filtros de búsqueda
        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['work_type_id'])) {
            $query->where('work_type_id', $filters['work_type_id']);
        }

        if (!empty($filters['status_id'])) {
            $query->where('current_status_id', $filters['status_id']);
        }

        if (!empty($filters['organizational_unit_id'])) {
            $query->where('organizational_unit_id', $filters['organizational_unit_id']);
        }

        if (!empty($filters['academic_period'])) {
            $query->where('academic_period', $filters['academic_period']);
        }

        if (isset($filters['is_draft'])) {
            $query->where('is_draft', $filters['is_draft']);
        }

        // Reglas de visibilidad centralizadas por rol
        if ($user->hasRole('profesor')) {
            $query->visibleToProfessor($user);
        } elseif ($user->hasRole('coordinador_extension')) {
            $query->visibleToCoordinator($user);
        } elseif ($user->hasRole('viex_admin')) {
            $query->visibleToViex();
        } else {
            // super_admin u otros roles con permisos amplios ven todos los trabajos
            // No aplicamos filtros adicionales
        }

        if ($perPage) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    /**
     * Obtener estadísticas de trabajos para el usuario
     */
    public function getStatisticsForUser(User $user): array
    {
        $works = $this->getWorksForUser($user);

        return [
            'total' => $works->count(),
            'draft' => $works->where('is_draft', '1')->count(),
            'in_review' => $works->whereNotIn('current_status_id', [1, 2])->where('current_status_id', '!=', null)->count(),
            'certified' => $works->where('currentStatus.name', 'Certificado')->count(),
            'rejected' => $works->where('currentStatus.name', 'like', '%Rechazado%')->count(),
            'by_type' => $this->getWorksByType($user),
            'by_status' => $this->getWorksByStatus($user),
            'by_unit' => $this->getWorksByOrganizationalUnit($user),
        ];
    }

    /**
     * Obtener trabajos agrupados por tipo
     */
    public function getWorksByType(User $user): array
    {
        $works = $this->getWorksForUser($user);

        return $works->groupBy('workType.name')->map(function ($group) {
            return $group->count();
        })->toArray();
    }

    /**
     * Obtener trabajos agrupados por estado
     */
    public function getWorksByStatus(User $user): array
    {
        $works = $this->getWorksForUser($user);

        return $works->groupBy('currentStatus.name')->map(function ($group) {
            return $group->count();
        })->toArray();
    }

    /**
     * Obtener trabajos agrupados por unidad organizacional
     */
    public function getWorksByOrganizationalUnit(User $user): array
    {
        $works = $this->getWorksForUser($user);

        return $works->groupBy('organizationalUnit.name')->map(function ($group) {
            return $group->count();
        })->toArray();
    }

    /**
     * Obtener trabajos pendientes de revisión para un coordinador
     */
    public function getPendingForCoordinator(User $coordinator): Collection
    {
        return WorkOfExtension::query()
            ->visibleToCoordinator($coordinator)
            ->whereHas('currentStatus', function ($query) {
                $query->whereIn('name', ['Enviado a Coordinador', 'En Revisión Coordinador']);
            })
            ->with(['workType', 'organizationalUnit', 'responsibleUser'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Obtener trabajos pendientes de evaluación en VIEX
     */
    public function getPendingForViex(): Collection
    {
        return WorkOfExtension::query()
            ->visibleToViex()
            ->whereHas('currentStatus', function ($query) {
                $query->whereIn('name', ['Enviado a VIEX', 'En VIEX - Pendiente Asignación', 'En VIEX - En Evaluación']);
            })
            ->with(['workType', 'organizationalUnit', 'responsibleUser', 'workEvaluators.evaluator'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Obtener trabajos listos para certificación
     */
    public function getReadyForCertification(): Collection
    {
        return WorkOfExtension::query()
            ->whereHas('currentStatus', function ($query) {
                $query->where('name', 'En VIEX - Aprobado');
            })
            ->whereDoesntHave('certification')
            ->with(['workType', 'organizationalUnit', 'responsibleUser'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Buscar trabajos por criterios avanzados
     */
    public function searchAdvanced(array $criteria, User $user): Collection
    {
        $query = WorkOfExtension::query()
            ->with(['workType', 'currentStatus', 'organizationalUnit', 'responsibleUser']);

        // Aplicar criterios de búsqueda
        if (!empty($criteria['title'])) {
            $query->where('title', 'like', '%' . $criteria['title'] . '%');
        }

        if (!empty($criteria['description'])) {
            $query->where('description', 'like', '%' . $criteria['description'] . '%');
        }

        if (!empty($criteria['work_type_ids'])) {
            $query->whereIn('work_type_id', $criteria['work_type_ids']);
        }

        if (!empty($criteria['status_ids'])) {
            $query->whereIn('current_status_id', $criteria['status_ids']);
        }

        if (!empty($criteria['organizational_unit_ids'])) {
            $query->whereIn('organizational_unit_id', $criteria['organizational_unit_ids']);
        }

        if (!empty($criteria['date_from'])) {
            $query->where('created_at', '>=', $criteria['date_from']);
        }

        if (!empty($criteria['date_to'])) {
            $query->where('created_at', '<=', $criteria['date_to']);
        }

        if (!empty($criteria['responsible_user_id'])) {
            $query->where('primary_responsible_user_id', $criteria['responsible_user_id']);
        }

        // Aplicar filtros de visibilidad según el rol del usuario
        if ($user->hasRole('profesor')) {
            $query->visibleToProfessor($user);
        } elseif ($user->hasRole('coordinador_extension')) {
            $query->visibleToCoordinator($user);
        } elseif ($user->hasRole('viex_admin')) {
            $query->visibleToViex();
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Obtener timeline completo de un trabajo
     */
    public function getWorkTimeline(WorkOfExtension $work): Collection
    {
        return $work->statusHistory()
            ->with(['status', 'changedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener estadísticas globales del sistema (solo para admins)
     */
    public function getSystemStatistics(): array
    {
        $totalWorks = WorkOfExtension::count();
        $draftWorks = WorkOfExtension::drafts()->count();
        $certifiedWorks = WorkOfExtension::whereHas('currentStatus', function ($query) {
            $query->where('name', 'Certificado');
        })->count();

        $worksByType = WorkOfExtension::selectRaw('work_types.name as type_name, COUNT(*) as count')
            ->join('work_types', 'work_of_extensions.work_type_id', '=', 'work_types.id')
            ->groupBy('work_types.name')
            ->pluck('count', 'type_name')
            ->toArray();

        $worksByStatus = WorkOfExtension::selectRaw('work_statuses.name as status_name, COUNT(*) as count')
            ->join('work_statuses', 'work_of_extensions.current_status_id', '=', 'work_statuses.id')
            ->groupBy('work_statuses.name')
            ->pluck('count', 'status_name')
            ->toArray();

        return [
            'total_works' => $totalWorks,
            'draft_works' => $draftWorks,
            'certified_works' => $certifiedWorks,
            'completion_rate' => $totalWorks > 0 ? round(($certifiedWorks / $totalWorks) * 100, 2) : 0,
            'works_by_type' => $worksByType,
            'works_by_status' => $worksByStatus,
        ];
    }
}