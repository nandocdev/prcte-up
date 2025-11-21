<?php

namespace App\Services\WorkOfExtension;

use App\Models\WorkOfExtension;
use App\Models\User;
use App\Models\WorkStatus;
use App\Models\WorkEvaluator;
use App\Models\WorkEvaluation;
use App\Events\WorkChangesRequestedByViex;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para manejar evaluaciones de trabajos de extensión en VIEX
 * Maneja asignación de evaluadores, recepción, aprobación, rechazo y solicitudes de cambios
 */
class EvaluateWorkService
{
    /**
     * CU9: Recibir trabajo en VIEX (transición desde Decano/Director)
     */
    public function receiveInViex(WorkOfExtension $work, User $viexAdmin, ?string $comments = null): WorkOfExtension
    {
        DB::beginTransaction();

        try {
            if ($work->statusIsNot('Enviado a VIEX')) {
                throw new \InvalidArgumentException('El trabajo debe estar "Enviado a VIEX" para ser recibido.');
            }

            $status = WorkStatus::where('name', 'En VIEX - En Evaluación')->firstOrFail();
            $work->changeStatus($status, $viexAdmin, $comments ?: 'Trabajo recibido en VIEX y listo para evaluación directa');

            Log::info('Trabajo recibido en VIEX', [
                'work_id' => $work->getKey(),
                'received_by' => $viexAdmin->getKey(),
            ]);

            DB::commit();

            return $work->fresh();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al recibir trabajo en VIEX', [
                'work_id' => $work->getKey(),
                'viex_admin_id' => $viexAdmin->getKey(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * CU9: Asignar evaluador a un trabajo
     */
    public function assignEvaluator(
        WorkOfExtension $work,
        User $evaluator,
        User $assignedBy,
        string $role = 'evaluator',
        ?string $assignmentNotes = null
    ): WorkEvaluator {
        DB::beginTransaction();

        try {
            if (!in_array($work->currentStatus->name, ['En VIEX - Pendiente Asignación', 'En VIEX - En Evaluación'])) {
                throw new \InvalidArgumentException('El trabajo debe estar en VIEX para asignar evaluadores.');
            }

            // Verificar si el evaluador ya está asignado
            $existingAssignment = $work->workEvaluators()
                ->where('evaluator_user_id', $evaluator->id)
                ->first();

            if ($existingAssignment) {
                throw new \InvalidArgumentException('Este evaluador ya está asignado a este trabajo.');
            }

            // Crear la asignación
            $workEvaluator = $work->workEvaluators()->create([
                'evaluator_user_id' => $evaluator->id,
                'assigned_by_user_id' => $assignedBy->id,
                'role_evaluator' => $role,
                'assignment_notes' => $assignmentNotes,
                'assigned_at' => now(),
                'status' => WorkEvaluator::STATUS_PENDING,
            ]);

            Log::info('Evaluador asignado al trabajo', [
                'work_id' => $work->getKey(),
                'evaluator_id' => $evaluator->id,
                'role_evaluator' => $role,
                'assigned_by' => $assignedBy->id,
            ]);

            DB::commit();

            return $workEvaluator;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al asignar evaluador', [
                'work_id' => $work->getKey(),
                'evaluator_id' => $evaluator->id,
                'assigned_by' => $assignedBy->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * CU9: Iniciar evaluación en VIEX (cuando hay al menos un evaluador asignado)
     */
    public function startViexEvaluation(WorkOfExtension $work, User $viexAdmin, ?string $comments = null): WorkOfExtension
    {
        DB::beginTransaction();

        try {
            if ($work->statusIsNot('En VIEX - Pendiente Asignación')) {
                throw new \InvalidArgumentException('El trabajo debe estar "En VIEX - Pendiente Asignación" para iniciar evaluación.');
            }

            // Verificar que hay al menos un evaluador asignado
            $evaluatorsCount = $work->workEvaluators()->count();
            if ($evaluatorsCount === 0) {
                throw new \InvalidArgumentException('Debe asignar al menos un evaluador antes de iniciar la evaluación.');
            }

            $status = WorkStatus::where('name', 'En VIEX - En Evaluación')->firstOrFail();
            $work->changeStatus($status, $viexAdmin, $comments ?: "Evaluación iniciada con {$evaluatorsCount} evaluador(es) asignado(s)");

            Log::info('Evaluación VIEX iniciada', [
                'work_id' => $work->getKey(),
                'evaluators_count' => $evaluatorsCount,
            ]);

            DB::commit();

            return $work->fresh();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al iniciar evaluación VIEX', [
                'work_id' => $work->getKey(),
                'viex_admin_id' => $viexAdmin->getKey(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * CU14: Aprobar trabajo por VIEX (evaluación positiva)
     */
    public function approveByViex(
        WorkOfExtension $work,
        User $evaluator,
        ?string $comments = null,
        ?string $recommendations = null
    ): WorkOfExtension {
        DB::beginTransaction();

        try {
            if ($work->statusIsNot('En VIEX - En Evaluación')) {
                throw new \InvalidArgumentException('El trabajo debe estar "En VIEX - En Evaluación" para poder ser aprobado.');
            }

            $status = WorkStatus::where('name', 'En VIEX - Aprobado')->firstOrFail();

            $finalComments = collect([
                $comments ? "Evaluación: {$comments}" : null,
                $recommendations ? "Recomendaciones: {$recommendations}" : null
            ])->filter()->implode(' | ');

            $work->changeStatus($status, $evaluator, $finalComments ?: 'Trabajo aprobado por VIEX');

            Log::info('Trabajo aprobado por VIEX', [
                'work_id' => $work->getKey(),
                'evaluator_id' => $evaluator->getKey(),
            ]);

            DB::commit();

            return $work->fresh();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al aprobar trabajo por VIEX', [
                'work_id' => $work->getKey(),
                'evaluator_id' => $evaluator->getKey(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * CU14: Rechazar trabajo por VIEX (evaluación negativa)
     */
    public function rejectByViex(
        WorkOfExtension $work,
        User $evaluator,
        string $reason,
        ?string $recommendations = null
    ): WorkOfExtension {
        DB::beginTransaction();

        try {
            if ($work->statusIsNot('En VIEX - En Evaluación')) {
                throw new \InvalidArgumentException('El trabajo debe estar "En VIEX - En Evaluación" para poder ser rechazado.');
            }

            $status = WorkStatus::where('name', 'Rechazado por VIEX')->firstOrFail();

            $finalComments = collect([
                "Razón del rechazo: {$reason}",
                $recommendations ? "Recomendaciones: {$recommendations}" : null
            ])->filter()->implode(' | ');

            $work->changeStatus($status, $evaluator, $finalComments);

            // Marcar como borrador para que el profesor pueda editar
            $work->update(['is_draft' => '1']);

            Log::info('Trabajo rechazado por VIEX', [
                'work_id' => $work->getKey(),
                'evaluator_id' => $evaluator->getKey(),
                'reason' => $reason,
            ]);

            DB::commit();

            return $work->fresh();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al rechazar trabajo por VIEX', [
                'work_id' => $work->getKey(),
                'evaluator_id' => $evaluator->getKey(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * CU14: Solicitar correcciones desde VIEX
     */
    public function requestChangesFromViex(WorkOfExtension $work, User $viexAdmin, string $comments): WorkOfExtension
    {
        DB::beginTransaction();

        try {
            if ($work->statusIsNot('En VIEX - En Evaluación')) {
                throw new \InvalidArgumentException('El trabajo debe estar "En VIEX - En Evaluación" para solicitar correcciones.');
            }

            // Cambiar a estado "Devuelto para Corrección"
            $changesStatus = WorkStatus::where('name', 'Devuelto para Corrección')->firstOrFail();

            $work->changeStatus($changesStatus, $viexAdmin, $comments);

            // Marcar como borrador para que el profesor pueda editar
            $work->update(['is_draft' => '1']);

            Log::info('Correcciones solicitadas por VIEX', [
                'work_id' => $work->getKey(),
                'viex_admin_id' => $viexAdmin->getKey(),
                'comments' => $comments,
            ]);

            // Disparar evento para notificar al profesor
            WorkChangesRequestedByViex::dispatch($work, $viexAdmin, $comments);

            DB::commit();

            return $work->fresh();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al solicitar correcciones por VIEX', [
                'work_id' => $work->getKey(),
                'viex_admin_id' => $viexAdmin->getKey(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Verificar si todas las evaluaciones están completadas
     */
    public function allEvaluationsCompleted(WorkOfExtension $work): bool
    {
        $totalEvaluators = $work->workEvaluators()->count();

        if ($totalEvaluators === 0) {
            return false;
        }

        $completedEvaluations = $work->evaluations()
            ->where('status', WorkEvaluation::STATUS_SUBMITTED)
            ->count();

        return $completedEvaluations === $totalEvaluators;
    }

    /**
     * Obtener resumen de evaluaciones del trabajo
     */
    public function getEvaluationSummary(WorkOfExtension $work): array
    {
        $evaluations = $work->evaluations()
            ->with(['evaluator', 'workEvaluator', 'evaluationDetails.criteria'])
            ->get();

        $totalEvaluators = $work->workEvaluators()->count();
        $submittedEvaluations = $evaluations->where('status', WorkEvaluation::STATUS_SUBMITTED);
        $completedCount = $submittedEvaluations->count();

        // Calcular promedios
        $avgTotalScore = $submittedEvaluations->avg('total_score') ?? 0;
        $avgWeightedScore = $submittedEvaluations->avg('weighted_score') ?? 0;

        // Contar decisiones
        $approvals = $submittedEvaluations->whereIn('final_decision', [
            WorkEvaluation::DECISION_APPROVE,
            WorkEvaluation::DECISION_APPROVE_WITH_CONDITIONS
        ])->count();

        $rejections = $submittedEvaluations->where('final_decision', WorkEvaluation::DECISION_REJECT)->count();

        // Evaluadores principales
        $leadEvaluators = $work->workEvaluators()
            ->where('role_evaluator', WorkEvaluator::ROLE_LEAD)
            ->with('evaluator')
            ->get();

        return [
            'total_evaluators' => $totalEvaluators,
            'completed_evaluations' => $completedCount,
            'pending_evaluations' => $totalEvaluators - $completedCount,
            'completion_percentage' => $totalEvaluators > 0 ? round(($completedCount / $totalEvaluators) * 100, 2) : 0,
            'average_total_score' => round($avgTotalScore, 2),
            'average_weighted_score' => round($avgWeightedScore, 2),
            'approvals_count' => $approvals,
            'rejections_count' => $rejections,
            'lead_evaluators' => $leadEvaluators,
            'all_completed' => $this->allEvaluationsCompleted($work),
            'recommendation' => $this->getEvaluationRecommendation($approvals, $rejections, $completedCount),
        ];
    }

    /**
     * Obtener recomendación basada en evaluaciones
     */
    private function getEvaluationRecommendation(int $approvals, int $rejections, int $total): string
    {
        if ($total === 0) {
            return 'Sin evaluaciones completadas';
        }

        $approvalRate = ($approvals / $total) * 100;

        if ($approvalRate >= 80) {
            return 'Aprobación altamente recomendada';
        } elseif ($approvalRate >= 60) {
            return 'Aprobación recomendada con observaciones';
        } elseif ($approvalRate >= 40) {
            return 'Revisión adicional requerida';
        } else {
            return 'Rechazo recomendado';
        }
    }
}