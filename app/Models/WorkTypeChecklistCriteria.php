<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkTypeChecklistCriteria extends Model
{
    protected $fillable = [
        'work_type_id',
        'criteria_key',
        'name',
        'description',
        'category',
        'order',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Relación con tipo de trabajo
     */
    public function workType(): BelongsTo
    {
        return $this->belongsTo(WorkType::class);
    }

    /**
     * Scope para criterios activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para criterios ordenados
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    /**
     * Scope para criterios requeridos
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Obtener criterios por tipo de trabajo
     */
    public static function getCriteriaForWorkType(int $workTypeId): array
    {
        return static::where('work_type_id', $workTypeId)
            ->active()
            ->ordered()
            ->get()
            ->toArray();
    }
}
