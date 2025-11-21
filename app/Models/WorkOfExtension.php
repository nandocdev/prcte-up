<?php

namespace App\Models;

use App\Jobs\GenerateCertificationPdf;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    use HasFactory, InteractsWithMedia;

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
        return $this->belongsTo(WorkType::class, 'work_type_id');
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
     * @deprecated Usar UpdateWorkService en su lugar
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

    /**
     * Obtener comentarios y retroalimentación del trabajo
     * Filtra solo entradas del historial que tienen comentarios
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCommentsAndFeedback(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->statusHistory()
            ->with(['status', 'changedBy'])
            ->whereNotNull('comments')
            ->where('comments', '!=', '')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener último comentario de rechazo
     * Útil para mostrar en alertas
     *
     * @return WorkStatusHistory|null
     */
    public function getLastRejectionComment(): ?WorkStatusHistory
    {
        return $this->statusHistory()
            ->with(['status', 'changedBy'])
            ->whereNotNull('comments')
            ->where('comments', '!=', '')
            ->whereHas('status', function ($query) {
                $query->whereIn('name', [
                    'Rechazado por Coordinador',
                    'Rechazado por Decano/Director',
                    'Rechazado por VIEX',
                    'Devuelto para Corrección'
                ]);
            })
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
