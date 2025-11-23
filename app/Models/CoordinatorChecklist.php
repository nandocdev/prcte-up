<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoordinatorChecklist extends Model
{
    protected $fillable = [
        'work_of_extension_id',
        'coordinator_id',
        'checklist_data',
        'reviewer_notes',
        'last_updated_at'
    ];

    protected $casts = [
        'checklist_data' => 'array',
        'last_updated_at' => 'datetime'
    ];

    /**
     * Relación con el trabajo de extensión
     */
    public function work(): BelongsTo
    {
        return $this->belongsTo(WorkOfExtension::class, 'work_of_extension_id');
    }

    /**
     * Relación con el coordinador
     */
    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    /**
     * Obtener o crear checklist para un trabajo y coordinador
     */
    public static function getOrCreateForWork(WorkOfExtension $work, User $coordinator): self
    {
        return static::firstOrCreate(
            [
                'work_of_extension_id' => $work->getKey(),
                'coordinator_id' => $coordinator->getKey()
            ],
            [
                'checklist_data' => self::getDefaultChecklistData($work),
                'reviewer_notes' => null,
                'last_updated_at' => now()
            ]
        );
    }

    /**
     * Generar checklist por defecto basado en el tipo de trabajo
     */
    public static function getDefaultChecklistData(WorkOfExtension $work): array
    {
        $baseChecklist = [
            // Campos globales (comunes a todos los tipos)
            'format_correct' => false,
            'objectives_clear' => false,
            'description_complete' => false,
            'evidence_attached' => false,
            'participants_complete' => false,
            'dates_coherent' => false,
            'regulations_compliant' => false,
        ];

        // Campos específicos por tipo de trabajo
        switch ($work->work_type_id) {
            case 1: // Proyecto
                $baseChecklist = array_merge($baseChecklist, [
                    'project_category_valid' => false,
                    'general_description_complete' => false,
                    'justification_adequate' => false,
                    'methodology_clear' => false,
                    'scope_defined' => false,
                    'resource_plan_complete' => false,
                    'schedule_realistic' => false,
                    'cost_plan_detailed' => false,
                    'beneficiaries_described' => false,
                    'communication_plan_present' => false,
                    'institution_relationships_clear' => false,
                    'final_comments_relevant' => false,
                    'ss_intervention_appropriate' => false,
                ]);
                break;

            case 2: // Actividad
                $baseChecklist = array_merge($baseChecklist, [
                    'activity_type_appropriate' => false,
                    'modality_suitable' => false,
                    'duration_reasonable' => false,
                    'introduction_contextualized' => false,
                    'justification_adequate' => false,
                    'objectives_specific' => false,
                    'methodology_detailed' => false,
                    'resources_available' => false,
                    'beneficiaries_profile_clear' => false,
                    'expected_participants_realistic' => false,
                    'institution_relationships_present' => false,
                    'comments_relevant' => false,
                    'certification_appropriate' => false,
                ]);
                break;

            case 3: // Publicación
                $baseChecklist = array_merge($baseChecklist, [
                    'publication_type_valid' => false,
                    'summary_comprehensive' => false,
                    'editorial_reputable' => false,
                    'isbn_issn_present' => false,
                    'target_audience_defined' => false,
                    'relevance_justified' => false,
                    'publication_date_valid' => false,
                    'media_type_appropriate' => false,
                    'media_nature_clear' => false,
                    'language_appropriate' => false,
                    'print_run_realistic' => false,
                ]);
                break;

            case 4: // Asistencia Técnica
                $baseChecklist = array_merge($baseChecklist, [
                    'assistance_type_valid' => false,
                    'collaborating_institution_clear' => false,
                    'specialization_area_relevant' => false,
                    'description_comprehensive' => false,
                    'objectives_clear' => false,
                    'methodology_detailed' => false,
                    'expected_products_defined' => false,
                    'evidence_sufficient' => false,
                    'work_modality_appropriate' => false,
                    'estimated_hours_realistic' => false,
                ]);
                break;
        }

        return $baseChecklist;
    }

    /**
     * Actualizar checklist y notas
     */
    public function updateChecklist(array $checklistData, ?string $reviewerNotes = null): bool
    {
        $this->checklist_data = $checklistData;
        $this->reviewer_notes = $reviewerNotes;
        $this->last_updated_at = now();

        return $this->save();
    }

    /**
     * Calcular progreso del checklist
     */
    public function getProgressPercentage(): float
    {
        if (empty($this->checklist_data)) {
            return 0.0;
        }

        $totalItems = count($this->checklist_data);
        $completedItems = count(array_filter($this->checklist_data, fn($value) => $value === true));

        return $totalItems > 0 ? round(($completedItems / $totalItems) * 100, 1) : 0.0;
    }

    /**
     * Obtener items completados
     */
    public function getCompletedItems(): array
    {
        return array_filter($this->checklist_data ?? [], fn($value) => $value === true);
    }

    /**
     * Obtener items pendientes
     */
    public function getPendingItems(): array
    {
        return array_filter($this->checklist_data ?? [], fn($value) => $value === false);
    }
}
