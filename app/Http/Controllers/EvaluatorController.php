<?php

namespace App\Http\Controllers;

use App\Models\WorkOfExtension;
use App\Models\WorkEvaluator;
use App\Models\WorkEvaluation;
use App\Models\EvaluationDetail;
use App\Models\EvaluationCriteria;
use App\Events\EvaluationSubmitted;
use App\Http\Requests\SubmitEvaluationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controlador: EvaluatorController
 * 
 * Gestiona las operaciones de los evaluadores para realizar
 * evaluaciones de trabajos de extensión asignados por VIEX.
 * 
 * Caso de Uso: CU9 - Evaluar Trabajo de Extensión (desde perspectiva del evaluador)
 */
class EvaluatorController extends Controller
{
    /**
     * Constructor - Aplicar middleware de autorización
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:evaluador']);
    }

    /**
     * Mostrar dashboard del evaluador con asignaciones
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Asignaciones del evaluador
        $pendingAssignments = WorkEvaluator::where('evaluator_user_id', $user->id)
            ->where('status', WorkEvaluator::STATUS_PENDING)
            ->with(['workOfExtension.workType', 'workOfExtension.responsibleUser', 'assignedBy'])
            ->orderBy('assigned_at', 'desc')
            ->get();

        $acceptedAssignments = WorkEvaluator::where('evaluator_user_id', $user->id)
            ->whereIn('status', [WorkEvaluator::STATUS_ACCEPTED, WorkEvaluator::STATUS_IN_PROGRESS])
            ->with(['workOfExtension.workType', 'workOfExtension.responsibleUser'])
            ->orderBy('accepted_at', 'desc')
            ->get();

        $completedAssignments = WorkEvaluator::where('evaluator_user_id', $user->id)
            ->where('status', WorkEvaluator::STATUS_COMPLETED)
            ->with(['workOfExtension.workType', 'workOfExtension.responsibleUser'])
            ->orderBy('completed_at', 'desc')
            ->limit(10)
            ->get();

        // Estadísticas
        $stats = [
            'pending' => $pendingAssignments->count(),
            'in_progress' => $acceptedAssignments->count(),
            'completed_this_month' => WorkEvaluator::where('evaluator_user_id', $user->id)
                ->where('status', WorkEvaluator::STATUS_COMPLETED)
                ->where('completed_at', '>=', now()->startOfMonth())
                ->count(),
            'total_completed' => WorkEvaluator::where('evaluator_user_id', $user->id)
                ->where('status', WorkEvaluator::STATUS_COMPLETED)
                ->count(),
        ];

        return view('evaluator.index', compact(
            'pendingAssignments',
            'acceptedAssignments',
            'completedAssignments',
            'stats'
        ));
    }

    /**
     * Mostrar detalles de un trabajo asignado
     *
     * @param WorkOfExtension $work
     * @return \Illuminate\View\View
     */
    public function show(WorkOfExtension $work)
    {
        $this->authorize('viewAsEvaluator', $work);

        $user = Auth::user();

        // Verificar que el usuario está asignado como evaluador
        $assignment = $work->workEvaluators()
            ->where('evaluator_user_id', $user->id)
            ->firstOrFail();

        $work->load([
            'responsibleUser',
            'organizationalUnit',
            'workType',
            'currentStatus',
            'participants.user',
            'projectDetail',
            'activityDetail',
            'publicationDetail',
            'technicalAssistanceDetail',
        ]);

        // Obtener evaluación existente si ya existe
        $evaluation = WorkEvaluation::where('work_of_extension_id', $work->id)
            ->where('evaluator_user_id', $user->id)
            ->with('evaluationDetails.criteria')
            ->first();

        return view('evaluator.show', compact('work', 'assignment', 'evaluation'));
    }

    /**
     * Aceptar asignación de evaluación
     *
     * @param WorkOfExtension $work
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acceptAssignment(WorkOfExtension $work)
    {
        $this->authorize('viewAsEvaluator', $work);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            $assignment = $work->workEvaluators()
                ->where('evaluator_user_id', $user->id)
                ->where('status', WorkEvaluator::STATUS_PENDING)
                ->firstOrFail();

            $assignment->markAsAccepted();

            DB::commit();

            return redirect()
                ->route('evaluator.show', $work)
                ->with('success', __('Asignación aceptada exitosamente. Puede iniciar la evaluación.'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al aceptar asignación', [
                'work_id' => $work->id,
                'evaluator_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->with('error', __('Error al aceptar asignación: ') . $e->getMessage());
        }
    }

    /**
     * Rechazar asignación de evaluación
     *
     * @param Request $request
     * @param WorkOfExtension $work
     * @return \Illuminate\Http\RedirectResponse
     */
    public function declineAssignment(Request $request, WorkOfExtension $work)
    {
        $this->authorize('viewAsEvaluator', $work);

        $user = Auth::user();

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $assignment = $work->workEvaluators()
                ->where('evaluator_user_id', $user->id)
                ->where('status', WorkEvaluator::STATUS_PENDING)
                ->firstOrFail();

            $assignment->update([
                'status' => WorkEvaluator::STATUS_DECLINED,
                'assignment_notes' => $request->input('reason'),
            ]);

            // TODO: Notificar a VIEX del rechazo (Fase 6)

            DB::commit();

            return redirect()
                ->route('evaluator.index')
                ->with('success', __('Asignación rechazada. VIEX ha sido notificado.'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al rechazar asignación', [
                'work_id' => $work->id,
                'evaluator_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', __('Error al rechazar asignación: ') . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de evaluación
     *
     * @param WorkOfExtension $work
     * @return \Illuminate\View\View
     */
    public function evaluate(WorkOfExtension $work)
    {
        $this->authorize('submitEvaluation', $work);

        $user = Auth::user();

        // Verificar que la asignación fue aceptada
        $assignment = $work->workEvaluators()
            ->where('evaluator_user_id', $user->id)
            ->whereIn('status', [WorkEvaluator::STATUS_ACCEPTED, WorkEvaluator::STATUS_IN_PROGRESS])
            ->firstOrFail();

        $work->load([
            'responsibleUser',
            'organizationalUnit',
            'workType',
            'projectDetail',
            'activityDetail',
            'publicationDetail',
            'technicalAssistanceDetail',
        ]);

        // Obtener o crear evaluación
        $evaluation = WorkEvaluation::firstOrCreate(
            [
                'work_of_extension_id' => $work->id,
                'evaluator_user_id' => $user->id,
            ],
            [
                'work_evaluator_id' => $assignment->id,
                'status' => WorkEvaluation::STATUS_DRAFT,
                'final_decision' => WorkEvaluation::DECISION_PENDING,
            ]
        );

        // Actualizar estado de asignación si es necesario
        if ($assignment->status === WorkEvaluator::STATUS_ACCEPTED) {
            $assignment->markAsInProgress();
            $evaluation->start();
        }

        // Obtener criterios activos
        $criteria = EvaluationCriteria::active()->ordered()->get();

        // Obtener detalles de evaluación existentes
        $evaluationDetails = $evaluation->evaluationDetails()
            ->with('criteria')
            ->get()
            ->keyBy('evaluation_criteria_id');

        return view('evaluator.evaluate', compact(
            'work',
            'assignment',
            'evaluation',
            'criteria',
            'evaluationDetails'
        ));
    }

    /**
     * Guardar evaluación (como borrador o enviar)
     *
     * @param SubmitEvaluationRequest $request
     * @param WorkOfExtension $work
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitEvaluation(SubmitEvaluationRequest $request, WorkOfExtension $work)
    {
        $this->authorize('submitEvaluation', $work);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            $validated = $request->validated();

            $evaluation = WorkEvaluation::where('work_of_extension_id', $work->id)
                ->where('evaluator_user_id', $user->id)
                ->firstOrFail();

            // Actualizar evaluación general
            $evaluation->update([
                'general_comments' => $validated['general_comments'],
                'strengths' => $validated['strengths'] ?? null,
                'weaknesses' => $validated['weaknesses'] ?? null,
                'recommendations' => $validated['recommendations'],
                'final_decision' => $validated['final_decision'],
                'decision_justification' => $validated['decision_justification'] ?? null,
            ]);

            // Guardar detalles por criterio
            foreach ($validated['criteria'] as $criteriaId => $criteriaData) {
                EvaluationDetail::updateOrCreate(
                    [
                        'work_evaluation_id' => $evaluation->id,
                        'evaluation_criteria_id' => $criteriaId,
                    ],
                    [
                        'score' => $criteriaData['score'],
                        'comments' => $criteriaData['comments'] ?? null,
                        'evidence' => $criteriaData['evidence'] ?? null,
                    ]
                );
            }

            // Recalcular puntajes
            $evaluation->calculateTotalScore();
            $evaluation->calculateWeightedScore();

            // Si es envío final (validado por el Form Request)
            if ($request->boolean('submit_final')) {
                $evaluation->submit();

                // Disparar evento para notificar a VIEX
                event(new EvaluationSubmitted($work, $evaluation));

                $message = __('Evaluación enviada exitosamente. VIEX ha sido notificado.');
            } else {
                $message = __('Evaluación guardada como borrador.');
            }

            DB::commit();

            return redirect()
                ->route('evaluator.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar evaluación', [
                'work_id' => $work->id,
                'evaluator_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', __('Error al guardar evaluación: ') . $e->getMessage());
        }
    }
}
