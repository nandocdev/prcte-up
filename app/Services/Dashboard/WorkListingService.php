<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Servicio para manejar la lógica de listados y filtros de trabajos de extensión
 * Centraliza queries complejas y cálculos de estadísticas para vistas de listado
 */
class WorkListingService
{
    /**
     * Mapeo de filtros de estado visibles para profesores según el flujo oficial VIEX
     */
    private const STATUS_FILTERS = [
        'draft' => [
            'label' => 'Borrador',
            'match' => 'draft',
            'visible' => true,
        ],
        'sent_to_coordinator' => [
            'label' => 'Enviado al Coordinador',
            'statuses' => [
                'Enviado a Coordinador',
            ],
            'visible' => true,
        ],
        'under_coordinator_review' => [
            'label' => 'En Revisión del Coordinador',
            'statuses' => [
                'En Revisión Coordinador',
            ],
            'visible' => true,
        ],
        'needs_corrections' => [
            'label' => 'Devuelto para Corrección',
            'statuses' => [
                'En Corrección',
                'Devuelto para Corrección',
            ],
            'visible' => true,
        ],
        'in_viex' => [
            'label' => 'En VIEX',
            'statuses' => [
                'Enviado a VIEX',
                'Pendiente VIEX',
                'En VIEX - Pendiente Asignación',
                'En VIEX - En Evaluación',
                'En Evaluación VIEX',
            ],
            'visible' => true,
        ],
        'approved' => [
            'label' => 'Aprobado por VIEX',
            'statuses' => [
                'En VIEX - Aprobado',
                'Aprobado Internamente',
            ],
            'visible' => true,
        ],
        'certified' => [
            'label' => 'Certificado',
            'statuses' => [
                'Certificado',
            ],
            'visible' => true,
        ],
        'rejected' => [
            'label' => 'Rechazado',
            'statuses' => [
                'Rechazado por Coordinador',
                'Rechazado por Decano/Director',
                'Rechazado por VIEX',
                'Rechazado',
            ],
            'visible' => true,
        ],
        // Filtros heredados para mantener compatibilidad con parámetros existentes
        'submitted' => [
            'label' => 'Enviados',
            'match' => 'submitted',
            'visible' => false,
        ],
        'in_review' => [
            'label' => 'En Revisión',
            'statuses' => [
                'Enviado a Coordinador',
                'En Revisión Coordinador',
                'Pendiente Decano',
                'Enviado a Decano/Director',
                'En Revisión Decano/Director',
                'Enviado a VIEX',
                'Pendiente VIEX',
                'En VIEX - Pendiente Asignación',
                'En VIEX - En Evaluación',
                'En Evaluación VIEX',
                'En VIEX - Aprobado',
                'Aprobado Internamente',
            ],
            'visible' => false,
        ],
    ];

    /**
     * Obtener datos para el dashboard principal de trabajos
     */
    public function getWorksListing(Request $request, User $user): array
    {
        $query = $this->buildBaseQuery($user);
        $query = $this->applyFilters($query, $request);
        $works = $query->get();

        return [
            'works' => $works,
            'statistics' => $this->calculateStatistics($works),
            'user' => $user,
            'statusFilters' => $this->getVisibleStatusFilters(),
        ];
    }

    /**
     * Construir query base según el rol del usuario
     */
    private function buildBaseQuery(User $user): Builder
    {
        $query = WorkOfExtension::query()
            ->with(['workType', 'currentStatus', 'organizationalUnit', 'responsibleUser'])
            ->orderBy('created_at', 'desc');

        // Aplicar scope de visibilidad según rol
        if ($user->hasRole('profesor')) {
            $query->visibleToProfessor($user);
        } elseif ($user->hasRole('coordinador_extension')) {
            $query->visibleToCoordinator($user);
        } elseif ($user->hasRole('viex_admin')) {
            $query->visibleToViex();
        }
        // super_admin no necesita scope de visibilidad

        return $query;
    }

    /**
     * Aplicar filtros a la query
     */
    private function applyFilters(Builder $query, Request $request): Builder
    {
        // Filtro por estado
        if ($request->filled('status')) {
            $query = $this->applyStatusFilter($query, $request->input('status'));
        }

        // Filtro por tipo de trabajo
        if ($request->filled('work_type')) {
            $query->where('work_type_id', $request->input('work_type'));
        }

        // Filtro por período académico
        if ($request->filled('academic_period')) {
            $query->where('academic_period', $request->input('academic_period'));
        }

        // Filtro por fecha de creación
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $query = $this->applyDateFilter($query, $request->input('date_from'), $request->input('date_to'));
        }

        return $query;
    }

    /**
     * Aplicar filtro de estado
     */
    private function applyStatusFilter(Builder $query, string $status): Builder
    {
        $config = self::STATUS_FILTERS[$status] ?? null;

        if (!$config) {
            return $query;
        }

        if (($config['match'] ?? null) === 'draft') {
            return $query->where('is_draft', true);
        }

        if (($config['match'] ?? null) === 'submitted') {
            return $query->where('is_draft', false);
        }

        if (!empty($config['statuses'])) {
            return $query->whereHas('currentStatus', function ($q) use ($config) {
                $q->whereIn('name', $config['statuses']);
            });
        }

        return $query;
    }

    /**
     * Aplicar filtro de búsqueda
     */
    private function applySearchFilter(\Illuminate\Database\Eloquent\Builder $query, string $search): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('title', 'like', "%{$search}%");
    }

    /**
     * Aplicar filtro de fecha
     */
    private function applyDateFilter(\Illuminate\Database\Eloquent\Builder $query, ?string $dateFrom, ?string $dateTo): \Illuminate\Database\Eloquent\Builder
    {
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return $query;
    }

    /**
     * Calcular estadísticas de los trabajos
     */
    private function calculateStatistics(\Illuminate\Database\Eloquent\Collection $works): array
    {
        $inReviewFilters = [
            'sent_to_coordinator',
            'under_coordinator_review',
            'in_viex',
            'approved',
        ];

        return [
            'total' => $works->count(),
            'draft' => $works->filter(fn($work) => $this->matchesStatusFilter($work, 'draft'))->count(),
            'in_review' => $works->filter(function ($work) use ($inReviewFilters) {
                foreach ($inReviewFilters as $filterKey) {
                    if ($this->matchesStatusFilter($work, $filterKey)) {
                        return true;
                    }
                }

                return false;
            })->count(),
            'certified' => $works->filter(fn($work) => $this->matchesStatusFilter($work, 'certified'))->count(),
        ];
    }

    /**
     * Retornar los filtros visibles para la vista
     */
    private function getVisibleStatusFilters(): array
    {
        return collect(self::STATUS_FILTERS)
            ->filter(fn(array $config) => $config['visible'] ?? false)
            ->map(fn(array $config) => ['label' => $config['label']])
            ->all();
    }

    /**
     * Verificar si un trabajo coincide con un filtro de estado
     */
    private function matchesStatusFilter(WorkOfExtension $work, string $filterKey): bool
    {
        $config = self::STATUS_FILTERS[$filterKey] ?? null;

        if (!$config) {
            return false;
        }

        if (($config['match'] ?? null) === 'draft') {
            return (bool) $work->is_draft;
        }

        if (($config['match'] ?? null) === 'submitted') {
            return !$work->is_draft;
        }

        if (empty($config['statuses'])) {
            return false;
        }

        $currentStatusName = $work->currentStatus?->name;

        if (!$currentStatusName) {
            return false;
        }

        return in_array($currentStatusName, $config['statuses'], true);
    }
}