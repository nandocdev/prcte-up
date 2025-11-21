<?php

namespace App\Http\Controllers;

use App\Models\WorkOfExtension;
use App\Models\WorkStatus;
use App\Services\WorkOfExtension\ApproveWorkService;
use App\Services\WorkOfExtension\RejectWorkService;
use App\Services\Dashboard\DeanDirectorDashboardService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Controlador para funcionalidades de Decanos/Directores
 * CU10: Revisar trabajos avalados por coordinador
 * CU11: Aprobar y tramitar a VIEX
 */
class DeanDirectorController extends Controller {
    use AuthorizesRequests;

    /**
     * Constructor
     */
    public function __construct() {
        // La validación se hace dentro de cada método
    }

    /**
     * CU10: Dashboard de decano/director - mostrar trabajos avalados por coordinador
     */
    public function dashboard(Request $request): View {
        // Validar permisos de decano/director
        $this->validateDeanDirectorPermissions($request);

        $user = $request->user();

        Log::info('Decano/Director accediendo a dashboard', [
            'user_id' => $user->getKey(),
            'organizational_unit_id' => $user->getAttribute('main_organizational_unit_id')
        ]);

        // Delegar lógica al servicio
        $service = new DeanDirectorDashboardService(app(\App\Services\Authorization\WorkAuthorizationService::class));
        $data = $service->getDashboardData($user);

        return view('dean.dashboard', $data);
    }

    /**
     * CU10: Mostrar detalles de un trabajo específico para revisión
     */
    public function show(Request $request, WorkOfExtension $work): View
    {
        // Validar permisos de decano/director
        $this->validateDeanDirectorPermissions($request);

        $user = $request->user();

        // Delegar verificación de autorización al servicio
        $authService = app(\App\Services\Authorization\WorkAuthorizationService::class);
        if (!$authService->canDeanDirectorReviewWork($user, $work)) {
            return redirect()
                ->route('dean.dashboard')
                ->with('error', __('No tiene permisos para revisar este trabajo.'));
        }

        Log::info('Decano/Director revisando trabajo', [
            'work_id' => $work->getKey(),
            'dean_director_id' => $user->getKey()
        ]);

        return view('dean.show', [
            'work' => $work,
            'user' => $user,
            'canApprove' => $authService->canApproveWork($work),
            'canRequestChanges' => $authService->canRequestChanges($work)
        ]);
    }
    /**
     * CU11: Aprobar trabajo y tramitarlo a VIEX
     */
    public function approve(Request $request, WorkOfExtension $work): RedirectResponse {
        // Validar permisos de decano/director
        $this->validateDeanDirectorPermissions($request);

        $user = $request->user();

        // Delegar validación de autorización al servicio
        $authService = app(\App\Services\Authorization\WorkAuthorizationService::class);
        if (!$authService->canDeanDirectorReviewWork($user, $work) || !$authService->canApproveWork($work)) {
            return redirect()
                ->route('dean.dashboard')
                ->with('error', __('No puede aprobar este trabajo en su estado actual.'));
        }

        // Validar comentarios opcionales
        $comments = $request->input('comments');

        try {
            // Lógica de negocio delegada al servicio
            $service = new ApproveWorkService();
            $service->approveByDeanDirector($work, $user, $comments);

            Log::info('Trabajo aprobado por decano/director', [
                'work_id' => $work->getKey(),
                'dean_director_id' => $user->getKey(),
                'has_comments' => !empty($comments)
            ]);

            return redirect()
                ->route('dean.dashboard')
                ->with('success', __('Trabajo aprobado exitosamente y enviado a VIEX para evaluación final.'));

        } catch (\InvalidArgumentException $e) {
            Log::error('Error al aprobar trabajo por decano/director', [
                'work_id' => $work->getKey(),
                'dean_director_id' => $user->getKey(),
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with('error', __('Error al aprobar el trabajo: :error', ['error' => $e->getMessage()]));
        } catch (\Exception $e) {
            Log::error('Error inesperado al aprobar trabajo por decano/director', [
                'work_id' => $work->getKey(),
                'dean_director_id' => $user->getKey(),
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with('error', __('Ocurrió un error inesperado. Por favor, intente nuevamente.'));
        }
    }

    /**
     * CU10: Solicitar cambios/correcciones al coordinador
     */
    public function requestChanges(Request $request, WorkOfExtension $work): RedirectResponse {
        // Validar permisos de decano/director
        $this->validateDeanDirectorPermissions($request);

        $user = $request->user();

        // Delegar validación de autorización al servicio
        $authService = app(\App\Services\Authorization\WorkAuthorizationService::class);
        if (!$authService->canDeanDirectorReviewWork($user, $work) || !$authService->canRequestChanges($work)) {
            return redirect()
                ->route('dean.dashboard')
                ->with('error', __('No puede solicitar cambios a este trabajo en su estado actual.'));
        }

        // Validar que se proporcionaron comentarios
        $request->validate([
            'comments' => 'required|string|min:10|max:1000'
        ], [
            'comments.required' => 'Debe proporcionar comentarios explicando las correcciones requeridas.',
            'comments.min' => 'Los comentarios deben tener al menos 10 caracteres.',
            'comments.max' => 'Los comentarios no pueden exceder 1000 caracteres.'
        ]);

        try {
            // Lógica de negocio delegada al servicio
            $service = new RejectWorkService();
            $service->requestChangesFromDeanDirector($work, $user, $request->input('comments'));

            Log::info('Cambios solicitados por decano/director', [
                'work_id' => $work->getKey(),
                'dean_director_id' => $user->getKey(),
                'comments_length' => strlen($request->input('comments'))
            ]);

            return redirect()
                ->route('dean.dashboard')
                ->with('success', __('Solicitud de correcciones enviada exitosamente. El coordinador ha sido notificado.'));

        } catch (\InvalidArgumentException $e) {
            Log::error('Error al solicitar cambios por decano/director', [
                'work_id' => $work->getKey(),
                'dean_director_id' => $user->getKey(),
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with('error', __('Error al solicitar cambios: :error', ['error' => $e->getMessage()]));
        } catch (\Exception $e) {
            Log::error('Error inesperado al solicitar cambios por decano/director', [
                'work_id' => $work->getKey(),
                'dean_director_id' => $user->getKey(),
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with('error', __('Ocurrió un error inesperado. Por favor, intente nuevamente.'));
        }
    }

    /**
     * Validar que el usuario tenga permisos de decano/director
     */
    private function validateDeanDirectorPermissions(Request $request): void {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Usuario no autenticado.');
        }

        if (!$user->hasAnyRole(['decano_director', 'super_admin'])) {
            abort(403, 'Acceso denegado. Se requiere rol de Decano/Director.');
        }

        Log::info('Acceso de decano/director validado', [
            'user_id' => $user->getKey(),
            'user_email' => $user->email,
            'roles' => $user->getRoleNames()->toArray()
        ]);
    }
}
