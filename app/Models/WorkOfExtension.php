<?php

namespace App\Models;

use App\Jobs\GenerateCertificationPdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Facades\Log;
use App\Models\WorkStatus;
use App\Models\WorkStatusHistory;
use App\Models\User;
use App\Models\OrganizationalUnit;

/**
 * Modelo principal para los trabajos de extensión
 * Tabla: work_of_extensions
 */
class WorkOfExtension extends Model implements HasMedia {
    use InteractsWithMedia;

    public const COORDINATOR_STATUS_NAMES = [
        'Enviado a Coordinador',
        'En Coordinador Extensión',
        'En Coordinador de Extensión',
        'En Revisión Coordinador',
        'En Corrección',
        'Pendiente Decano',
        'Aprobado por Coordinador',
        'Certificado',
        'Rechazado por VIEX',
    ];

    public const DEAN_STATUS_NAMES = [
        'Enviado a Decano/Director',
        'En Revisión Decano/Director',
        'Pendiente Decano',
        'Aprobado por Coordinador',
        'Pendiente VIEX',
        'Certificado',
        'Rechazado por VIEX',
    ];

    public const VIEX_STATUS_NAMES = [
        'Enviado a VIEX',
        'Pendiente VIEX',
        'En VIEX - Pendiente Asignación',
        'En VIEX - En Evaluación',
        'En Evaluación VIEX',
        'En VIEX - Aprobado',
        'Aprobado Internamente',
        'Certificado',
        'Rechazado',
        'Rechazado por VIEX',
    ];

    protected $fillable = [
        'title',
        'work_type_id',
        'primary_responsible_user_id',
        'organizational_unit_id',
        'current_status_id',
        'start_date',
        'end_date',
        'publication_consent',
        'is_draft',
        'submitted_at',
        'description',
        'academic_period',
        'responsible_phone',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'submitted_at' => 'date',
        'publication_consent' => 'boolean',
        'is_draft' => 'boolean',
    ];

    // Relaciones

    /**
     * Tipo de trabajo de extensión
     */
    public function workType() {
        return $this->belongsTo(WorkType::class);
    }

    /**
     * Usuario responsable principal del trabajo
     */
    public function responsibleUser() {
        return $this->belongsTo(User::class, 'primary_responsible_user_id');
    }

    /**
     * Alias para responsibleUser - para compatibilidad con vistas
     */
    public function primaryResponsible() {
        return $this->belongsTo(User::class, 'primary_responsible_user_id');
    }

    /**
     * Unidad organizacional que presenta el trabajo
     */
    public function organizationalUnit() {
        return $this->belongsTo(OrganizationalUnit::class);
    }

    /**
     * Estado actual del trabajo
     */
    public function currentStatus() {
        return $this->belongsTo(WorkStatus::class, 'current_status_id');
    }

    /**
     * Historial de cambios de estado
     */
    public function statusHistory() {
        return $this->hasMany(WorkStatusHistory::class)->orderBy('created_at');
    }

    /**
     * Participantes del trabajo
     */
    public function participants() {
        return $this->hasMany(WorkParticipant::class);
    }

    /**
     * Detalles del proyecto (si es tipo proyecto)
     */
    public function projectDetail() {
        return $this->hasOne(ProjectDetail::class);
    }

    /**
     * Detalles de actividad (si es tipo actividad)
     */
    public function activityDetail() {
        return $this->hasOne(ActivityDetail::class);
    }

    /**
     * Detalles de publicación (si es tipo publicación)
     */
    public function publicationDetail() {
        return $this->hasOne(PublicationDetail::class);
    }

    /**
     * Detalles de asistencia técnica (si es tipo asistencia técnica)
     */
    public function technicalAssistanceDetail() {
        return $this->hasOne(TechnicalAssistanceDetail::class);
    }

    /**
     * Certificación del trabajo
     */
    public function certification() {
        return $this->hasOne(Certification::class);
    }

    /**
     * Evaluadores asignados al trabajo (relación pivote con metadatos)
     */
    public function workEvaluators()
    {
        return $this->hasMany(WorkEvaluator::class);
    }

    /**
     * Usuarios evaluadores del trabajo (relación many-to-many)
     */
    public function evaluators()
    {
        return $this->belongsToMany(
            User::class,
            'work_evaluators',
            'work_of_extension_id',
            'evaluator_user_id'
        )
            ->withPivot([
            'role_evaluator',
                'assignment_notes',
                'assigned_at',
                'notified_at',
                'accepted_at',
                'completed_at',
                'status',
                'assigned_by_user_id'
            ])
            ->withTimestamps();
    }

    /**
     * Evaluaciones completas del trabajo
     */
    public function evaluations()
    {
        return $this->hasMany(WorkEvaluation::class);
    }

    // Scopes

    /**
     * Trabajos en estado borrador
     */
    public function scopeDrafts($query) {
        return $query->where('is_draft', true);
    }

    /**
     * Trabajos enviados (no borrador)
     */
    public function scopeSubmitted($query) {
        return $query->where('is_draft', false);
    }

    /**
     * Trabajos por tipo específico
     */
    public function scopeOfType($query, $workTypeId) {
        return $query->where('work_type_id', $workTypeId);
    }

    /**
     * Trabajos por unidad organizacional
     */
    public function scopeFromUnit($query, $unitId) {
        return $query->where('organizational_unit_id', $unitId);
    }

    /**
     * Trabajos con consentimiento de publicación
     */
    public function scopeWithPublicationConsent($query) {
        return $query->where('publication_consent', true);
    }

    /**
     * Scope: trabajos visibles para un profesor (solo sus trabajos)
     */
    public function scopeVisibleToProfessor($query, $user)
    {
        return $query->where('primary_responsible_user_id', $user->getKey());
    }

    /**
     * Scope: trabajos visibles para un coordinador (solo su unidad y que hayan sido enviados a coordinador)
     */
    public function scopeVisibleToCoordinator($query, $user)
    {
        $unitId = (int) $user->getAttribute('main_organizational_unit_id');
        if ($unitId === 0) {
            return $query->whereRaw('0 = 1');
        }

        $unitIds = OrganizationalUnit::descendantIds($unitId);

        $coordinatorUnit = OrganizationalUnit::find($unitId);
        if ($coordinatorUnit && $coordinatorUnit->getAttribute('parent_id')) {
            $parentBranchIds = OrganizationalUnit::descendantIds((int) $coordinatorUnit->getAttribute('parent_id'));
            $unitIds = array_values(array_unique(array_merge($unitIds, $parentBranchIds)));
        }

        $statuses = self::COORDINATOR_STATUS_NAMES;

        return $query->whereIn('organizational_unit_id', $unitIds)
            ->whereHas('currentStatus', function ($q) use ($statuses) {
                $q->whereIn('name', $statuses);
            });
    }

    /**
     * Scope: trabajos visibles para un decano/director (su unidad y subunidades, y que hayan sido aprobados por el coordinador y remitidos al decano)
     */
    public function scopeVisibleToDean($query, $user)
    {
        $unitId = (int) $user->getAttribute('main_organizational_unit_id');
        $unitIds = OrganizationalUnit::descendantIds($unitId);

        $statuses = self::DEAN_STATUS_NAMES;

        return $query->whereIn('organizational_unit_id', $unitIds)
            ->whereHas('currentStatus', function ($q) use ($statuses) {
                $q->whereIn('name', $statuses);
            });
    }

    /**
     * Scope: trabajos visibles para VIEX (los que pasaron por profesor->coordinador->decano, procesados o pendientes en VIEX)
     */
    public function scopeVisibleToViex($query)
    {
        $statuses = self::VIEX_STATUS_NAMES;

        return $query->whereHas('currentStatus', function ($q) use ($statuses) {
            $q->whereIn('name', $statuses);
        });
    }

    // Métodos estáticos para el controlador

    /**
     * Obtener trabajos filtrados según el rol del usuario
     */
    public static function getWorksForUser($user) {
        $query = self::query()->with(['workType', 'currentStatus', 'organizationalUnit'])->orderBy('created_at', 'desc');

        // Reglas de visibilidad centralizadas por rol
        if ($user->hasRole('profesor')) {
            return $query->visibleToProfessor($user)->get();
        }

        if ($user->hasRole('coordinador_extension')) {
            return $query->visibleToCoordinator($user)->get();
        }

        if ($user->hasRole('decano_director')) {
            return $query->visibleToDean($user)->get();
        }

        if ($user->hasRole('viex_admin')) {
            return $query->visibleToViex()->get();
        }

        // super_admin u otros roles con permisos amplios ven todos los trabajos
        return $query->get();
    }

    /**
     * Obtener estadísticas de trabajos para el usuario
     */
    public static function getStatisticsForUser($user) {
        $works = self::getWorksForUser($user);

        return [
            'total' => $works->count(),
            'draft' => $works->where('is_draft', '1')->count(),
            'in_review' => $works->whereNotIn('current_status_id', [1, 2])->where('current_status_id', '!=', null)->count(),
            'certified' => $works->where('currentStatus.name', 'Certificado')->count(),
        ];
    }

    /**
     * Crear trabajo completo desde formulario avanzado
     * Incluye creación de detalles específicos según tipo
     */
    public static function createFromCompleteRequest(array $data, $user): self {
        DB::beginTransaction();

        try {
            // Extraer datos estructurados del request
            $workData = $data['work_data'];
            $specificData = $data['specific_data'];
            $workType = $data['work_type'];

            // Crear trabajo principal
            $work = self::create([
                'title' => $workData['title'],
                'work_type_id' => $workData['work_type_id'],
                'primary_responsible_user_id' => $user->id,
                'organizational_unit_id' => $workData['organizational_unit_id'],
                'current_status_id' => 1, // Borrador
                'start_date' => $workData['start_date'],
                'end_date' => $workData['end_date'],
                'publication_consent' => $workData['publication_consent'] ?? false,
                'is_draft' => true,
                'description' => $workData['description'],
                'academic_period' => $workData['academic_period'] ?? config('work_types.current_academic_period'),
                'responsible_phone' => $workData['responsible_phone'] ?? null,
            ]);

            // Crear detalles específicos según tipo
            switch ($workType) {
                case '1': // Proyecto
                    $work->projectDetail()->create([
                        'project_category' => 'general', // Valor por defecto
                        'objectives' => $specificData['objectives'] ?? null,
                        'methodology' => $specificData['methodology'] ?? null,
                        'direct_beneficiaries' => $specificData['direct_beneficiaries'] ?? null,
                        'indirect_beneficiaries' => $specificData['indirect_beneficiaries'] ?? null,
                        'geographic_area' => $specificData['geographic_area'] ?? null,
                        'details_json' => json_encode([
                            'objectives' => $specificData['objectives'] ?? null,
                            'methodology' => $specificData['methodology'] ?? null,
                            'direct_beneficiaries' => $specificData['direct_beneficiaries'] ?? null,
                            'indirect_beneficiaries' => $specificData['indirect_beneficiaries'] ?? null,
                            'geographic_area' => $specificData['geographic_area'] ?? null,
                        ]),
                    ]);
                    break;

                case '2': // Actividad
                    $work->activityDetail()->create([
                        'activity_type' => $specificData['activity_type'] ?? null,
                        'modality' => $specificData['modality'] ?? null,
                        'duration_hours' => $specificData['duration_hours'] ?? null,
                        'expected_participants' => $specificData['expected_participants'] ?? null,
                        'participant_profile' => $specificData['participant_profile'] ?? null,
                        'offers_certificate' => $specificData['offers_certificate'] ?? false,
                        'details_json' => json_encode([
                            'activity_type' => $specificData['activity_type'] ?? null,
                            'modality' => $specificData['modality'] ?? null,
                            'duration_hours' => $specificData['duration_hours'] ?? null,
                            'expected_participants' => $specificData['expected_participants'] ?? null,
                            'participant_profile' => $specificData['participant_profile'] ?? null,
                            'offers_certificate' => $specificData['offers_certificate'] ?? false,
                        ]),
                    ]);
                    break;

                case '3': // Publicación
                    $work->publicationDetail()->create([
                        'publication_type' => $specificData['publication_type'] ?? null,
                        'editorial' => $specificData['editorial'] ?? null,
                        'isbn_issn' => $specificData['isbn_issn'] ?? null,
                        'target_audience' => $specificData['target_audience'] ?? null,
                        'language' => $specificData['language'] ?? 'español',
                        'print_run' => $specificData['print_run'] ?? null,
                        'relevance_justification' => $specificData['relevance_justification'] ?? '',
                        'publication_date' => $specificData['publication_date'] ?? now(),
                        'media_type' => $specificData['media_type'] ?? '',
                        'media_nature' => $specificData['media_nature'] ?? null,
                    ]);
                    break;

                case '4': // Asistencia Técnica
                    $work->technicalAssistanceDetail()->create([
                        'assistance_type' => $specificData['assistance_type'] ?? null,
                        'collaborating_institution' => $specificData['collaborating_institution'] ?? null,
                        'specialization_area' => $specificData['specialization_area'] ?? null,
                        'expected_products' => $specificData['expected_products'] ?? null,
                        'work_modality' => $specificData['work_modality'] ?? 'presencial',
                        'estimated_hours' => $specificData['estimated_hours'] ?? null,
                        'details_json' => json_encode([
                            'assistance_type' => $specificData['assistance_type'] ?? null,
                            'collaborating_institution' => $specificData['collaborating_institution'] ?? null,
                            'specialization_area' => $specificData['specialization_area'] ?? null,
                            'expected_products' => $specificData['expected_products'] ?? null,
                            'work_modality' => $specificData['work_modality'] ?? 'presencial',
                            'estimated_hours' => $specificData['estimated_hours'] ?? null,
                        ]),
                    ]);
                    break;
            }

            // Crear entrada inicial en historial de estados
            $draftStatus = WorkStatus::where('name', 'Borrador')->first();
            if (!$draftStatus) {
                $draftStatus = WorkStatus::first(); // Fallback al primer estado disponible
            }

            $work->statusHistory()->create([
                'from_status_id' => null, // Primer estado, no hay estado anterior
                'to_status_id' => $draftStatus->getKey(),
                'changed_by_user_id' => $user->getKey(),
                'comments' => 'Trabajo creado en estado borrador',
            ]);

            DB::commit();

            return $work;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Manejar archivos adjuntos después de la creación del trabajo
     */
    public function handleAttachments(array $files): void {
        foreach ($files as $file) {
            $this->addMedia($file)
                ->usingFileName($file->getClientOriginalName())
                ->toMediaCollection('attachments');
        }
    }

    // Métodos de estado y validación

    /**
     * Verificar si el trabajo está en borrador
     */
    public function isInDraft(): bool {
        return $this->is_draft === '1' || $this->is_draft === 1 || $this->is_draft === true;
    }

    /**
     * Verificar si el trabajo puede ser enviado
     * Valida todos los campos obligatorios según el tipo de trabajo
     * 
     * @return bool
     */
    public function canBeSubmitted(): bool {
        // Verificar que esté en borrador
        if (!$this->isInDraft()) {
            return false;
        }

        // Validar campos básicos obligatorios
        if (
            empty($this->title) ||
            empty($this->work_type_id) ||
            empty($this->description) ||
            empty($this->organizational_unit_id) ||
            empty($this->start_date) ||
            empty($this->end_date) ||
            empty($this->academic_period)
        ) {
            return false;
        }

        // Validar detalles específicos según tipo de trabajo
        return $this->validateSpecificDetails();
    }

    /**
     * Validar que los detalles específicos del tipo de trabajo estén completos
     * 
     * @return bool
     */
    protected function validateSpecificDetails(): bool
    {
        switch ($this->work_type_id) {
            case 1: // Proyecto
                $detail = $this->projectDetail;
                return $detail &&
                    !empty($detail->objectives) &&
                    !empty($detail->methodology);

            case 2: // Actividad
                $detail = $this->activityDetail;
                return $detail &&
                    !empty($detail->activity_type) &&
                    !empty($detail->modality);

            case 3: // Publicación
                $detail = $this->publicationDetail;
                return $detail &&
                    !empty($detail->publication_type);

            case 4: // Asistencia Técnica
                $detail = $this->technicalAssistanceDetail;
                return $detail &&
                    !empty($detail->assistance_type) &&
                    !empty($detail->collaborating_institution);

            default:
                return false;
        }
    }

    /**
     * Obtener lista de campos faltantes para poder enviar el trabajo
     * Útil para mostrar mensajes de error específicos al usuario
     * 
     * @return array
     */
    public function getMissingFieldsForSubmission(): array
    {
        $missing = [];

        // Verificar campos básicos
        if (empty($this->title)) {
            $missing[] = __('Título del trabajo');
        }
        if (empty($this->work_type_id)) {
            $missing[] = __('Tipo de trabajo');
        }
        if (empty($this->description)) {
            $missing[] = __('Descripción');
        }
        if (empty($this->organizational_unit_id)) {
            $missing[] = __('Unidad organizacional');
        }
        if (empty($this->start_date)) {
            $missing[] = __('Fecha de inicio');
        }
        if (empty($this->end_date)) {
            $missing[] = __('Fecha de finalización');
        }
        if (empty($this->academic_period)) {
            $missing[] = __('Período académico');
        }

        // Verificar campos específicos según tipo
        switch ($this->work_type_id) {
            case 1: // Proyecto
                $detail = $this->projectDetail;
                if (!$detail || empty($detail->objectives)) {
                    $missing[] = __('Objetivos del proyecto');
                }
                if (!$detail || empty($detail->methodology)) {
                    $missing[] = __('Metodología del proyecto');
                }
                break;

            case 2: // Actividad
                $detail = $this->activityDetail;
                if (!$detail || empty($detail->activity_type)) {
                    $missing[] = __('Tipo de actividad');
                }
                if (!$detail || empty($detail->modality)) {
                    $missing[] = __('Modalidad de la actividad');
                }
                break;

            case 3: // Publicación
                $detail = $this->publicationDetail;
                if (!$detail || empty($detail->publication_type)) {
                    $missing[] = __('Tipo de publicación');
                }
                break;

            case 4: // Asistencia Técnica
                $detail = $this->technicalAssistanceDetail;
                if (!$detail || empty($detail->assistance_type)) {
                    $missing[] = __('Tipo de asistencia técnica');
                }
                if (!$detail || empty($detail->collaborating_institution)) {
                    $missing[] = __('Institución colaboradora');
                }
                break;
        }

        return $missing;
    }

    /**
     * Obtener timeline de estados del trabajo
     */
    public function getStatusTimeline() {
        return $this->statusHistory()
            ->with(['status', 'changedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Enviar trabajo para revisión
     * Valida completitud antes de enviar y proporciona mensajes de error específicos
     * 
     * @param User $user Usuario que envía el trabajo
     * @throws \InvalidArgumentException Si el trabajo no cumple requisitos para ser enviado
     */
    /**
     * Envía el trabajo para revisión por el coordinador de extensión.
     * Puede usarse tanto para envío inicial (CU04) como reenvío después de rechazo (CU05).
     *
     * @param User $user Usuario que envía/reenvía
     * @param bool $isResubmission True si es un reenvío después de rechazo
     * @throws \InvalidArgumentException Si el trabajo no está listo para envío
     */
    public function submitForReview(User $user, bool $isResubmission = false): void
    {
        // Verificar validación completa
        if (!$this->canBeSubmitted()) {
            $missingFields = $this->getMissingFieldsForSubmission();

            if (!empty($missingFields)) {
                $message = __('El trabajo no puede ser enviado. Faltan los siguientes campos obligatorios: ') .
                    implode(', ', $missingFields);
                throw new \InvalidArgumentException($message);
            }

            throw new \InvalidArgumentException(__('El trabajo no puede ser enviado en su estado actual.'));
        }

        $submittedStatus = WorkStatus::where('name', 'Enviado a Coordinador')->first();

        if (!$submittedStatus) {
            // Intentar con nombres alternativos
            $submittedStatus = WorkStatus::where('name', 'En Coordinador de Extensión')
                ->orWhere('name', 'En Coordinador Extensión')
                ->first();

            if (!$submittedStatus) {
                throw new \InvalidArgumentException(__('No se encontró el estado de envío a coordinador. Contacte al administrador.'));
            }
        }

        // Hacer la transición y el marcado de envío de manera atómica
        DB::transaction(function () use ($submittedStatus, $user, $isResubmission) {
            // Mensaje diferenciado para historial
            $comment = $isResubmission
                ? 'Trabajo corregido y reenviado para revisión por el coordinador de extensión.'
                : 'Trabajo enviado para revisión por el coordinador de extensión.';

            // Cambiar estado (actualiza current_status_id y crea WorkStatusHistory)
            $this->changeStatus($submittedStatus, $user, $comment);

            // Marcar como enviado y timestamp
            $this->update([
                'is_draft' => '0',
                'submitted_at' => now(),
            ]);
        });

        // Disparar evento para notificar al coordinador (con contexto de reenvío)
        \App\Events\WorkSubmitted::dispatch($this, $user, $isResubmission);

        Log::info($isResubmission ? 'Trabajo reenviado (WorkSubmitted)' : 'Evento WorkSubmitted disparado', [
            'work_id' => $this->getKey(),
            'submitted_by' => $user->getKey(),
            'work_title' => $this->getAttribute('title'),
            'is_resubmission' => $isResubmission
        ]);
    }

    /**
     * Método centralizado para cambiar el estado de un trabajo.
     * Captura el estado anterior, realiza el update, crea el historial y hace logging.
     */
    public function changeStatus(WorkStatus $newStatus, User $by, ?string $comments = null): void
    {
        $oldStatusId = $this->getAttribute('current_status_id');

        // Actualizar estado en el modelo
        $this->update([
            'current_status_id' => $newStatus->getKey(),
        ]);

        // Registrar en historial con from_status correcto
        WorkStatusHistory::create([
            'work_of_extension_id' => $this->getKey(),
            'from_status_id' => $oldStatusId,
            'to_status_id' => $newStatus->getKey(),
            'changed_by_user_id' => $by->getKey(),
            'comments' => $comments,
        ]);

        Log::info('Cambio de estado registrado', [
            'work_id' => $this->getKey(),
            'from_status_id' => $oldStatusId,
            'to_status_id' => $newStatus->getKey(),
            'by' => $by->getKey(),
        ]);
    }

    /**
     * Verificar que el trabajo NO esté en el estado indicado
     */
    public function statusIsNot(string $statusName): bool
    {
        return ($this->currentStatus?->getAttribute('name') ?? null) !== $statusName;
    }

    /**
     * Iniciar la revisión por parte del coordinador: Enviado a Coordinador -> En Revisión Coordinador
     */
    public function startReviewByCoordinator(User $coordinator): void
    {
        $current = $this->currentStatus?->getAttribute('name') ?? null;

        if ($current !== 'Enviado a Coordinador') {
            throw new \InvalidArgumentException('El trabajo no está en el estado correcto para iniciar revisión por coordinador.');
        }

        $reviewStatus = WorkStatus::where('name', 'En Revisión Coordinador')->firstOrFail();

        $this->changeStatus($reviewStatus, $coordinator, 'Coordinador inició la revisión.');
    }

    /**
     * Aprobar trabajo por coordinador y enviarlo a Decano/Director
     * CU08: Avalar y remitir a Decano/Director
     */
    public function approveByCoordinator(User $user, ?string $comments): void {
        // Estados válidos: Enviado a Coordinador o En Revisión Coordinador
        $validStatuses = ['Enviado a Coordinador', 'En Revisión Coordinador'];
        $currentStatusName = $this->currentStatus->getAttribute('name');

        if (!in_array($currentStatusName, $validStatuses)) {
            throw new \InvalidArgumentException(
                "El trabajo no está en el estado correcto para ser aprobado. Estado actual: {$currentStatusName}"
            );
        }

        // Cambiar a estado "Enviado a Decano/Director"
        $approvedStatus = WorkStatus::where('name', 'Enviado a Decano/Director')->first();

        if (!$approvedStatus) {
            throw new \InvalidArgumentException('No se encontró el estado "Enviado a Decano/Director".');
        }

        // Guardar estado anterior ANTES de actualizar
        $oldStatusId = $this->getAttribute('current_status_id');

        $this->update([
            'current_status_id' => $approvedStatus->getKey(),
        ]);

        // Registrar en historial
        WorkStatusHistory::create([
            'work_of_extension_id' => $this->getKey(),
            'from_status_id' => $oldStatusId,
            'to_status_id' => $approvedStatus->getKey(),
            'changed_by_user_id' => $user->getKey(),
            'comments' => $comments ?? 'Trabajo aprobado por el coordinador de extensión.',
        ]);

        Log::info('Trabajo aprobado por coordinador', [
            'work_id' => $this->getKey(),
            'coordinator_id' => $user->getKey(),
            'new_status' => 'Enviado a Decano/Director'
        ]);

        // Disparar evento para notificar al Decano/Director y Profesor
        \App\Events\WorkApprovedByCoordinator::dispatch($this, $user, $comments);
    }

    /**
     * Solicitar subsanaciones al profesor desde coordinador
     * CU07: Solicitar subsanaciones al profesor
     */
    public function requestChangesFromCoordinator(User $user, string $comments): void {
        // Estados válidos: Enviado a Coordinador o En Revisión Coordinador
        $validStatuses = ['Enviado a Coordinador', 'En Revisión Coordinador'];
        $currentStatusName = $this->currentStatus->getAttribute('name');

        if (!in_array($currentStatusName, $validStatuses)) {
            throw new \InvalidArgumentException(
                "El trabajo no está en el estado correcto para solicitar cambios. Estado actual: {$currentStatusName}"
            );
        }

        // Cambiar a estado "Devuelto para Corrección"
        $changesStatus = WorkStatus::where('name', 'Devuelto para Corrección')->first();

        if (!$changesStatus) {
            throw new \InvalidArgumentException('No se encontró el estado "Devuelto para Corrección".');
        }

        // Guardar estado anterior ANTES de actualizar
        $oldStatusId = $this->getAttribute('current_status_id');

        $this->update([
            'current_status_id' => $changesStatus->getKey(),
            'is_draft' => '1', // Volver a borrador para que el profesor pueda editar
        ]);

        // Registrar en historial
        WorkStatusHistory::create([
            'work_of_extension_id' => $this->getKey(),
            'from_status_id' => $oldStatusId,
            'to_status_id' => $changesStatus->getKey(),
            'changed_by_user_id' => $user->getKey(),
            'comments' => $comments,
        ]);

        Log::info('Subsanaciones solicitadas por coordinador', [
            'work_id' => $this->getKey(),
            'coordinator_id' => $user->getKey(),
            'new_status' => 'Devuelto para Corrección'
        ]);

        // Disparar evento para notificar al profesor
        \App\Events\WorkChangesRequestedByCoordinator::dispatch($this, $user, $comments);
    }

    /**
     * Rechazar trabajo definitivamente desde coordinador
     * CU08: Rechazar trabajo por coordinador
     */
    public function rejectByCoordinator(User $user, string $comments): void {
        $currentStatus = $this->currentStatus->getAttribute('name');

        if (!in_array($currentStatus, ['Enviado a Coordinador', 'En Revisión Coordinador'])) {
            throw new \InvalidArgumentException(
                "El trabajo no está en el estado correcto para ser rechazado. Estado actual: {$currentStatus}"
            );
        }

        // Cambiar a estado "Rechazado por Coordinador"
        $rejectedStatus = WorkStatus::where('name', 'Rechazado por Coordinador')->first();

        if (!$rejectedStatus) {
            throw new \InvalidArgumentException('No se encontró el estado "Rechazado por Coordinador".');
        }

        // Guardar estado anterior ANTES de actualizar
        $oldStatusId = $this->getAttribute('current_status_id');

        $this->update([
            'current_status_id' => $rejectedStatus->getKey(),
            'is_draft' => '1', // Volver a borrador para que el profesor pueda editar
        ]);

        // Registrar en historial
        WorkStatusHistory::create([
            'work_of_extension_id' => $this->getKey(),
            'from_status_id' => $oldStatusId,
            'to_status_id' => $rejectedStatus->getKey(),
            'changed_by_user_id' => $user->getKey(),
            'comments' => $comments,
        ]);

        Log::info('Trabajo rechazado por coordinador', [
            'work_id' => $this->getKey(),
            'coordinator_id' => $user->getKey(),
            'reason' => $comments
        ]);

        // Disparar evento para notificar al profesor del rechazo
        \App\Events\WorkRejectedByCoordinator::dispatch($this, $user, $comments);
    }

    /**
     * Aprobar trabajo por decano/director y enviarlo a VIEX
     * CU11: Aprobar y tramitar a VIEX
     */
    public function approveByDeanDirector(User $user, ?string $comments): void {
        if ($this->currentStatus->getAttribute('name') !== 'Enviado a Decano/Director') {
            throw new \InvalidArgumentException('El trabajo no está en el estado correcto para ser aprobado por el decano/director.');
        }

        // Cambiar a estado "Enviado a VIEX"
        $approvedStatus = WorkStatus::where('name', 'Enviado a VIEX')->first();

        if (!$approvedStatus) {
            throw new \InvalidArgumentException('No se encontró el estado "Enviado a VIEX".');
        }

        // Guardar estado anterior ANTES de actualizar
        $oldStatusId = $this->getAttribute('current_status_id');

        $this->update([
            'current_status_id' => $approvedStatus->getKey(),
        ]);

        // Registrar en historial
        WorkStatusHistory::create([
            'work_of_extension_id' => $this->getKey(),
            'from_status_id' => $oldStatusId,
            'to_status_id' => $approvedStatus->getKey(),
            'changed_by_user_id' => $user->getKey(),
            'comments' => $comments ?? 'Trabajo aprobado por el decano/director.',
        ]);

        Log::info('Trabajo aprobado por decano/director', [
            'work_id' => $this->getKey(),
            'dean_director_id' => $user->getKey(),
            'new_status' => 'Enviado a VIEX'
        ]);

        // TODO: Disparar evento para notificar a VIEX
    }

    /**
     * Solicitar correcciones desde decano/director
     * CU10: Devolver trabajo al coordinador con observaciones
     */
    public function requestChangesFromDeanDirector(User $user, string $comments): void {
        if ($this->currentStatus->getAttribute('name') !== 'Enviado a Decano/Director') {
            throw new \InvalidArgumentException('El trabajo no está en el estado correcto para solicitar correcciones.');
        }

        // Cambiar a estado "Rechazado por Decano/Director"
        $changesStatus = WorkStatus::where('name', 'Rechazado por Decano/Director')->first();

        if (!$changesStatus) {
            throw new \InvalidArgumentException('No se encontró el estado "Rechazado por Decano/Director".');
        }

        // Guardar estado anterior ANTES de actualizar
        $oldStatusId = $this->getAttribute('current_status_id');

        $this->update([
            'current_status_id' => $changesStatus->getKey(),
            'is_draft' => '1', // Volver a borrador para que el profesor pueda editar
        ]);

        // Registrar en historial
        WorkStatusHistory::create([
            'work_of_extension_id' => $this->getKey(),
            'from_status_id' => $oldStatusId,
            'to_status_id' => $changesStatus->getKey(),
            'changed_by_user_id' => $user->getKey(),
            'comments' => $comments,
        ]);

        Log::info('Correcciones solicitadas por decano/director', [
            'work_id' => $this->getKey(),
            'dean_director_id' => $user->getKey(),
            'new_status' => 'Rechazado por Decano/Director'
        ]);

        // TODO: Disparar evento para notificar al coordinador
    }

    /**
     * Rechazar trabajo definitivamente desde decano/director
     * CU11: Rechazar trabajo por decano/director
     */
    public function rejectByDeanDirector(User $user, string $comments): void {
        $currentStatus = $this->currentStatus->getAttribute('name');

        if (!in_array($currentStatus, ['Enviado a Decano/Director', 'En Revisión Decano/Director'])) {
            throw new \InvalidArgumentException('El trabajo no está en el estado correcto para ser rechazado por el decano/director.');
        }

        // Cambiar a estado "Rechazado por Decano/Director"
        $rejectedStatus = WorkStatus::where('name', 'Rechazado por Decano/Director')->first();

        if (!$rejectedStatus) {
            throw new \InvalidArgumentException('No se encontró el estado "Rechazado por Decano/Director".');
        }

        // Guardar estado anterior ANTES de actualizar
        $oldStatusId = $this->getAttribute('current_status_id');

        $this->update([
            'current_status_id' => $rejectedStatus->getKey(),
            'is_draft' => '1', // Volver a borrador para que el profesor pueda editar
        ]);

        // Registrar en historial
        WorkStatusHistory::create([
            'work_of_extension_id' => $this->getKey(),
            'from_status_id' => $oldStatusId,
            'to_status_id' => $rejectedStatus->getKey(),
            'changed_by_user_id' => $user->getKey(),
            'comments' => $comments,
        ]);

        Log::info('Trabajo rechazado definitivamente por decano/director', [
            'work_id' => $this->getKey(),
            'dean_director_id' => $user->getKey(),
            'reason' => $comments
        ]);

        \App\Events\WorkRejectedByDeanDirector::dispatch($this, $user, $comments);
    }

    /**
     * Actualizar trabajo completo desde formulario avanzado
     * Reutiliza la lógica de createFromCompleteRequest adaptada para actualización
     */
    public function updateFromCompleteRequest(array $data, $user): self {
        DB::beginTransaction();

        try {
            // Extraer datos estructurados del request (igual que en creación)
            $workData = $data['work_data'];
            $specificData = $data['specific_data'];
            $workType = (string) ($data['work_type'] ?? $workData['work_type_id']);
            $previousType = (string) $this->getAttribute('work_type_id');
            $newWorkTypeId = (string) ($workData['work_type_id'] ?? $previousType);

            // Actualizar trabajo principal
            $this->update([
                'title' => $workData['title'],
                'work_type_id' => $workData['work_type_id'],
                'organizational_unit_id' => $workData['organizational_unit_id'],
                'start_date' => $workData['start_date'],
                'end_date' => $workData['end_date'],
                'publication_consent' => isset($workData['publication_consent']) ? (bool) $workData['publication_consent'] : false,
                'description' => $workData['description'],
                'academic_period' => $workData['academic_period'] ?? config('work_types.current_academic_period'),
                'responsible_phone' => $workData['responsible_phone'] ?? null,
            ]);

            // Limpiar detalles que no correspondan al nuevo tipo
            if ($previousType !== $newWorkTypeId) {
                $this->removeDetailRecordsExcept($newWorkTypeId);
            }

            // Actualizar o crear detalles específicos según tipo
            switch ($workType) {
                case '1': // Proyecto
                    $this->projectDetail()->updateOrCreate(
                        ['work_of_extension_id' => $this->getKey()],
                        [
                            'project_category' => 'general',
                            'objectives' => $specificData['objectives'] ?? null,
                            'methodology' => $specificData['methodology'] ?? null,
                            'direct_beneficiaries' => $specificData['direct_beneficiaries'] ?? null,
                            'indirect_beneficiaries' => $specificData['indirect_beneficiaries'] ?? null,
                            'geographic_area' => $specificData['geographic_area'] ?? null,
                            'details_json' => json_encode([
                                'objectives' => $specificData['objectives'] ?? null,
                                'methodology' => $specificData['methodology'] ?? null,
                                'direct_beneficiaries' => $specificData['direct_beneficiaries'] ?? null,
                                'indirect_beneficiaries' => $specificData['indirect_beneficiaries'] ?? null,
                                'geographic_area' => $specificData['geographic_area'] ?? null,
                            ]),
                        ]
                    );
                    break;

                case '2': // Actividad
                    $this->activityDetail()->updateOrCreate(
                        ['work_of_extension_id' => $this->getKey()],
                        [
                            'activity_type' => $specificData['activity_type'] ?? null,
                            'modality' => $specificData['modality'] ?? null,
                            'duration_hours' => $specificData['duration_hours'] ?? null,
                            'expected_participants' => $specificData['expected_participants'] ?? null,
                            'participant_profile' => $specificData['participant_profile'] ?? null,
                            'offers_certificate' => isset($specificData['offers_certificate']) ? (bool) $specificData['offers_certificate'] : false,
                            'details_json' => json_encode([
                                'activity_type' => $specificData['activity_type'] ?? null,
                                'modality' => $specificData['modality'] ?? null,
                                'duration_hours' => $specificData['duration_hours'] ?? null,
                                'expected_participants' => $specificData['expected_participants'] ?? null,
                                'participant_profile' => $specificData['participant_profile'] ?? null,
                                'offers_certificate' => isset($specificData['offers_certificate']) ? (bool) $specificData['offers_certificate'] : false,
                            ]),
                        ]
                    );
                    break;

                case '3': // Publicación
                    $this->publicationDetail()->updateOrCreate(
                        ['work_of_extension_id' => $this->getKey()],
                        [
                            'publication_type' => $specificData['publication_type'] ?? null,
                            'editorial' => $specificData['editorial'] ?? null,
                            'isbn_issn' => $specificData['isbn_issn'] ?? null,
                            'target_audience' => $specificData['target_audience'] ?? null,
                            'language' => $specificData['language'] ?? 'español',
                            'print_run' => $specificData['print_run'] ?? null,
                            'relevance_justification' => $specificData['relevance_justification'] ?? null,
                            'publication_date' => $specificData['publication_date'] ?? null,
                            'media_type' => $specificData['media_type'] ?? null,
                            'media_nature' => $specificData['media_nature'] ?? null,
                        ]
                    );
                    break;

                case '4': // Asistencia Técnica
                    $this->technicalAssistanceDetail()->updateOrCreate(
                        ['work_of_extension_id' => $this->getKey()],
                        [
                            'assistance_type' => $specificData['assistance_type'] ?? null,
                            'collaborating_institution' => $specificData['collaborating_institution'] ?? null,
                            'specialization_area' => $specificData['specialization_area'] ?? null,
                            'expected_products' => $specificData['expected_products'] ?? null,
                            'work_modality' => $specificData['work_modality'] ?? 'presencial',
                            'estimated_hours' => $specificData['estimated_hours'] ?? null,
                            'details_json' => json_encode([
                                'assistance_type' => $specificData['assistance_type'] ?? null,
                                'collaborating_institution' => $specificData['collaborating_institution'] ?? null,
                                'specialization_area' => $specificData['specialization_area'] ?? null,
                                'expected_products' => $specificData['expected_products'] ?? null,
                                'work_modality' => $specificData['work_modality'] ?? 'presencial',
                                'estimated_hours' => $specificData['estimated_hours'] ?? null,
                            ]),
                        ]
                    );
                    break;
            }

            // Registrar actualización en historial de estados
            $this->statusHistory()->create([
                'from_status_id' => $this->getAttribute('current_status_id'), // Estado actual
                'to_status_id' => $this->getAttribute('current_status_id'), // No cambia estado
                'changed_by_user_id' => $user->getKey(),
                'comments' => 'Trabajo actualizado por el usuario (edición completa)',
            ]);

            DB::commit();

            return $this->fresh([
                'projectDetail',
                'activityDetail',
                'publicationDetail',
                'technicalAssistanceDetail',
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Eliminar detalles específicos que ya no corresponden al tipo de trabajo actual.
     */
    protected function removeDetailRecordsExcept(string $currentType): void
    {
        if ($currentType !== '1') {
            $this->projectDetail()->delete();
        }

        if ($currentType !== '2') {
            $this->activityDetail()->delete();
        }

        if ($currentType !== '3') {
            $this->publicationDetail()->delete();
        }

        if ($currentType !== '4') {
            $this->technicalAssistanceDetail()->delete();
        }
    }

    /**
     * Actualizar trabajo desde request (método simple - DEPRECADO)
     * @deprecated Usar updateFromCompleteRequest() en su lugar
     */
    public function updateFromRequest(array $validated, User $user): void {
        $this->update($validated);

        // Registrar cambio en historial si es necesario
        WorkStatusHistory::create([
            'work_of_extension_id' => $this->getKey(),
            'from_status_id' => $this->getAttribute('current_status_id'),
            'to_status_id' => $this->getAttribute('current_status_id'), // No cambia estado
            'changed_by_user_id' => $user->getKey(),
            'comments' => 'Trabajo actualizado por el usuario.',
        ]);
    }

    /**
     * Eliminar trabajo de forma segura
     * Solo se pueden eliminar trabajos en estado borrador
     */
    public function safeDelete(): void {
        if (!$this->isInDraft()) {
            throw new \InvalidArgumentException('Solo se pueden eliminar trabajos en estado borrador.');
        }

        Log::info('Iniciando eliminación segura de trabajo', [
            'work_id' => $this->getKey(),
            'title' => $this->getAttribute('title')
        ]);

        DB::transaction(function () {
            try {
                // Eliminar archivos relacionados
                if ($this->hasMedia('attachments')) {
                    $this->clearMediaCollection('attachments');
                    Log::info('Archivos eliminados', ['work_id' => $this->getKey()]);
                }

                // Eliminar detalles específicos según el tipo
                switch ($this->getAttribute('work_type_id')) {
                    case 1: // Proyecto
                        if ($this->projectDetail) {
                            $this->projectDetail->delete();
                        }
                        break;
                    case 2: // Actividad
                        if ($this->activityDetail) {
                            $this->activityDetail->delete();
                        }
                        break;
                    case 3: // Publicación
                        if ($this->publicationDetail) {
                            $this->publicationDetail->delete();
                        }
                        break;
                    case 4: // Asistencia Técnica
                        if ($this->technicalAssistanceDetail) {
                            $this->technicalAssistanceDetail->delete();
                        }
                        break;
                }
                Log::info('Detalles específicos eliminados', ['work_id' => $this->getKey()]);

                // Eliminar registros relacionados
                $this->statusHistory()->delete();
                Log::info('Historial de estados eliminado', ['work_id' => $this->getKey()]);

                if ($this->participants()) {
                    $this->participants()->delete();
                    Log::info('Participantes eliminados', ['work_id' => $this->getKey()]);
                }

                // Eliminar el trabajo principal
                $workId = $this->getKey();
                $this->delete();
                Log::info('Trabajo principal eliminado', ['work_id' => $workId]);

            } catch (\Exception $e) {
                Log::error('Error durante eliminación segura', [
                    'work_id' => $this->getKey(),
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        });
    }

    // ==========================================
    // MÉTODOS PARA VIEX ADMIN (CU12-CU15)
    // ==========================================

    /**
     * CU9: Recibir trabajo en VIEX (transición desde Decano/Director)
     *
     * @param User $viexAdmin Usuario de VIEX que recibe el trabajo
     * @param string|null $comments Comentarios al recibir
     * @return void
     */
    public function receiveInViex(User $viexAdmin, ?string $comments = null): void
    {
        if ($this->statusIsNot('Enviado a VIEX')) {
            throw new \InvalidArgumentException('El trabajo debe estar "Enviado a VIEX" para ser recibido.');
        }

        $status = WorkStatus::where('name', 'En VIEX - Pendiente Asignación')->firstOrFail();
        $this->changeStatus($status, $viexAdmin, $comments ?: 'Trabajo recibido en VIEX, pendiente asignación de evaluadores');

        Log::info('Trabajo recibido en VIEX', [
            'work_id' => $this->getKey(),
            'received_by' => $viexAdmin->getKey(),
        ]);
    }

    /**
     * CU9: Asignar evaluador a un trabajo
     *
     * @param User $evaluator Usuario evaluador
     * @param User $assignedBy Usuario que asigna (VIEX admin)
     * @param string $role Rol del evaluador: 'lead_evaluator' o 'evaluator'
     * @param string|null $assignmentNotes Notas de asignación
     * @return WorkEvaluator
     * @throws \InvalidArgumentException
     */
    public function assignEvaluator(User $evaluator, User $assignedBy, string $role = 'evaluator', ?string $assignmentNotes = null): WorkEvaluator
    {
        if (!in_array($this->currentStatus->name, ['En VIEX - Pendiente Asignación', 'En VIEX - En Evaluación'])) {
            throw new \InvalidArgumentException('El trabajo debe estar en VIEX para asignar evaluadores.');
        }

        // Verificar si el evaluador ya está asignado
        $existingAssignment = $this->workEvaluators()
            ->where('evaluator_user_id', $evaluator->id)
            ->first();

        if ($existingAssignment) {
            throw new \InvalidArgumentException('Este evaluador ya está asignado a este trabajo.');
        }

        // Crear la asignación
        $workEvaluator = $this->workEvaluators()->create([
            'evaluator_user_id' => $evaluator->id,
            'assigned_by_user_id' => $assignedBy->id,
            'role_evaluator' => $role,
            'assignment_notes' => $assignmentNotes,
            'assigned_at' => now(),
            'status' => WorkEvaluator::STATUS_PENDING,
        ]);

        Log::info('Evaluador asignado al trabajo', [
            'work_id' => $this->getKey(),
            'evaluator_id' => $evaluator->id,
            'role_evaluator' => $role,
            'assigned_by' => $assignedBy->id,
        ]);

        return $workEvaluator;
    }

    /**
     * CU9: Iniciar evaluación en VIEX (cuando hay al menos un evaluador asignado)
     *
     * @param User $viexAdmin Usuario de VIEX
     * @param string|null $comments Comentarios
     * @return void
     * @throws \InvalidArgumentException
     */
    public function startViexEvaluation(User $viexAdmin, ?string $comments = null): void
    {
        if ($this->statusIsNot('En VIEX - Pendiente Asignación')) {
            throw new \InvalidArgumentException('El trabajo debe estar "En VIEX - Pendiente Asignación" para iniciar evaluación.');
        }

        // Verificar que hay al menos un evaluador asignado
        $evaluatorsCount = $this->workEvaluators()->count();
        if ($evaluatorsCount === 0) {
            throw new \InvalidArgumentException('Debe asignar al menos un evaluador antes de iniciar la evaluación.');
        }

        $status = WorkStatus::where('name', 'En VIEX - En Evaluación')->firstOrFail();
        $this->changeStatus($status, $viexAdmin, $comments ?: "Evaluación iniciada con {$evaluatorsCount} evaluador(es) asignado(s)");

        Log::info('Evaluación VIEX iniciada', [
            'work_id' => $this->getKey(),
            'evaluators_count' => $evaluatorsCount,
        ]);
    }

    /**
     * CU9: Verificar si todas las evaluaciones están completadas
     *
     * @return bool
     */
    public function allEvaluationsCompleted(): bool
    {
        $totalEvaluators = $this->workEvaluators()->count();

        if ($totalEvaluators === 0) {
            return false;
        }

        $completedEvaluations = $this->evaluations()
            ->where('status', WorkEvaluation::STATUS_SUBMITTED)
            ->count();

        return $completedEvaluations === $totalEvaluators;
    }

    /**
     * CU9: Obtener resumen de evaluaciones del trabajo
     *
     * @return array
     */
    public function getEvaluationSummary(): array
    {
        $evaluations = $this->evaluations()
            ->with(['evaluator', 'workEvaluator', 'evaluationDetails.criteria'])
            ->get();

        $totalEvaluators = $this->workEvaluators()->count();
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
        $leadEvaluators = $this->workEvaluators()
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
            'all_completed' => $this->allEvaluationsCompleted(),
            'recommendation' => $this->getEvaluationRecommendation($approvals, $rejections, $completedCount),
        ];
    }

    /**
     * CU9: Obtener recomendación basada en evaluaciones
     *
     * @param int $approvals
     * @param int $rejections
     * @param int $total
     * @return string
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

    /**
     * CU13: Asignar trabajo a evaluador específico (método legacy - usar assignEvaluator)
     *
     * @param User $evaluator Usuario evaluador
     * @param User $assignedBy Usuario que asigna
     * @param string|null $instructions Instrucciones para el evaluador
     * @return void
     */
    public function assignToEvaluator(User $evaluator, User $assignedBy, ?string $instructions = null): void {
        if ($this->statusIsNot('Enviado a VIEX')) {
            throw new \InvalidArgumentException('El trabajo debe estar en estado "Enviado a VIEX" para asignar evaluador.');
        }

        $status = WorkStatus::where('name', 'En Evaluación VIEX')->firstOrFail();
        $this->changeStatus($status, $assignedBy, $instructions ? "Asignado a: {$evaluator->name}. Instrucciones: {$instructions}" : "Asignado a: {$evaluator->name}");

        Log::info('Trabajo asignado a evaluador', [
            'work_id' => $this->getKey(),
            'evaluator_id' => $evaluator->getKey(),
            'assigned_by' => $assignedBy->getKey(),
        ]);
    }

    /**
     * CU14: Aprobar trabajo por VIEX (evaluación positiva)
     *
     * @param User $evaluator Usuario que evalúa
     * @param string|null $comments Comentarios de evaluación
     * @param string|null $recommendations Recomendaciones
     * @return void
     */
    public function approveByViex(User $evaluator, ?string $comments = null, ?string $recommendations = null): void {
        if ($this->statusIsNot('En VIEX - En Evaluación')) {
            throw new \InvalidArgumentException('El trabajo debe estar "En VIEX - En Evaluación" para poder ser aprobado.');
        }

        $status = WorkStatus::where('name', 'En VIEX - Aprobado')->firstOrFail();

        $finalComments = collect([
            $comments ? "Evaluación: {$comments}" : null,
            $recommendations ? "Recomendaciones: {$recommendations}" : null
        ])->filter()->implode(' | ');

        $this->changeStatus($status, $evaluator, $finalComments ?: 'Trabajo aprobado por VIEX');

        Log::info('Trabajo aprobado por VIEX', [
            'work_id' => $this->getKey(),
            'evaluator_id' => $evaluator->getKey(),
        ]);
    }

    /**
     * CU14: Rechazar trabajo por VIEX (evaluación negativa)
     *
     * @param User $evaluator Usuario que evalúa
     * @param string $reason Razón del rechazo
     * @param string|null $recommendations Recomendaciones
     * @return void
     */
    public function rejectByViex(User $evaluator, string $reason, ?string $recommendations = null): void {
        if ($this->statusIsNot('En VIEX - En Evaluación')) {
            throw new \InvalidArgumentException('El trabajo debe estar "En VIEX - En Evaluación" para poder ser rechazado.');
        }

        $status = WorkStatus::where('name', 'Rechazado por VIEX')->firstOrFail();

        $finalComments = collect([
            "Razón del rechazo: {$reason}",
            $recommendations ? "Recomendaciones: {$recommendations}" : null
        ])->filter()->implode(' | ');

        // Marcar como borrador para que el profesor pueda editar
        $this->is_draft = '1';
        $this->save();

        $this->changeStatus($status, $evaluator, $finalComments);

        Log::info('Trabajo rechazado por VIEX', [
            'work_id' => $this->getKey(),
            'evaluator_id' => $evaluator->getKey(),
            'reason' => $reason,
        ]);
    }

    /**
     * CU15: Generar certificación oficial
     *
     * @param User $issuedBy Usuario que emite la certificación
     * @param string|null $certificationNumber Número de certificación (auto si null)
     * @param string|null $comments Comentarios adicionales
     * @param int $validityYears Años de vigencia del certificado
     * @return \App\Models\Certification
     */
    public function generateCertification(User $issuedBy, ?string $certificationNumber = null, ?string $comments = null, int $validityYears = 5): \App\Models\Certification
    {
        $currentStatusName = $this->currentStatus?->getAttribute('name');

        if (!in_array($currentStatusName, ['En VIEX - Aprobado', 'Certificado'], true)) {
            throw new \InvalidArgumentException('El trabajo debe estar en estado "En VIEX - Aprobado" para generar certificación oficial.');
        }

        $issueDate = now();
        $validUntil = $issueDate->copy()->addYears($validityYears);
        $number = $certificationNumber ?: \App\Models\Certification::generateCertificationNumber();

        $statusComment = __(
            'certifications.status_comment',
            [
                'number' => $number,
                'date' => $validUntil->format('d/m/Y'),
            ]
        );

        if ($currentStatusName !== 'Certificado') {
            $certifiedStatus = WorkStatus::where('name', 'Certificado')->firstOrFail();
            $this->changeStatus($certifiedStatus, $issuedBy, $statusComment);
            $this->refresh();
        } else {
            $this->statusHistory()->create([
                'work_of_extension_id' => $this->getKey(),
                'from_status_id' => $this->getAttribute('current_status_id'),
                'to_status_id' => $this->getAttribute('current_status_id'),
                'changed_by_user_id' => $issuedBy->getKey(),
                'comments' => $statusComment,
            ]);
        }

        $certification = \App\Models\Certification::create([
            'work_of_extension_id' => $this->getKey(),
            'certification_number' => $number,
            'issued_by_user_id' => $issuedBy->getKey(),
            'issue_date' => $issueDate,
            'valid_until' => $validUntil,
            'comments' => $comments,
        ]);

        try {
            GenerateCertificationPdf::dispatchSync($certification->getKey());
        } catch (\Throwable $exception) {
            Log::error('No se pudo generar el PDF de la certificación', [
                'certification_id' => $certification->getKey(),
                'work_id' => $this->getKey(),
                'error' => $exception->getMessage(),
            ]);
        }

        Log::info('Certificación generada', [
            'work_id' => $this->getKey(),
            'certification_number' => $number,
            'issued_by' => $issuedBy->getKey(),
            'validity_years' => $validityYears,
        ]);

        return $certification->fresh();
    }



    /**
     * Verificar si el trabajo ya tiene certificación oficial
     */
    public function hasCertification(): bool {
        return $this->certification()->exists();
    }

    /**
     * Obtener evaluadores asignados al trabajo
     */
    public function getAssignedEvaluators() {
        return $this->statusHistory()
            ->whereHas('status', function ($query) {
                $query->where('name', 'En Evaluación VIEX');
            })
            ->with('changedBy')
            ->get()
            ->pluck('changedBy')
            ->filter();
    }

    /**
     * Verificar si el trabajo puede ser evaluado por VIEX
     */
    public function canBeEvaluatedByViex(): bool {
        return in_array($this->currentStatus->getAttribute('name'), [
            'Enviado a VIEX',
            'En Evaluación VIEX'
        ]);
    }

    /**
     * Verificar si el trabajo está listo para certificación
     */
    public function isReadyForCertification(): bool {
        return $this->currentStatus->getAttribute('name') === 'En VIEX - Aprobado' && !$this->hasCertification();
    }

    /**
     * Obtener estadísticas del trabajo en VIEX
     */
    public function getViexStatistics(): array {
        $receivedAt = $this->statusHistory()
            ->whereHas('status', function ($query) {
                $query->where('name', 'Enviado a VIEX');
            })
            ->first();

        $evaluationStarted = $this->statusHistory()
            ->whereHas('status', function ($query) {
                $query->where('name', 'En Evaluación VIEX');
            })
            ->first();

        $completed = $this->statusHistory()
            ->whereHas('status', function ($query) {
                $query->whereIn('name', ['Certificado', 'Rechazado por VIEX']);
            })
            ->first();

        return [
            'received_at' => $receivedAt?->created_at,
            'evaluation_started_at' => $evaluationStarted?->created_at,
            'completed_at' => $completed?->created_at,
            'days_in_viex' => $receivedAt ? $receivedAt->created_at->diffInDays(now()) : null,
            'days_in_evaluation' => $evaluationStarted && !$completed ?
                $evaluationStarted->created_at->diffInDays(now()) : null,
            'is_overdue' => $evaluationStarted && !$completed ?
                $evaluationStarted->created_at->diffInDays(now()) > 30 : false, // 30 días límite
        ];
    }

    /**
     * Verificar si el trabajo está en algún estado de evaluación VIEX
     * Usado en las vistas VIEX para mostrar paneles de evaluación
     */
    public function isInViexEvaluationState(): bool {
        $currentStatusName = $this->currentStatus?->getAttribute('name');

        return in_array($currentStatusName, [
            'Enviado a VIEX',
            'En VIEX - Pendiente Asignación',
            'En VIEX - En Evaluación',
            'En VIEX - Aprobado'
        ]);
    }

    /**
     * Verificar si se puede generar reporte del trabajo
     * Usado para mostrar botón de reporte en vistas
     */
    public function canGenerateReport(): bool {
        $currentStatusName = $this->currentStatus?->getAttribute('name');

        return in_array($currentStatusName, [
            'En VIEX - Aprobado',
            'Certificado',
            'Rechazado por VIEX'
        ]);
    }
}
