<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para los estados del flujo de trabajo
 * Tabla: work_statuses
 */
class WorkStatus extends Model {
    protected $fillable = [
        'name',
        'description',
    ];

    // Relaciones

    /**
     * Trabajos que están actualmente en este estado
     */
    public function currentWorks() {
        return $this->hasMany(WorkOfExtension::class, 'current_status_id');
    }

    /**
     * Cambios de estado desde este estado
     */
    public function transitionsFrom() {
        return $this->hasMany(WorkStatusHistory::class, 'from_status_id');
    }

    /**
     * Cambios de estado hacia este estado
     */
    public function transitionsTo() {
        return $this->hasMany(WorkStatusHistory::class, 'to_status_id');
    }

    // Constantes para estados comunes
    public const DRAFT = 'borrador';
    public const PENDING_COORDINATOR = 'pendiente_coordinador';
    public const PENDING_DEAN = 'pendiente_decano';
    public const PENDING_VIEX = 'pendiente_viex';
    public const APPROVED = 'aprobado';
    public const REJECTED = 'rechazado';
    public const CERTIFIED = 'certificado';
}
