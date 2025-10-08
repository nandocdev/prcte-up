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

        // Obtener trabajos avalados pendientes para este decano/director
        $pendingWorks = $this->getPendingWorksForDeanDirector($user);

        // Obtener estadísticas
        $statistics = $this->getDeanDirectorStatistics($user);

        // Obtener trabajos procesados recientemente
        $recentWorks = $this->getRecentWorksProcessedByDeanDirector($user);

        return view('dean.dashboard', [
            'pendingWorks' => $pendingWorks,
            'statistics' => $statistics,
            'recentWorks' => $recentWorks,
            'user' => $user
        ]);
    }

    /**
     * CU10: Mostrar detalle de trabajo para revisión por decano/director
     */
    public function show(Request $request, WorkOfExtension $work): View|RedirectResponse {
        // Validar permisos de decano/director
        $this->validateDeanDirectorPermissions($request);

        $user = $request->user();

        // Verificar que el trabajo pertenece a la unidad del decano/director
        if (!$this->canDeanDirectorReviewWork($user, $work)) {
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
            'canApprove' => $this->canApproveWork($work),
            'canRequestChanges' => $this->canRequestChanges($work)
        ]);
    }

    /**
     * CU11: Aprobar trabajo y tramitarlo a VIEX
     */
    public function approve(Request $request, WorkOfExtension $work): RedirectResponse {
        // Validar permisos de decano/director
        $this->validateDeanDirectorPermissions($request);

        $user = $request->user();

        // Validar autorización específica para este trabajo
        if (!$this->canDeanDirectorReviewWork($user, $work) || !$this->canApproveWork($work)) {
            return redirect()
                ->route('dean.dashboard')
                ->with('error', __('No puede aprobar este trabajo en su estado actual.'));
        }

        // Validar comentarios opcionales
        $comments = $request->input('comments');

        try {
            // Lógica de negocio delegada al modelo
            $work->approveByDeanDirector($user, $comments);

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

        // Validar autorización específica para este trabajo
        if (!$this->canDeanDirectorReviewWork($user, $work) || !$this->canRequestChanges($work)) {
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
            // Lógica de negocio delegada al modelo
            $work->requestChangesFromDeanDirector($user, $request->input('comments'));

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
     * Obtener trabajos pendientes de revisión para el decano/director
     */
    private function getPendingWorksForDeanDirector($user) {
        return WorkOfExtension::where('organizational_unit_id', $user->getAttribute('main_organizational_unit_id'))
            ->whereHas('currentStatus', function ($query) {
                $query->where('name', 'Enviado a Decano/Director');
            })
            ->with(['workType', 'responsibleUser', 'currentStatus'])
            ->orderBy('updated_at', 'asc') // Más antiguos primero
            ->get();
    }

    /**
     * Obtener trabajos procesados recientemente por el decano/director
     */
    private function getRecentWorksProcessedByDeanDirector($user) {
        return WorkOfExtension::where('organizational_unit_id', $user->getAttribute('main_organizational_unit_id'))
            ->whereHas('statusHistory', function ($query) use ($user) {
                $query->where('changed_by_user_id', $user->getKey())
                    ->where('created_at', '>=', now()->subDays(30));
            })
            ->with(['workType', 'responsibleUser', 'currentStatus'])
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * CU11: Rechazar trabajo definitivamente
     */
    public function reject(Request $request, WorkOfExtension $work): RedirectResponse {
        // Validar permisos de usuario
        $this->validateDeanDirectorPermissions($request);

        $user = $request->user();
        $currentStatus = $work->currentStatus->name ?? '';

        // Verificar que el trabajo está en estado correcto para rechazar
        if (!in_array($currentStatus, ['Enviado a Decano/Director', 'En Revisión Decano/Director'])) {
            return redirect()
                ->route('dean.show', $work)
                ->with('error', __('Este trabajo no puede ser rechazado en su estado actual.'));
        }

        // Verificar autorización por unidad organizacional
        if (!$this->canDeanDirectorReviewWork($user, $work)) {
            abort(403, 'No tiene autorización para rechazar este trabajo.');
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
            $work->rejectByDeanDirector($user, $comments);

            Log::info('Trabajo rechazado por decano/director', [
                'work_id' => $work->getKey(),
                'dean_director_id' => $user->getKey(),
                'reason' => $comments
            ]);

            return redirect()
                ->route('dean.show', $work)
                ->with('success', __('Trabajo rechazado definitivamente. El profesor ha sido notificado.'));

        } catch (\Exception $e) {
            Log::error('Error al rechazar trabajo por decano/director', [
                'work_id' => $work->getKey(),
                'dean_director_id' => $user->getKey(),
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->route('dean.show', $work)
                ->with('error', __('Error al rechazar el trabajo. Inténtelo de nuevo.'));
        }
    }

    /**
     * Obtener estadísticas para el dashboard del decano/director
     */
    private function getDeanDirectorStatistics($user) {
        $unitId = $user->getAttribute('main_organizational_unit_id');

        return [
            'pending' => WorkOfExtension::where('organizational_unit_id', $unitId)
                ->whereHas('currentStatus', function ($query) {
                    $query->where('name', 'Enviado a Decano/Director');
                })
                ->count(),

            'approved_this_month' => WorkOfExtension::where('organizational_unit_id', $unitId)
                ->whereHas('statusHistory', function ($query) use ($user) {
                    $query->where('changed_by_user_id', $user->getKey())
                        ->whereHas('status', function ($statusQuery) {
                            $statusQuery->where('name', 'Enviado a VIEX');
                        })
                        ->where('created_at', '>=', now()->startOfMonth());
                })
                ->count(),

            'changes_requested_this_month' => WorkOfExtension::where('organizational_unit_id', $unitId)
                ->whereHas('statusHistory', function ($query) use ($user) {
                    $query->where('changed_by_user_id', $user->getKey())
                        ->whereHas('status', function ($statusQuery) {
                            $statusQuery->where('name', 'Rechazado por Decano/Director');
                        })
                        ->where('created_at', '>=', now()->startOfMonth());
                })
                ->count(),

            'total_unit_works' => WorkOfExtension::where('organizational_unit_id', $unitId)
                ->whereHas('currentStatus', function ($query) {
                    $query->whereNotIn('name', ['Borrador']);
                })
                ->count(),
        ];
    }

    /**
     * Verificar si el decano/director puede revisar un trabajo específico
     */
    private function canDeanDirectorReviewWork($user, WorkOfExtension $work): bool {
        // Super admin puede revisar cualquier trabajo
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // El decano/director debe ser de la misma unidad organizacional
        return $work->getAttribute('organizational_unit_id') === $user->getAttribute('main_organizational_unit_id');
    }

    /**
     * Verificar si el trabajo puede ser aprobado
     */
    private function canApproveWork($work): bool {
        return $work->currentStatus->name === 'Enviado a Decano/Director';
    }

    /**
     * Verificar si se pueden solicitar cambios al trabajo
     */
    private function canRequestChanges($work): bool {
        return $work->currentStatus->name === 'Enviado a Decano/Director';
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
