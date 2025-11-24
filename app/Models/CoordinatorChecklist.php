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
        $criterias = \App\Models\WorkTypeChecklistCriteria::getCriteriaForWorkType($work->work_type_id);

        $checklistData = [];
        foreach ($criterias as $criteria) {
            $checklistData[$criteria['criteria_key']] = false;
        }

        return $checklistData;
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
     * Obtener criterios con nombres legibles para la vista
     */
    public function getCriteriaWithNames(): array
    {
        $criterias = \App\Models\WorkTypeChecklistCriteria::getCriteriaForWorkType($this->work->work_type_id);

        $result = [];
        foreach ($criterias as $criteria) {
            $result[$criteria['criteria_key']] = [
                'name' => $criteria['name'],
                'description' => $criteria['description'],
                'category' => $criteria['category'],
                'is_required' => $criteria['is_required'],
                'checked' => $this->checklist_data[$criteria['criteria_key']] ?? false,
            ];
        }

        return $result;
    }

    /**
     * Verificar si todos los criterios requeridos están completos
     */
    public function isComplete(): bool
    {
        $criterias = \App\Models\WorkTypeChecklistCriteria::where('work_type_id', $this->work->work_type_id)
            ->active()
            ->required()
            ->pluck('criteria_key')
            ->toArray();

        foreach ($criterias as $criteriaKey) {
            if (!($this->checklist_data[$criteriaKey] ?? false)) {
                return false;
            }
        }

        return true;
    }
}
