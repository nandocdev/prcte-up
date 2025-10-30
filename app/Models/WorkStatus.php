<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany as EloquentHasMany;

/**
 * Modelo para los estados del flujo de trabajo.
 * Tabla: work_statuses
 */
class WorkStatus extends Model
{
    protected $fillable = [
        'name',
        'description',
        'color',
        'sort_order',
        'is_active',
        'is_final',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_final' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Trabajos que están actualmente en este estado.
     */
    public function currentWorks(): EloquentHasMany
    {
        return $this->hasMany(WorkOfExtension::class, 'current_status_id');
    }

    /**
     * Cambios de estado desde este estado.
     */
    public function transitionsFrom(): EloquentHasMany
    {
        return $this->hasMany(WorkStatusHistory::class, 'from_status_id');
    }

    /**
     * Cambios de estado hacia este estado.
     */
    public function transitionsTo(): EloquentHasMany
    {
        return $this->hasMany(WorkStatusHistory::class, 'to_status_id');
    }

    /**
     * Filtrar únicamente los estados activos.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Determinar si el estado tiene trabajos en curso o historial asociado.
     */
    public function hasAssociations(): bool
    {
        if (
            property_exists($this, 'current_works_count')
            && property_exists($this, 'transitions_from_count')
            && property_exists($this, 'transitions_to_count')
        ) {
            return ($this->current_works_count > 0)
                || ($this->transitions_from_count > 0)
                || ($this->transitions_to_count > 0);
        }

        return $this->currentWorks()->exists()
            || $this->transitionsFrom()->exists()
            || $this->transitionsTo()->exists();
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
