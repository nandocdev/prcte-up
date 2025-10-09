<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo: WorkEvaluator
 * 
 * Representa la asignación de un evaluador a un trabajo de extensión.
 * Tabla pivote entre work_of_extensions y users (evaluadores).
 * 
 * @property int $id
 * @property int $work_of_extension_id
 * @property int $evaluator_user_id
 * @property int $assigned_by_user_id
 * @property string $role_evaluator
 * @property string|null $assignment_notes
 * @property \Carbon\Carbon $assigned_at
 * @property \Carbon\Carbon|null $notified_at
 * @property \Carbon\Carbon|null $accepted_at
 * @property \Carbon\Carbon|null $completed_at
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read WorkOfExtension $workOfExtension
 * @property-read User $evaluator
 * @property-read User $assignedBy
 */
class WorkEvaluator extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'work_evaluators';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'work_of_extension_id',
        'evaluator_user_id',
        'assigned_by_user_id',
        'role_evaluator',
        'assignment_notes',
        'assigned_at',
        'notified_at',
        'accepted_at',
        'completed_at',
        'status',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'assigned_at' => 'datetime',
        'notified_at' => 'datetime',
        'accepted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Roles válidos para evaluadores
     */
    const ROLE_LEAD = 'lead_evaluator';
    const ROLE_EVALUATOR = 'evaluator';

    /**
     * Estados válidos de asignación
     */
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DECLINED = 'declined';

    /**
     * Scope para obtener solo evaluadores principales
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLeadEvaluators($query)
    {
        return $query->where('role_evaluator', self::ROLE_LEAD);
    }

    /**
     * Scope para obtener evaluaciones pendientes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope para obtener evaluaciones aceptadas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    /**
     * Scope para obtener evaluaciones completadas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Relación: Trabajo de extensión al que se asigna el evaluador
     *
     * @return BelongsTo
     */
    public function workOfExtension(): BelongsTo
    {
        return $this->belongsTo(WorkOfExtension::class);
    }

    /**
     * Relación: Usuario evaluador
     *
     * @return BelongsTo
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_user_id');
    }

    /**
     * Relación: Usuario que asignó al evaluador
     *
     * @return BelongsTo
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }

    /**
     * Marcar que el evaluador fue notificado
     *
     * @return void
     */
    public function markAsNotified(): void
    {
        $this->update([
            'notified_at' => now(),
        ]);
    }

    /**
     * Marcar que el evaluador aceptó la asignación
     *
     * @return void
     */
    public function markAsAccepted(): void
    {
        $this->update([
            'accepted_at' => now(),
            'status' => self::STATUS_ACCEPTED,
        ]);
    }

    /**
     * Marcar que la evaluación está en progreso
     *
     * @return void
     */
    public function markAsInProgress(): void
    {
        $this->update([
            'status' => self::STATUS_IN_PROGRESS,
        ]);
    }

    /**
     * Marcar que el evaluador completó la evaluación
     *
     * @return void
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'completed_at' => now(),
            'status' => self::STATUS_COMPLETED,
        ]);
    }

    /**
     * Marcar que el evaluador rechazó la asignación
     *
     * @return void
     */
    public function markAsDeclined(): void
    {
        $this->update([
            'status' => self::STATUS_DECLINED,
        ]);
    }

    /**
     * Verificar si es evaluador principal
     *
     * @return bool
     */
    public function isLeadEvaluator(): bool
    {
        return $this->role_evaluator === self::ROLE_LEAD;
    }

    /**
     * Verificar si la evaluación está pendiente
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Verificar si la evaluación fue aceptada
     *
     * @return bool
     */
    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
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
     * Verificar si la evaluación está completada
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Verificar si la asignación fue rechazada
     *
     * @return bool
     */
    public function isDeclined(): bool
    {
        return $this->status === self::STATUS_DECLINED;
    }
}
