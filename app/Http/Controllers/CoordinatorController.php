<?php

namespace App\Http\Controllers;

use App\Models\WorkOfExtension;
use App\Models\WorkStatus;
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

        // Obtener trabajos pendientes para este coordinador
        $pendingWorks = $this->getPendingWorksForCoordinator($user);

        // Obtener estadísticas
        $statistics = $this->getCoordinatorStatistics($user);

        // Obtener trabajos recientes (últimos 10)
        $recentWorks = $this->getRecentWorksForCoordinator($user);

        return view('coordinator.dashboard', [
            'pendingWorks' => $pendingWorks,
            'recentWorks' => $recentWorks,
            'statistics' => $statistics,
            'user' => $user
        ]);
    }

    /**
     * CU06: Mostrar detalle de trabajo para revisión del coordinador
     */
    public function show(Request $request, WorkOfExtension $work): View|RedirectResponse {
        // Validar permisos de coordinador
        $this->validateCoordinatorPermissions($request);

        $user = $request->user();

        // Verificar que el trabajo pertenece a la unidad del coordinador
        if (!$this->canCoordinatorReviewWork($user, $work)) {
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
            'canApprove' => $this->canApproveWork($work),
            'canRequestChanges' => $this->canRequestChanges($work)
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
        if (!$this->canCoordinatorReviewWork($user, $work) || !$this->canApproveWork($work)) {
            return redirect()
                ->route('coordinator.dashboard')
                ->with('error', __('No puede aprobar este trabajo en su estado actual.'));
        }

        // Validar comentarios opcionales
        $comments = $request->input('comments');

        try {
            // Lógica de negocio delegada al modelo
            $work->approveByCoordinator($user, $comments);

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
        if (!$this->canCoordinatorReviewWork($user, $work) || !$this->canRequestChanges($work)) {
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
            // Lógica de negocio delegada al modelo
            $work->requestChangesFromCoordinator($user, $request->input('comments'));

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

    // Métodos privados auxiliares

    /**
     * Obtener trabajos pendientes de revisión para el coordinador
     */
    private function getPendingWorksForCoordinator($user) {
        return WorkOfExtension::where('organizational_unit_id', $user->getAttribute('main_organizational_unit_id'))
            ->whereHas('currentStatus', function ($query) {
                $query->where('name', 'En Revisión Coordinador');
            })
            ->with(['workType', 'responsibleUser', 'currentStatus'])
            ->orderBy('submitted_at', 'asc')
            ->get();
    }

    /**
     * Obtener trabajos recientes procesados por el coordinador
     */
    private function getRecentWorksForCoordinator($user) {
        return WorkOfExtension::where('organizational_unit_id', $user->getAttribute('main_organizational_unit_id'))
            ->whereHas('statusHistory', function ($query) use ($user) {
                $query->where('changed_by_user_id', $user->getKey());
            })
            ->with(['workType', 'responsibleUser', 'currentStatus'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtener estadísticas para el dashboard del coordinador
     */
    private function getCoordinatorStatistics($user) {
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
                            $q->where('name', 'En Decano/Director');
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
     * Verificar si el coordinador puede revisar el trabajo
     */
    private function canCoordinatorReviewWork($user, $work): bool {
        // Super admin puede revisar cualquier trabajo
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // El trabajo debe ser de la unidad del coordinador
        return $work->getAttribute('organizational_unit_id') === $user->getAttribute('main_organizational_unit_id');
    }

    /**
     * CU08: Rechazar trabajo definitivamente (Solicitar correcciones)
     */
    public function reject(Request $request, WorkOfExtension $work): RedirectResponse {
        // Validar permisos de usuario
        $this->validateCoordinatorPermissions($request);

        $user = $request->user();
        $currentStatus = $work->currentStatus->name ?? '';

        // Verificar que el trabajo está en estado correcto para rechazar
        if (!in_array($currentStatus, ['Enviado a Coordinador', 'En Revisión Coordinador'])) {
            return redirect()
                ->route('coordinator.show', $work)
                ->with('error', __('Este trabajo no puede ser rechazado en su estado actual.'));
        }

        // Validar datos de entrada
        $request->validate([
            'comments' => 'required|string|min:10|max:2000',
        ], [
            'comments.required' => 'Debe proporcionar comentarios para el rechazo.',
            'comments.min' => 'Los comentarios deben tener al menos 10 caracteres.',
            'comments.max' => 'Los comentarios no pueden exceder 2000 caracteres.',
        ]);

        $comments = $request->input('comments');

        try {
            // Lógica de negocio delegada al modelo
            $work->rejectByCoordinator($user, $comments);

            Log::info('Trabajo rechazado por coordinador', [
                'work_id' => $work->getKey(),
                'coordinator_id' => $user->getKey(),
                'reason' => $comments
            ]);

            return redirect()
                ->route('coordinator.show', $work)
                ->with('success', __('Trabajo rechazado. El profesor ha sido notificado y debe realizar las correcciones indicadas.'));

        } catch (\Exception $e) {
            Log::error('Error al rechazar trabajo', [
                'work_id' => $work->getKey(),
                'coordinator_id' => $user->getKey(),
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->route('coordinator.show', $work)
                ->with('error', __('Error al rechazar el trabajo. Inténtelo de nuevo.'));
        }
    }

    /**
     * Verificar si el trabajo puede ser aprobado
     */
    private function canApproveWork($work): bool {
        return $work->currentStatus->name === 'En Revisión Coordinador';
    }

    /**
     * Verificar si se pueden solicitar cambios al trabajo
     */
    private function canRequestChanges($work): bool {
        return $work->currentStatus->name === 'En Revisión Coordinador';
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
