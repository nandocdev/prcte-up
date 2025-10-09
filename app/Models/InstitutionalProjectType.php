<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany as EloquentHasMany;

/**
 * Modelo para los tipos de proyectos institucionales.
 * Tabla: inst_project_types
 */
class InstitutionalProjectType extends Model
{
    protected $table = 'inst_project_types';
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Proyectos que utilizan este tipo institucional.
     */
    public function projectDetails(): EloquentHasMany
    {
        return $this->hasMany(ProjectDetail::class);
    }

    /**
     * Filtrar tipos institucionales activos.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Determinar si existen proyectos asociados al tipo.
     */
    public function hasAssociatedProjects(): bool
    {
        if (property_exists($this, 'project_details_count')) {
            return (int) $this->project_details_count > 0;
        }

        return $this->projectDetails()->exists();
    }
}
