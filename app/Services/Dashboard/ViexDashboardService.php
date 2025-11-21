<?php

namespace App\Services\Dashboard;

use App\Models\WorkOfExtension;

/**
 * Servicio para manejar la lógica del dashboard de VIEX
 * Centraliza queries y cálculos de estadísticas para el dashboard
 */
class ViexDashboardService
{
    /**
     * Obtener todos los datos necesarios para el dashboard de VIEX
     */
    public function getDashboardData(): array
    {
        return [
            'pendingEvaluation' => $this->getPendingEvaluationWorks(),
            'approved' => $this->getApprovedWorks(),
            'stats' => $this->getViexStatistics()
        ];
    }

    /**
     * Obtener trabajos pendientes de evaluación en VIEX
     */
    public function getPendingEvaluationWorks(): \Illuminate\Database\Eloquent\Collection
    {
        return WorkOfExtension::whereHas('currentStatus', function ($query) {
            $query->where('name', 'En VIEX - En Evaluación');
        })
        ->with(['responsibleUser', 'organizationalUnit', 'workType', 'currentStatus'])
        ->orderBy('submitted_at', 'desc')
        ->get();
    }

    /**
     * Obtener trabajos aprobados en VIEX
     */
    public function getApprovedWorks(): \Illuminate\Database\Eloquent\Collection
    {
        return WorkOfExtension::whereHas('currentStatus', function ($query) {
            $query->where('name', 'En VIEX - Aprobado');
        })
        ->with(['responsibleUser', 'organizationalUnit', 'workType', 'currentStatus'])
        ->orderBy('updated_at', 'desc')
        ->limit(10)
        ->get();
    }

    /**
     * Obtener estadísticas para el dashboard de VIEX
     */
    public function getViexStatistics(): array
    {
        return [
            'pending_evaluation' => WorkOfExtension::whereHas('currentStatus', function ($query) {
                $query->where('name', 'En VIEX - En Evaluación');
            })->count(),

            'approved_this_month' => WorkOfExtension::whereHas('currentStatus', function ($query) {
                $query->where('name', 'En VIEX - Aprobado');
            })
            ->where('updated_at', '>=', now()->startOfMonth())
            ->count(),

            'certified_this_month' => WorkOfExtension::whereHas('currentStatus', function ($query) {
                $query->where('name', 'Certificado');
            })
            ->where('updated_at', '>=', now()->startOfMonth())
            ->count(),
        ];
    }
}