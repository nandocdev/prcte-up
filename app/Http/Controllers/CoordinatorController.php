<?php

namespace App\Http\Controllers;

use App\Models\WorkOfExtension;
use App\Models\WorkStatus;
use App\Services\WorkOfExtension\ApproveWorkService;
use App\Services\WorkOfExtension\RejectWorkService;
use App\Services\Dashboard\CoordinatorDashboardService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Controlador para funcionalidades de Coordinadores de Extensión
 * CU06: Recibir y revisar trabajos de su unidad
 * CU07: Solicitar subsanaciones al profesor
 * CU08: Avalar y remitir a Decano/Director
 */
class CoordinatorController extends Controller {
    use AuthorizesRequests;

    /**
     * Constructor
     */
    public function __construct() {
        // En Laravel 11, la validación se hace dentro de cada método
    }

    /**
     * CU06: Dashboard de coordinador - mostrar trabajos pendientes de revisión
     */
    public function dashboard(Request $request): View {
        // Validar permisos de coordinador
        $this->validateCoordinatorPermissions($request);

        $user = $request->user();

        Log::info('Coordinador accediendo a dashboard', [
            'user_id' => $user->getKey(),
            'organizational_unit_id' => $user->getAttribute('main_organizational_unit_id')
        ]);

        // Delegar lógica al servicio
        $service = new CoordinatorDashboardService(app(\App\Services\Authorization\WorkAuthorizationService::class));
        $data = $service->getDashboardData($user);

        return view('coordinator.dashboard', $data);
    }

    /**
     * CU06: Mostrar detalle de trabajo para revisión del coordinador
     */
    public function show(Request $request, WorkOfExtension $work): View|RedirectResponse {
        // Validar permisos de coordinador
        $this->validateCoordinatorPermissions($request);

        $user = $request->user();

        // Verificar que el trabajo pertenece a la unidad del coordinador
        $dashboardService = new CoordinatorDashboardService(app(\App\Services\Authorization\WorkAuthorizationService::class));
        if (!$dashboardService->canCoordinatorReviewWork($user, $work)) {
            return redirect()
                ->route('coordinator.dashboard')
                ->with('error', __('No tiene permisos para revisar este trabajo.'));
        }

        // Cargar relaciones necesarias
        $work->load([
            'workType',
            'currentStatus',
            'organizationalUnit',
            'responsibleUser',
            'statusHistory.changedBy',
            'statusHistory.fromStatus',
            'statusHistory.toStatus',
            'statusHistory.status',
            'projectDetail',
            'activityDetail',
            'publicationDetail',
            'technicalAssistanceDetail',
            'media'
        ]);

        Log::info('Coordinador revisando trabajo', [
            'work_id' => $work->getKey(),
            'coordinator_id' => $user->getKey()
        ]);

        return view('coordinator.show', [
            'work' => $work,
            'user' => $user,
            'canApprove' => $dashboardService->canApproveWork($work),
            'canRequestChanges' => $dashboardService->canRequestChanges($work)
        ]);
    }

    /**
     * CU08: Avalar trabajo y remitir a Decano/Director
     */
    public function approve(Request $request, WorkOfExtension $work): RedirectResponse {
        // Validar permisos de coordinador
        $this->validateCoordinatorPermissions($request);

        $user = $request->user();

        // Validar autorización específica para este trabajo
        $dashboardService = new CoordinatorDashboardService(app(\App\Services\Authorization\WorkAuthorizationService::class));
        if (!$dashboardService->canCoordinatorReviewWork($user, $work) || !$dashboardService->canApproveWork($work)) {
            return redirect()
                ->route('coordinator.dashboard')
                ->with('error', __('No puede aprobar este trabajo en su estado actual.'));
        }

        // Validar comentarios opcionales
        $comments = $request->input('comments');

        try {
            // Lógica de negocio delegada al servicio
            $service = new ApproveWorkService();
            $service->approveByCoordinator($work, $user, $comments);

            Log::info('Trabajo aprobado por coordinador', [
                'work_id' => $work->getKey(),
                'coordinator_id' => $user->getKey(),
                'has_comments' => !empty($comments)
            ]);

            return redirect()
                ->route('coordinator.show', $work)
                ->with('success', __('Trabajo aprobado y enviado al Decano/Director para revisión.'));

        } catch (\Exception $e) {
            Log::error('Error al aprobar trabajo', [
                'work_id' => $work->getKey(),
                'coordinator_id' => $user->getKey(),
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->route('coordinator.show', $work)
                ->with('error', __('Error al aprobar el trabajo. Inténtelo de nuevo.'));
        }
    }

    /**
     * CU07: Solicitar subsanaciones al profesor
     */
    public function requestChanges(Request $request, WorkOfExtension $work): RedirectResponse {
        // Validar permisos de coordinador
        $this->validateCoordinatorPermissions($request);

        $user = $request->user();

        // Validar autorización específica para este trabajo
        $dashboardService = new CoordinatorDashboardService(app(\App\Services\Authorization\WorkAuthorizationService::class));
        if (!$dashboardService->canCoordinatorReviewWork($user, $work) || !$dashboardService->canRequestChanges($work)) {
            return redirect()
                ->route('coordinator.dashboard')
                ->with('error', __('No puede solicitar cambios a este trabajo en su estado actual.'));
        }

        // Validar que se proporcionaron comentarios
        $request->validate([
            'comments' => 'required|string|min:10|max:1000'
        ], [
            'comments.required' => 'Debe proporcionar comentarios explicando las subsanaciones requeridas.',
            'comments.min' => 'Los comentarios deben tener al menos 10 caracteres.',
            'comments.max' => 'Los comentarios no pueden exceder 1000 caracteres.'
        ]);

        try {
            // Lógica de negocio delegada al servicio
            $service = new RejectWorkService();
            $service->requestChangesFromCoordinator($work, $user, $request->input('comments'));

            Log::info('Subsanaciones solicitadas por coordinador', [
                'work_id' => $work->getKey(),
                'coordinator_id' => $user->getKey()
            ]);

            return redirect()
                ->route('coordinator.show', $work)
                ->with('success', __('Subsanaciones solicitadas. El profesor ha sido notificado.'));

        } catch (\Exception $e) {
            Log::error('Error al solicitar subsanaciones', [
                'work_id' => $work->getKey(),
                'coordinator_id' => $user->getKey(),
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->route('coordinator.show', $work)
                ->with('error', __('Error al solicitar subsanaciones. Inténtelo de nuevo.'));
        }
    }

    /**
     * Validar que el usuario tenga permisos de coordinador
     */
    private function validateCoordinatorPermissions(Request $request): void {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Usuario no autenticado.');
        }

        if (!$user->hasAnyRole(['coordinador_extension', 'super_admin'])) {
            Log::error('Acceso de coordinador validado', [
                'user_id' => $user->getKey(),
                'user_email' => $user->email,
                'roles' => $user->getRoleNames()->toArray()
            ]);
            abort(403, 'Acceso denegado. Se requiere rol de Coordinador de Extensión.');
        }

        Log::info('Acceso de coordinador validado', [
            'user_id' => $user->getKey(),
            'user_email' => $user->email,
            'roles' => $user->getRoleNames()->toArray()
        ]);
    }
}
