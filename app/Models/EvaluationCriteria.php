<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo: EvaluationCriteria
 * 
 * Representa un criterio de evaluación utilizado en VIEX para evaluar trabajos de extensión.
 * Cada criterio tiene una puntuación máxima, un peso relativo y puede estar activo o inactivo.
 * 
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string|null $category
 * @property int $max_score
 * @property int $weight
 * @property int $order_visualization
 * @property bool $is_active
 * @property bool $is_required
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|EvaluationDetail[] $evaluationDetails
 */
class EvaluationCriteria extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'evaluation_criteria';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'category',
        'max_score',
        'weight',
        'order_visualization',
        'order', // Alias para order_visualization
        'is_active',
        'is_required',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'max_score' => 'integer',
        'weight' => 'integer',
        'order_visualization' => 'integer',
        'order' => 'integer', // Alias para order_visualization
        'is_active' => 'boolean',
        'is_required' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Scope para obtener solo criterios activos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para obtener criterios ordenados
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_visualization')->orderBy('name');
    }

    /**
     * Scope para obtener solo criterios requeridos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Relación: Detalles de evaluación que usan este criterio
     *
     * @return HasMany
     */
    public function evaluationDetails(): HasMany
    {
        return $this->hasMany(EvaluationDetail::class);
    }

    /**
     * Get the order attribute (alias for order_visualization)
     *
     * @return int
     */
    public function getOrderAttribute(): int
    {
        return $this->order_visualization;
    }

    /**
     * Set the order attribute (alias for order_visualization)
     *
     * @param int $value
     * @return void
     */
    public function setOrderAttribute(int $value): void
    {
        $this->order_visualization = $value;
    }

    /**
     * Determinar si el criterio posee evaluaciones asociadas.
     */
    public function hasEvaluationDetails(): bool
    {
        if (property_exists($this, 'evaluation_details_count')) {
            return $this->evaluation_details_count > 0;
        }

        return $this->evaluationDetails()->exists();
    }

    /**
     * Obtener la suma de pesos de los criterios activos.
     */
    public static function totalActiveWeight(): int
    {
        return (int) static::query()->where('is_active', true)->sum('weight');
    }

    /**
     * Calcular el peso porcentual de este criterio respecto al total
     *
     * @return float
     */
    public function getWeightPercentage(): float
    {
        $totalWeight = static::active()->sum('weight');
        
        if ($totalWeight == 0) {
            return 0;
        }
        
        return ($this->weight / $totalWeight) * 100;
    }

    /**
     * Verificar si el criterio está activo
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Verificar si el criterio es requerido
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->is_required === true;
    }

    /**
     * Normalizar una puntuación a escala de 0-100
     *
     * @param int $score
     * @return float
     */
    public function normalizeScore(int $score): float
    {
        if ($this->max_score == 0) {
            return 0;
        }
        
        return ($score / $this->max_score) * 100;
    }
}
