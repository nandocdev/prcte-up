<?php

namespace App\Http\Controllers;

use App\Models\WorkOfExtension;
use App\Models\User;
use App\Services\WorkOfExtension\EvaluateWorkService;
use App\Services\WorkOfExtension\CertifyWorkService;
use App\Services\Dashboard\ViexDashboardService;
use App\Services\Authorization\WorkAuthorizationService;
use App\Events\WorkReceivedInViex;
use App\Events\EvaluatorAssigned;
use App\Events\WorkCertifiedByViex;
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
        $this->middleware(['auth']);
    }

    /**
     * Mostrar dashboard de VIEX con trabajos pendientes
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Delegar lógica del dashboard al servicio
        $dashboardService = new ViexDashboardService(app(WorkAuthorizationService::class));
        $data = $dashboardService->getDashboardData($user);

        return view('viex.index', $data);
    }

    /**
     * Mostrar detalles de un trabajo específico
     *
     * @param WorkOfExtension $work
     * @return \Illuminate\View\View
     */
    public function show(WorkOfExtension $work)
    {
        // Delegar verificación de autorización al servicio
        $authService = app(WorkAuthorizationService::class);
        if (!$authService->canViexReviewWork(Auth::user(), $work)) {
            abort(403, 'No tiene permisos para revisar este trabajo.');
        }

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
            $service = new EvaluateWorkService();
            $evaluationSummary = $service->getEvaluationSummary($work);
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
        // Delegar verificación de autorización al servicio
        $authService = app(WorkAuthorizationService::class);
        if (!$authService->canViexReviewWork(Auth::user(), $work)) {
            abort(403, 'No tiene permisos para recibir este trabajo.');
        }

        $request->validate([
            'comments' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $service = new EvaluateWorkService();
            $service->receiveInViex($work, Auth::user(), $request->input('comments'));

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
        // Delegar verificación de autorización al servicio
        $authService = app(WorkAuthorizationService::class);
        if (!$authService->canViexAssignEvaluators(Auth::user(), $work)) {
            abort(403, 'No tiene permisos para asignar evaluadores a este trabajo.');
        }

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
        // Delegar verificación de autorización al servicio
        $authService = app(WorkAuthorizationService::class);
        if (!$authService->canViexAssignEvaluators(Auth::user(), $work)) {
            abort(403, 'No tiene permisos para asignar evaluadores a este trabajo.');
        }

        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $evaluator = User::findOrFail($validated['evaluator_id']);

            $service = new EvaluateWorkService();
            $workEvaluator = $service->assignEvaluator(
                $work,
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
        // Delegar verificación de autorización al servicio
        $authService = app(WorkAuthorizationService::class);
        if (!$authService->canViexAssignEvaluators(Auth::user(), $work)) {
            abort(403, 'No tiene permisos para iniciar evaluación de este trabajo.');
        }

        $request->validate([
            'comments' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $service = new EvaluateWorkService();
            $service->startViexEvaluation($work, Auth::user(), $request->input('comments'));

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
        // Delegar verificación de autorización al servicio
        $authService = app(WorkAuthorizationService::class);
        if (!$authService->canViexApproveWork(Auth::user(), $work)) {
            abort(403, 'No tiene permisos para revisar evaluaciones de este trabajo.');
        }

        $work->load([
            'responsibleUser',
            'organizationalUnit',
            'workType',
            'currentStatus',
            'workEvaluators.evaluator',
            'evaluations.evaluator',
            'evaluations.evaluationDetails.criteria',
        ]);

        $service = new EvaluateWorkService();
        $evaluationSummary = $service->getEvaluationSummary($work);

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
        // Delegar verificación de autorización al servicio
        $authService = app(WorkAuthorizationService::class);
        if (!$authService->canViexApproveWork(Auth::user(), $work)) {
            abort(403, 'No tiene permisos para aprobar este trabajo.');
        }

        try {
            DB::beginTransaction();

            $validated = $request->validated();

            $service = new EvaluateWorkService();
            $service->approveByViex(
                $work,
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
        // Delegar verificación de autorización al servicio
        $authService = app(WorkAuthorizationService::class);
        if (!$authService->canViexRejectWork(Auth::user(), $work)) {
            abort(403, 'No tiene permisos para rechazar este trabajo.');
        }

        try {
            DB::beginTransaction();

            $validated = $request->validated();

            $service = new EvaluateWorkService();
            $service->rejectByViex(
                $work,
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
        // Delegar verificación de autorización al servicio
        $authService = app(WorkAuthorizationService::class);
        if (!$authService->canViexApproveAndCertifyWork(Auth::user(), $work)) {
            abort(403, 'No tiene permisos para aprobar y certificar este trabajo.');
        }

        $request->validate([
            'comments' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();

            // Paso 1: Cambiar a estado "En VIEX - En Evaluación" si no está ya en ese estado
            if ($work->currentStatus?->name === 'Enviado a VIEX') {
                $evaluateService = new EvaluateWorkService();
                $evaluateService->receiveInViex($work, $user, $request->input('comments') ?: 'Trabajo recibido en VIEX para certificación directa');
                $work->refresh(); // Recargar el modelo con el nuevo estado
            }

            // Paso 2: Generar certificación directamente (cambiará el estado a "Certificado")
            $certifyService = new CertifyWorkService();
            $certification = $certifyService->generateCertification(
                $work,
                $user,
                null, // número automático
                $request->input('comments'), // comentarios
                2 // 2 años por defecto
            );

            // Disparar eventos
            event(new WorkCertifiedByViex($work, $user, $request->input('comments')));

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
        // Delegar verificación de autorización al servicio
        $authService = app(WorkAuthorizationService::class);
        if (!$authService->canViexRequestChanges(Auth::user(), $work)) {
            abort(403, 'No tiene permisos para solicitar cambios a este trabajo.');
        }

        try {
            DB::beginTransaction();

            $validated = $request->validated();

            $service = new EvaluateWorkService();
            $service->requestChangesFromViex($work, Auth::user(), $validated['comments']);

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
