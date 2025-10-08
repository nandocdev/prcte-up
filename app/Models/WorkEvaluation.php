<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo: WorkEvaluation
 * 
 * Representa la evaluación completa realizada por un evaluador a un trabajo de extensión.
 * Contiene la valoración general, comentarios, decisión final y puntuaciones.
 * 
 * @property int $id
 * @property int $work_of_extension_id
 * @property int $evaluator_user_id
 * @property int|null $work_evaluator_id
 * @property string|null $general_comments
 * @property string|null $strengths
 * @property string|null $weaknesses
 * @property string|null $recommendations
 * @property float|null $total_score
 * @property float|null $weighted_score
 * @property string $final_decision
 * @property string|null $decision_justification
 * @property \Carbon\Carbon|null $started_at
 * @property \Carbon\Carbon|null $submitted_at
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * 
 * @property-read WorkOfExtension $workOfExtension
 * @property-read User $evaluator
 * @property-read WorkEvaluator|null $workEvaluator
 * @property-read \Illuminate\Database\Eloquent\Collection|EvaluationDetail[] $evaluationDetails
 */
class WorkEvaluation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'work_evaluations';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'work_of_extension_id',
        'evaluator_user_id',
        'work_evaluator_id',
        'general_comments',
        'strengths',
        'weaknesses',
        'recommendations',
        'total_score',
        'weighted_score',
        'final_decision',
        'decision_justification',
        'started_at',
        'submitted_at',
        'status',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_score' => 'decimal:2',
        'weighted_score' => 'decimal:2',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Decisiones finales válidas
     */
    const DECISION_APPROVE = 'approve';
    const DECISION_APPROVE_WITH_CONDITIONS = 'approve_with_conditions';
    const DECISION_REJECT = 'reject';
    const DECISION_PENDING = 'pending';

    /**
     * Estados válidos de evaluación
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_REVIEWED = 'reviewed';

    /**
     * Scope para obtener evaluaciones enviadas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSubmitted($query)
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    /**
     * Scope para obtener evaluaciones pendientes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('final_decision', self::DECISION_PENDING);
    }

    /**
     * Scope para obtener evaluaciones aprobadas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->whereIn('final_decision', [
            self::DECISION_APPROVE,
            self::DECISION_APPROVE_WITH_CONDITIONS
        ]);
    }

    /**
     * Scope para obtener evaluaciones rechazadas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRejected($query)
    {
        return $query->where('final_decision', self::DECISION_REJECT);
    }

    /**
     * Relación: Trabajo de extensión evaluado
     *
     * @return BelongsTo
     */
    public function workOfExtension(): BelongsTo
    {
        return $this->belongsTo(WorkOfExtension::class);
    }

    /**
     * Relación: Evaluador
     *
     * @return BelongsTo
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_user_id');
    }

    /**
     * Relación: Asignación del evaluador (si existe)
     *
     * @return BelongsTo
     */
    public function workEvaluator(): BelongsTo
    {
        return $this->belongsTo(WorkEvaluator::class);
    }

    /**
     * Relación: Detalles de evaluación (puntuaciones por criterio)
     *
     * @return HasMany
     */
    public function evaluationDetails(): HasMany
    {
        return $this->hasMany(EvaluationDetail::class);
    }

    /**
     * Iniciar la evaluación
     *
     * @return void
     */
    public function start(): void
    {
        $this->update([
            'started_at' => now(),
            'status' => self::STATUS_IN_PROGRESS,
        ]);
    }

    /**
     * Enviar la evaluación para revisión
     *
     * @return void
     */
    public function submit(): void
    {
        $this->update([
            'submitted_at' => now(),
            'status' => self::STATUS_SUBMITTED,
        ]);

        // Actualizar el estado del evaluador a completado
        if ($this->workEvaluator) {
            $this->workEvaluator->markAsCompleted();
        }
    }

    /**
     * Marcar la evaluación como revisada
     *
     * @return void
     */
    public function markAsReviewed(): void
    {
        $this->update([
            'status' => self::STATUS_REVIEWED,
        ]);
    }

    /**
     * Calcular la puntuación total de la evaluación
     *
     * @return float
     */
    public function calculateTotalScore(): float
    {
        $totalScore = $this->evaluationDetails()
            ->join('evaluation_criteria', 'evaluation_details.evaluation_criteria_id', '=', 'evaluation_criteria.id')
            ->where('evaluation_criteria.is_active', true)
            ->sum('evaluation_details.score');

        $this->update(['total_score' => $totalScore]);

        return $totalScore;
    }

    /**
     * Calcular la puntuación ponderada de la evaluación
     *
     * @return float
     */
    public function calculateWeightedScore(): float
    {
        $weightedScore = $this->evaluationDetails()
            ->join('evaluation_criteria', 'evaluation_details.evaluation_criteria_id', '=', 'evaluation_criteria.id')
            ->where('evaluation_criteria.is_active', true)
            ->sum('evaluation_details.weighted_score');

        $this->update(['weighted_score' => $weightedScore]);

        return $weightedScore;
    }

    /**
     * Verificar si la evaluación está completa (todos los criterios requeridos tienen puntaje)
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        $requiredCriteria = EvaluationCriteria::active()->required()->count();
        $completedCriteria = $this->evaluationDetails()
            ->whereHas('criteria', function ($query) {
                $query->active()->required();
            })
            ->whereNotNull('score')
            ->count();

        return $requiredCriteria === $completedCriteria;
    }

    /**
     * Verificar si la evaluación está enviada
     *
     * @return bool
     */
    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    /**
     * Verificar si la evaluación está en progreso
     *
     * @return bool
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Verificar si es una decisión de aprobación
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return in_array($this->final_decision, [
            self::DECISION_APPROVE,
            self::DECISION_APPROVE_WITH_CONDITIONS
        ]);
    }

    /**
     * Verificar si es una decisión de rechazo
     *
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->final_decision === self::DECISION_REJECT;
    }

    /**
     * Verificar si la decisión está pendiente
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->final_decision === self::DECISION_PENDING;
    }
}
