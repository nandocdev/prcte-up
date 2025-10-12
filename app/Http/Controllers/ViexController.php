<?php

namespace App\Http\Controllers;

use App\Models\WorkOfExtension;
use App\Models\User;
use App\Events\WorkReceivedInViex;
use App\Events\EvaluatorAssigned;
use App\Events\WorkApprovedByViex;
use App\Events\WorkRejectedByViex;
use App\Http\Requests\AssignEvaluatorRequest;
use App\Http\Requests\ApproveWorkRequest;
use App\Http\Requests\RejectWorkRequest;
use App\Http\Requests\RequestChangesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Controlador: ViexController
 * 
 * Gestiona las operaciones de VIEX (Vicerrectoría de Extensión) para
 * la evaluación y aprobación de trabajos de extensión.
 * 
 * Caso de Uso: CU9 - Evaluar y Aprobar Trabajo de Extensión en VIEX
 */
class ViexController extends Controller
{
    /**
     * Constructor - Aplicar middleware de autorización
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:viex_admin']);
    }

    /**
     * Mostrar dashboard de VIEX con trabajos pendientes
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Trabajos recibidos en VIEX (diferentes estados)
        $pendingEvaluation = WorkOfExtension::whereHas('currentStatus', function ($query) {
            $query->where('name', 'En VIEX - En Evaluación');
        })
        ->with(['responsibleUser', 'organizationalUnit', 'workType', 'currentStatus'])
        ->orderBy('submitted_at', 'desc')
        ->get();

        $approved = WorkOfExtension::whereHas('currentStatus', function ($query) {
            $query->where('name', 'En VIEX - Aprobado');
        })
        ->with(['responsibleUser', 'organizationalUnit', 'workType', 'currentStatus'])
        ->orderBy('updated_at', 'desc')
        ->limit(10)
        ->get();

        // Estadísticas
        $stats = [
            'pending_evaluation' => $pendingEvaluation->count(),
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

        return view('viex.index', compact(
            'pendingEvaluation',
            'approved',
            'stats'
        ));
    }

    /**
     * Mostrar detalles de un trabajo específico
     *
     * @param WorkOfExtension $work
     * @return \Illuminate\View\View
     */
    public function show(WorkOfExtension $work)
    {
        $this->authorize('viewAsViex', $work);

        $work->load([
            'responsibleUser',
            'organizationalUnit',
            'workType',
            'currentStatus',
            'statusHistory.changedBy',
            'statusHistory.status',
            'participants.user',
            'workEvaluators.evaluator',
            'workEvaluators.assignedBy',
            'evaluations.evaluator',
            'evaluations.evaluationDetails.criteria',
            'projectDetail',
            'activityDetail',
            'publicationDetail',
            'technicalAssistanceDetail',
        ]);

        // Obtener resumen de evaluaciones si está en evaluación
        $evaluationSummary = null;
        if (in_array($work->currentStatus->name, ['En VIEX - En Evaluación', 'En VIEX - Aprobado'])) {
            $evaluationSummary = $work->getEvaluationSummary();
        }

        return view('viex.show', compact('work', 'evaluationSummary'));
    }

    /**
     * Recibir trabajo en VIEX (desde Decano/Director)
     *
     * @param Request $request
     * @param WorkOfExtension $work
     * @return \Illuminate\Http\RedirectResponse
     */
    public function receive(Request $request, WorkOfExtension $work)
    {
        $this->authorize('viewAsViex', $work);

        $request->validate([
            'comments' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $work->receiveInViex(Auth::user(), $request->input('comments'));

            // Disparar evento para notificar al equipo VIEX
            event(new WorkReceivedInViex($work, Auth::user()));

            DB::commit();

            return redirect()
                ->route('viex.show', $work)
                ->with('success', __('Trabajo recibido en VIEX exitosamente. Proceda a asignar evaluadores.'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al recibir trabajo en VIEX', [
                'work_id' => $work->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', __('Error al recibir el trabajo: ') . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario para asignar evaluadores
     *
     * @param WorkOfExtension $work
     * @return \Illuminate\View\View
     */
    public function showAssignEvaluatorsForm(WorkOfExtension $work)
    {
        $this->authorize('assignEvaluator', $work);

        $work->load(['workEvaluators.evaluator', 'workType', 'organizationalUnit']);

        // Obtener lista de posibles evaluadores (usuarios con rol evaluador)
        $availableEvaluators = User::role('evaluador')
            ->whereNotIn('id', $work->workEvaluators->pluck('evaluator_user_id'))
            ->orderBy('name')
            ->get();

        return view('viex.assign_evaluators', compact('work', 'availableEvaluators'));
    }

    /**
     * Asignar evaluador a un trabajo
     *
     * @param AssignEvaluatorRequest $request
     * @param WorkOfExtension $work
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignEvaluator(AssignEvaluatorRequest $request, WorkOfExtension $work)
    {
        $this->authorize('assignEvaluator', $work);

        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $evaluator = User::findOrFail($validated['evaluator_id']);

            $workEvaluator = $work->assignEvaluator(
                $evaluator,
                Auth::user(),
                $validated['role_evaluator'],
                $validated['assignment_notes']
            );

            // Disparar evento para notificar al evaluador
            event(new EvaluatorAssigned($work, $workEvaluator, Auth::user()));

            DB::commit();

            return redirect()
                ->route('viex.show', $work)
                ->with('success', __('Evaluador asignado exitosamente.'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al asignar evaluador', [
                'work_id' => $work->id,
                'evaluator_id' => $validated['evaluator_id'] ?? null,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', __('Error al asignar evaluador: ') . $e->getMessage());
        }
    }

    /**
     * Iniciar proceso de evaluación (cuando hay evaluadores asignados)
     *
     * @param Request $request
     * @param WorkOfExtension $work
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startEvaluation(Request $request, WorkOfExtension $work)
    {
        $this->authorize('assignEvaluator', $work);

        $request->validate([
            'comments' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $work->startViexEvaluation(Auth::user(), $request->input('comments'));

            // TODO: Disparar evento para notificar a evaluadores (Fase 6)
            // event(new EvaluationStarted($work));

            DB::commit();

            return redirect()
                ->route('viex.show', $work)
                ->with('success', __('Evaluación iniciada. Los evaluadores han sido notificados.'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al iniciar evaluación', [
                'work_id' => $work->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->with('error', __('Error al iniciar evaluación: ') . $e->getMessage());
        }
    }

    /**
     * Mostrar resumen de evaluaciones para tomar decisión final
     *
     * @param WorkOfExtension $work
     * @return \Illuminate\View\View
     */
    public function reviewEvaluations(WorkOfExtension $work)
    {
        $this->authorize('approveAsViex', $work);

        $work->load([
            'responsibleUser',
            'organizationalUnit',
            'workType',
            'currentStatus',
            'workEvaluators.evaluator',
            'evaluations.evaluator',
            'evaluations.evaluationDetails.criteria',
        ]);

        $evaluationSummary = $work->getEvaluationSummary();

        return view('viex.review_evaluations', compact('work', 'evaluationSummary'));
    }

    /**
     * Aprobar trabajo (decisión final de VIEX)
     *
     * @param ApproveWorkRequest $request
     * @param WorkOfExtension $work
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(ApproveWorkRequest $request, WorkOfExtension $work)
    {
        $this->authorize('approveAsViex', $work);

        try {
            DB::beginTransaction();

            $validated = $request->validated();

            $work->approveByViex(
                Auth::user(),
                $validated['comments'],
                $validated['recommendations']
            );

            // Disparar evento para notificar al profesor responsable
            event(new WorkApprovedByViex($work, Auth::user(), $validated['comments']));

            DB::commit();

            return redirect()
                ->route('viex.index')
                ->with('success', __('Trabajo aprobado exitosamente. Ahora puede proceder a generar la certificación.'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al aprobar trabajo en VIEX', [
                'work_id' => $work->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', __('Error al aprobar el trabajo: ') . $e->getMessage());
        }
    }

    /**
     * Rechazar trabajo (decisión final de VIEX)
     *
     * @param RejectWorkRequest $request
     * @param WorkOfExtension $work
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(RejectWorkRequest $request, WorkOfExtension $work)
    {
        $this->authorize('rejectAsViex', $work);

        try {
            DB::beginTransaction();

            $validated = $request->validated();

            $work->rejectByViex(
                Auth::user(),
                $validated['reason'],
                $validated['recommendations']
            );

            // Disparar evento para notificar al profesor responsable
            event(new WorkRejectedByViex($work, Auth::user(), $validated['reason']));

            DB::commit();

            return redirect()
                ->route('viex.index')
                ->with('success', __('Trabajo rechazado. El responsable ha sido notificado y puede realizar correcciones.'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al rechazar trabajo en VIEX', [
                'work_id' => $work->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', __('Error al rechazar el trabajo: ') . $e->getMessage());
        }
    }

    /**
     * Aprobar y certificar trabajo directamente (sin evaluadores)
     *
     * @param Request $request
     * @param WorkOfExtension $work
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveAndCertify(Request $request, WorkOfExtension $work)
    {
        $this->authorize('approveAsViex', $work);

        $request->validate([
            'comments' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Aprobar el trabajo
            $work->approveByViex(Auth::user(), $request->input('comments'), null);

            // Generar certificación automáticamente
            $certification = $work->generateCertification(
                Auth::user(),
                2, // 2 años por defecto
                null, // número automático
                $request->input('comments')
            );

            // Disparar eventos
            event(new WorkApprovedByViex($work, Auth::user(), $request->input('comments')));

            DB::commit();

            return redirect()
                ->route('viex.show', $work)
                ->with('success', __('Trabajo aprobado y certificado exitosamente.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al aprobar y certificar trabajo en VIEX', [
                'work_id' => $work->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', __('Error al aprobar y certificar el trabajo: ') . $e->getMessage());
        }
    }

    /**
     * Solicitar correcciones al profesor desde VIEX
     *
     * @param RequestChangesRequest $request
     * @param WorkOfExtension $work
     * @return \Illuminate\Http\RedirectResponse
     */
    public function requestChanges(RequestChangesRequest $request, WorkOfExtension $work)
    {
        $this->authorize('requestChangesAsViex', $work);

        try {
            DB::beginTransaction();

            $validated = $request->validated();

            $work->requestChangesFromViex(Auth::user(), $validated['comments']);

            DB::commit();

            return redirect()
                ->route('viex.show', $work)
                ->with('success', __('Correcciones solicitadas al profesor. El trabajo ha sido devuelto para edición.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al solicitar correcciones desde VIEX', [
                'work_id' => $work->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', __('Error al solicitar correcciones: ') . $e->getMessage());
        }
    }
}
