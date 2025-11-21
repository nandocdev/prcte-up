<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Http\Request;

/**
 * Servicio para manejar la lógica de listados y filtros de trabajos de extensión
 * Centraliza queries complejas y cálculos de estadísticas para vistas de listado
 */
class WorkListingService
{
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
            'user' => $user
        ];
    }

    /**
     * Construir query base según el rol del usuario
     */
    private function buildBaseQuery(User $user): \Illuminate\Database\Eloquent\Builder
    {
        $query = WorkOfExtension::query()
            ->with(['workType', 'currentStatus', 'organizationalUnit', 'responsibleUser'])
            ->orderBy('created_at', 'desc');

        // Aplicar scope de visibilidad según rol
        if ($user->hasRole('profesor')) {
            $query->visibleToProfessor($user);
        } elseif ($user->hasRole('coordinador_extension')) {
            $query->visibleToCoordinator($user);
        } elseif ($user->hasRole('decano_director')) {
            $query->visibleToDean($user);
        } elseif ($user->hasRole('viex_admin')) {
            $query->visibleToViex();
        }
        // super_admin no necesita scope de visibilidad

        return $query;
    }

    /**
     * Aplicar filtros a la query
     */
    private function applyFilters(\Illuminate\Database\Eloquent\Builder $query, Request $request): \Illuminate\Database\Eloquent\Builder
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

        // Búsqueda por texto
        if ($request->filled('search')) {
            $query = $this->applySearchFilter($query, $request->input('search'));
        }

        return $query;
    }

    /**
     * Aplicar filtro de estado
     */
    private function applyStatusFilter(\Illuminate\Database\Eloquent\Builder $query, string $status): \Illuminate\Database\Eloquent\Builder
    {
        switch ($status) {
            case 'draft':
                return $query->where('is_draft', true);
            case 'submitted':
                return $query->where('is_draft', false);
            case 'in_review':
                return $query->where('is_draft', false)
                    ->whereHas('currentStatus', function ($q) {
                        $q->whereNotIn('name', ['Borrador', 'Certificado', 'Rechazado', 'Rechazado por VIEX']);
                    });
            case 'certified':
                return $query->whereHas('currentStatus', function ($q) {
                    $q->where('name', 'Certificado');
                });
            case 'rejected':
                return $query->whereHas('currentStatus', function ($q) {
                    $q->whereIn('name', ['Rechazado', 'Rechazado por VIEX']);
                });
            default:
                return $query;
        }
    }

    /**
     * Aplicar filtro de búsqueda
     */
    private function applySearchFilter(\Illuminate\Database\Eloquent\Builder $query, string $search): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Calcular estadísticas de los trabajos
     */
    private function calculateStatistics(\Illuminate\Database\Eloquent\Collection $works): array
    {
        return [
            'total' => $works->count(),
            'draft' => $works->where('is_draft', '1')->count(),
            'in_review' => $works->where('is_draft', '0')
                ->filter(function ($work) {
                    $statusName = $work->currentStatus?->name;
                    return $statusName &&
                        !in_array($statusName, ['Borrador', 'Certificado', 'Rechazado', 'Rechazado por VIEX']);
                })->count(),
            'certified' => $works->filter(function ($work) {
                return $work->currentStatus?->name === 'Certificado';
            })->count(),
        ];
    }
}