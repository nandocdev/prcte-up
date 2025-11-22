<?php

namespace App\Http\Controllers;

use App\Models\WorkOfExtension;
use App\Models\WorkType;
use App\Models\OrganizationalUnit;
use App\Models\Certification;
use App\Models\SdgGoal;
use App\Models\InstitutionalProjectType;
use App\Http\Requests\RegisterWorkRequest;
use App\Http\Requests\StoreCompleteWorkRequest;
use App\Services\WorkOfExtension\CreateWorkService;
use App\Services\WorkOfExtension\UpdateWorkService;
use App\Services\WorkOfExtension\SubmitWorkService;
use App\Services\Dashboard\WorkListingService;
use App\Services\WorkOfExtension\PublicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Controlador principal para gestión de trabajos de extensión
 *
 * Casos de Uso Cubiertos:
 * - CU02: Registrar trabajo de extensión
 * - CU03: Editar trabajo de extensión
 * - CU04: Enviar trabajo a coordinador
 * - CU05: Eliminar trabajo de extensión
 * - CU09: Consultar estado del trabajo
 */
class WorkOfExtensionController extends Controller {
    use AuthorizesRequests;

    protected WorkListingService $workListingService;
    protected PublicationService $publicationService;

    public function __construct(
        WorkListingService $workListingService,
        PublicationService $publicationService
    ) {
        $this->workListingService = $workListingService;
        $this->publicationService = $publicationService;
    }
    /**
     * Display a listing of the resource.
     * Muestra dashboard con trabajos del usuario según su rol
     */
    public function index(Request $request): View {
        // Log para debug
        Log::info('Consultando trabajos de extensión', [
            'user_id' => $request->user()->getKey(),
            'filters' => $request->only(['status', 'work_type', 'academic_period', 'search'])
        ]);

        // Delegar lógica de listado al servicio
        $data = $this->workListingService->getWorksListing($request, $request->user());

        return view('works.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     * CU02: Formulario de registro de trabajo de extensión
     */
    public function create(): View {
        // Log para debug
        Log::info('Mostrando formulario de creación de trabajo', [
            'user_id' => Auth::id()
        ]);

        // Delegar obtención de datos maestros al modelo
        $workTypes = WorkType::getActiveTypes();
        // Log::info('Tipos de trabajo obtenidos', ['works_types' => $workTypes->pluck('id', 'name')]);
        $organizationalUnits = OrganizationalUnit::getUnitsForSelection();
        $sdgGoals = SdgGoal::orderBy('code')->get();
        $institutionalProjectTypes = InstitutionalProjectType::orderBy('name')->get();

        // Obtener configuración para dropdowns
        $workTypesConfig = config('work_types');

        return view('works.create', [
            'workTypes' => $workTypes,
            'organizationalUnits' => $organizationalUnits,
            'workTypesConfig' => $workTypesConfig,
            'user' => Auth::user(),
            'sdgGoals' => $sdgGoals,
            'institutionalProjectTypes' => $institutionalProjectTypes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * CU02: Procesar registro de nuevo trabajo de extensión
     */
    public function store(StoreCompleteWorkRequest $request): RedirectResponse {
        try {
            // Log para debug
            Log::info('Iniciando creación de trabajo', [
                'user_id' => $request->user()->getKey(),
                'has_files' => $request->hasFile('attachments')
            ]);

            // Obtener datos validados del Form Request
            $validated = $request->getValidatedData();

            // Log datos validados
            Log::info('Datos validados', $validated);

            // Lógica de negocio delegada al servicio
            $service = new CreateWorkService();
            $work = $service->execute($validated, $request->user());

            // Log trabajo creado
            Log::info('Trabajo creado', ['work_id' => $work->getKey()]);

            // evalúa si la respuesta de la logica de negocios es satisfactoria
            if (!$work) {
                Log::error('Error: trabajo no se creó correctamente');
                return redirect()
                    ->route('works.create')
                    ->with('error', __('Error al registrar el trabajo de extensión.'));
            }

            // Manejar archivos adjuntos si existen
            if ($request->hasFile('attachments')) {
                Log::info('Procesando archivos adjuntos');
                $work->handleAttachments($request->file('attachments'));
            }

            Log::info('Trabajo guardado exitosamente', ['work_id' => $work->getKey()]);

            return redirect()
                ->route('works.show', $work)
                ->with('success', __('Trabajo de extensión registrado exitosamente.'));

        } catch (\Exception $e) {
            Log::error('Error en store method', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('works.create')
                ->with('error', __('Error al procesar el formulario: ') . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * CU09: Consultar estado del trabajo
     */
    public function show(Request $request, WorkOfExtension $work): View {
        // Verificar autorización
        $this->authorize('view', $work);

        // Cargar relaciones necesarias
        $work->load([
            'workType',
            'responsibleUser',
            'organizationalUnit',
            'participants.user',
            'statusHistory.changedBy',
            'statusHistory.status',
            'media',
            'projectDetail.institutionalProjectType',
            'activityDetail',
            'publicationDetail',
            'technicalAssistanceDetail'
        ]);

        return view('works.show', [
            'work' => $work,
            'canEdit' => $request->user()->can('update', $work),
            'canSubmit' => $work->canBeSubmitted(),
            'timeline' => $work->getStatusTimeline()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * CU03: Formulario de edición de trabajo de extensión
     */
    public function edit(WorkOfExtension $work): View|RedirectResponse {
        // Verificar autorización
        $this->authorize('update', $work);

        // Solo permitir edición si está en borrador o en estados de corrección
        if (!$work->canBeEditedForCorrection()) {
            return redirect()
                ->route('works.show', $work)
                ->with('warning', __('Solo se pueden editar trabajos en estado borrador o devueltos para corrección.'));
        }

        // Log para debug
        Log::info('Mostrando formulario de edición de trabajo', [
            'work_id' => $work->getKey(),
            'user_id' => Auth::id()
        ]);

        // Delegar obtención de datos maestros al modelo (igual que create)
        $workTypes = WorkType::getActiveTypes();
        $organizationalUnits = OrganizationalUnit::getUnitsForSelection();
        $sdgGoals = SdgGoal::orderBy('code')->get();
        $institutionalProjectTypes = InstitutionalProjectType::orderBy('name')->get();

        // Cargar datos específicos del tipo de trabajo
        $work->load(['projectDetail', 'activityDetail', 'publicationDetail', 'technicalAssistanceDetail']);

        return view('works.edit', [
            'work' => $work,
            'workTypes' => $workTypes,
            'organizationalUnits' => $organizationalUnits,
            'periods' => config('work_types.academic_periods'),
            'config' => config('work_types'),
            'sdgGoals' => $sdgGoals,
            'institutionalProjectTypes' => $institutionalProjectTypes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * CU03: Actualización de trabajo de extensión
     */
    public function update(StoreCompleteWorkRequest $request, WorkOfExtension $work): RedirectResponse {
        // Verificar autorización
        $this->authorize('update', $work);

        // Validar que esté en estado editable
        if (!$work->canBeEditedForCorrection()) {
            return redirect()
                ->route('works.show', $work)
                ->with('error', __('Solo se pueden actualizar trabajos en estado borrador o devueltos para corrección.'));
        }

        // Log para debug detallado
        Log::info('Actualizando trabajo de extensión', [
            'work_id' => $work->getKey(),
            'user_id' => $request->user()->getKey(),
            'work_type_id' => $request->input('work_type_id'),
            'activity_type' => $request->input('activity_type'),
            'modality' => $request->input('modality'),
            'all_input' => $request->except(['attachments', '_token']),
            'validation_data' => $request->getValidatedData()
        ]);

        try {
            // Debug: Capturar todos los datos de entrada ANTES de validación
            Log::info('DEBUG: Datos RAW recibidos', [
                'all_data' => $request->all(),
                'activity_type_raw' => $request->input('activity_type'),
                'modality_raw' => $request->input('modality')
            ]);

            // Intentar obtener datos validados y capturar cualquier error
            try {
                $validatedData = $request->getValidatedData();
                Log::info('DEBUG: Datos validados exitosamente', $validatedData);
            } catch (\Exception $validationError) {
                Log::error('DEBUG: Error en validación', [
                    'message' => $validationError->getMessage(),
                    'errors' => $request->errors ?? 'N/A'
                ]);
                throw $validationError;
            }

            // Manejar eliminación de archivos antes de la actualización
            if ($request->filled('remove_media')) {
                $mediaToRemove = array_filter(explode(',', $request->input('remove_media')));
                foreach ($mediaToRemove as $mediaId) {
                    $media = $work->getMedia('attachments')->where('id', $mediaId)->first();
                    if ($media) {
                        $media->delete();
                        Log::info('Archivo eliminado', ['media_id' => $mediaId, 'work_id' => $work->getKey()]);
                    }
                }
            }

            // Delegar lógica de negocio al servicio
            $service = new UpdateWorkService();
            $updatedWork = $service->execute($work, $validatedData, $request->user());

            // Manejar archivos adjuntos si los hay
            if ($request->hasFile('attachments')) {
                $updatedWork->handleAttachments($request->file('attachments'));
            }

            return redirect()
                ->route('works.show', $updatedWork)
                ->with('success', __('Trabajo actualizado exitosamente.'));

        } catch (\Exception $e) {
            Log::error('Error al actualizar trabajo', [
                'work_id' => $work->getKey(),
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('Error al actualizar el trabajo. Por favor, inténtalo de nuevo.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * CU05: Eliminar trabajo de extensión (solo en borrador)
     */
    public function destroy(WorkOfExtension $work): RedirectResponse {
        // Log para debug
        Log::info('Intentando eliminar trabajo de extensión', [
            'work_id' => $work->getKey(),
            'user_id' => Auth::id(),
            'work_status' => $work->is_draft,
            'work_owner' => $work->getAttribute('primary_responsible_user_id')
        ]);

        // Verificar autorización
        $this->authorize('delete', $work);

        // Solo permitir eliminación si está en borrador
        if (!$work->isInDraft()) {
            Log::warning('Intento de eliminar trabajo que no está en borrador', [
                'work_id' => $work->getKey(),
                'user_id' => Auth::id(),
                'is_draft' => $work->is_draft
            ]);

            return redirect()
                ->route('works.show', $work)
                ->with('error', __('Solo se pueden eliminar trabajos en estado borrador.'));
        }

        try {
            // Obtener información antes de eliminar
            $workTitle = $work->getAttribute('title');
            $workId = $work->getKey();

            // Lógica de eliminación delegada al modelo
            $work->safeDelete();

            Log::info('Trabajo eliminado exitosamente', [
                'work_id' => $workId,
                'work_title' => $workTitle,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->route('works.index')
                ->with('success', __('Trabajo eliminado exitosamente.'));

        } catch (\Exception $e) {
            Log::error('Error al eliminar trabajo', [
                'work_id' => $work->getKey(),
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('works.show', $work)
                ->with('error', __('Error al eliminar el trabajo. Por favor, inténtalo de nuevo.'));
        }
    }

    /**
     * CU04: Enviar trabajo a coordinador para revisión
     */
    public function submit(Request $request, WorkOfExtension $work): RedirectResponse {
        // Log para debug del token CSRF
        Log::info('Submit request received', [
            'work_id' => $work->getKey(),
            'user_id' => $request->user()->getKey(),
            'has_csrf_token' => $request->has('_token'),
            'csrf_token_length' => strlen($request->input('_token', '')),
            'session_id' => session()->getId(),
            'method' => $request->method(),
            'all_input' => $request->all()
        ]);

        // Verificar autorización
        $this->authorize('update', $work);

        // Lógica de negocio delegada al servicio
        try {
            $service = new SubmitWorkService();
            $service->execute($work, $request->user());

            return redirect()
                ->route('works.show', $work)
                ->with('success', __('Trabajo enviado a coordinador de extensión para revisión.'));

        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('works.show', $work)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Reenviar trabajo después de realizar correcciones
     * CU05: Subsanar Trabajo Rechazado
     */
    public function resubmit(Request $request, WorkOfExtension $work): RedirectResponse {
        // Verificar autorización
        $this->authorize('update', $work);

        try {
            // Verificar que el trabajo está en un estado que permite reenvío
            $currentStatus = $work->currentStatus->name ?? '';
            $resubmitStates = [
                'Rechazado por Coordinador',
                'Rechazado por Decano/Director',
                'Rechazado por VIEX',
                'Devuelto para Corrección'
            ];

            if (!in_array($currentStatus, $resubmitStates)) {
                return redirect()
                    ->route('works.show', $work)
                    ->with('error', 'Este trabajo no se puede reenviar en su estado actual.');
            }

            // Reenviar con flag isResubmission=true para diferenciar notificación
            $service = new SubmitWorkService();
            $service->execute($work, $request->user(), true);

            return redirect()
                ->route('works.show', $work)
                ->with('success', 'Trabajo corregido y reenviado para revisión.');

        } catch (\Exception $e) {
            return redirect()
                ->route('works.show', $work)
                ->with('error', 'Error al reenviar el trabajo: ' . $e->getMessage());
        }
    }

    /**
     * Autorizar/Revocar publicación de resultados del trabajo
     * CU06: Autorizar Publicación de Resultados
     */
    public function authorizePublication(Request $request, WorkOfExtension $work): RedirectResponse
    {
        // Verificar autorización
        $this->authorize('update', $work);

        try {
            $isAuthorized = $request->boolean('authorized', true);

            // Delegar lógica de negocio al servicio
            $this->publicationService->authorizePublication($work, $request->user(), $isAuthorized);

            $message = $isAuthorized
                ? __('¡Autorización registrada exitosamente! VIEX ha sido notificado de su consentimiento para publicar este trabajo.')
                : __('Autorización de publicación revocada exitosamente. VIEX ha sido notificado del cambio.');

            return redirect()
                ->route('works.show', $work)
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()
                ->route('works.show', $work)
                ->with('error', __('Error al procesar la autorización: ') . $e->getMessage());
        }
    }

    /**
     * Descargar certificado del trabajo
     */
    public function downloadCertificate(Certification $certification) {
        $work = $certification->work;

        if (!$work) {
            abort(404, 'Trabajo no encontrado');
        }

        // Verificar autorización usando las policies
        $this->authorize('view', $work);

        try {
            return $certification->buildDownloadResponse();
        } catch (\RuntimeException $exception) {
            Log::warning('Archivo de certificación no disponible para descarga pública.', [
                'certification_id' => $certification->getKey(),
                'work_id' => $work->getKey(),
                'error' => $exception->getMessage(),
            ]);

            return redirect()->back()->with('error', __('certifications.download_missing_file'));
        } catch (\Throwable $exception) {
            Log::error('Error inesperado al descargar certificado.', [
                'certification_id' => $certification->getKey(),
                'work_id' => $work->getKey(),
                'error' => $exception->getMessage(),
            ]);

            return redirect()->back()->with('error', __('certifications.download_error'));
        }
    }
}
