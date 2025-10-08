<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo: EvaluationDetail
 * 
 * Representa la puntuación individual de un criterio específico dentro de una evaluación.
 * Almacena el puntaje, comentarios y evidencias para cada criterio evaluado.
 * 
 * @property int $id
 * @property int $work_evaluation_id
 * @property int $evaluation_criteria_id
 * @property int|null $score
 * @property string|null $comments
 * @property string|null $evidence
 * @property float|null $normalized_score
 * @property float|null $weighted_score
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read WorkEvaluation $workEvaluation
 * @property-read EvaluationCriteria $criteria
 */
class EvaluationDetail extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'evaluation_details';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'work_evaluation_id',
        'evaluation_criteria_id',
        'score',
        'comments',
        'evidence',
        'normalized_score',
        'weighted_score',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'score' => 'integer',
        'normalized_score' => 'decimal:2',
        'weighted_score' => 'decimal:2',
    ];

    /**
     * Relación: Evaluación a la que pertenece este detalle
     *
     * @return BelongsTo
     */
    public function workEvaluation(): BelongsTo
    {
        return $this->belongsTo(WorkEvaluation::class);
    }

    /**
     * Relación: Criterio de evaluación asociado
     *
     * @return BelongsTo
     */
    public function criteria(): BelongsTo
    {
        return $this->belongsTo(EvaluationCriteria::class, 'evaluation_criteria_id');
    }

    /**
     * Boot method para calcular automáticamente los puntajes normalizados y ponderados
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::saving(function (EvaluationDetail $detail) {
            if ($detail->score !== null && $detail->criteria) {
                $detail->calculateScores();
            }
        });

        static::saved(function (EvaluationDetail $detail) {
            // Recalcular la puntuación total de la evaluación
            $detail->workEvaluation->calculateTotalScore();
            $detail->workEvaluation->calculateWeightedScore();
        });
    }

    /**
     * Calcular los puntajes normalizados y ponderados
     *
     * @return void
     */
    public function calculateScores(): void
    {
        if ($this->score === null || !$this->criteria) {
            $this->normalized_score = null;
            $this->weighted_score = null;
            return;
        }

        // Normalizar el puntaje a escala 0-100
        $this->normalized_score = $this->criteria->normalizeScore($this->score);

        // Calcular el puntaje ponderado
        $totalWeight = EvaluationCriteria::active()->sum('weight');
        if ($totalWeight > 0) {
            $this->weighted_score = ($this->normalized_score * $this->criteria->weight) / $totalWeight;
        } else {
            $this->weighted_score = 0;
        }
    }

    /**
     * Verificar si el detalle tiene puntaje
     *
     * @return bool
     */
    public function hasScore(): bool
    {
        return $this->score !== null;
    }

    /**
     * Verificar si el puntaje es máximo
     *
     * @return bool
     */
    public function isMaxScore(): bool
    {
        if (!$this->criteria || $this->score === null) {
            return false;
        }

        return $this->score === $this->criteria->max_score;
    }

    /**
     * Obtener el porcentaje del puntaje obtenido respecto al máximo
     *
     * @return float
     */
    public function getScorePercentage(): float
    {
        if (!$this->criteria || $this->score === null || $this->criteria->max_score === 0) {
            return 0;
        }

        return ($this->score / $this->criteria->max_score) * 100;
    }

    /**
     * Validar que el puntaje esté dentro del rango válido
     *
     * @return bool
     */
    public function isValidScore(): bool
    {
        if ($this->score === null || !$this->criteria) {
            return true; // Un puntaje nulo es válido
        }

        return $this->score >= 0 && $this->score <= $this->criteria->max_score;
    }
}
