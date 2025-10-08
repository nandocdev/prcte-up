<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para el historial de cambios de estado
 * Provee auditoría completa de cambios
 * Tabla: work_status_history
 */
class WorkStatusHistory extends Model {
    protected $table = 'work_status_history';

    protected $fillable = [
        'work_of_extension_id',
        'from_status_id',
        'to_status_id',
        'changed_by_user_id',
        'comments',
    ];

    // Relaciones

    /**
     * Trabajo al que pertenece este cambio de estado
     */
    public function work() {
        return $this->belongsTo(WorkOfExtension::class, 'work_of_extension_id');
    }

    /**
     * Estado anterior
     */
    public function fromStatus() {
        return $this->belongsTo(WorkStatus::class, 'from_status_id');
    }

    /**
     * Estado nuevo
     */
    public function toStatus() {
        return $this->belongsTo(WorkStatus::class, 'to_status_id');
    }

    /**
     * Usuario que realizó el cambio
     */
    public function changedBy() {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }

    /**
     * Alias para toStatus - usado en timeline
     */
    public function status() {
        return $this->belongsTo(WorkStatus::class, 'to_status_id');
    }

    // Scopes

    /**
     * Cambios para un trabajo específico
     */
    public function scopeForWork($query, $workId) {
        return $query->where('work_of_extension_id', $workId);
    }

    /**
     * Cambios realizados por un usuario específico
     */
    public function scopeByUser($query, $userId) {
        return $query->where('changed_by_user_id', $userId);
    }
}
