<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo para los tipos de trabajos de extensión.
 * Tabla: work_type
 */
class WorkType extends Model
{
    protected $table = 'work_type';

    public const ACTIVE = '1';
    public const INACTIVE = '0';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Trabajos de extensión asociados al tipo.
     */
    public function works(): HasMany
    {
        return $this->hasMany(WorkOfExtension::class);
    }

    /**
     * Obtener únicamente los tipos activos.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', self::ACTIVE);
    }

    /**
     * Determinar si el tipo tiene trabajos asociados.
     */
    public function hasAssociatedWorks(): bool
    {
        if (property_exists($this, 'works_count')) {
            return (int) $this->works_count > 0;
        }

        return $this->works()->exists();
    }

    /**
     * Obtener tipos de trabajo activos ordenados por nombre.
     */
    public static function getActiveTypes(): Collection
    {
        return self::query()
            // ->active()
            ->orderBy('name')
            ->get();
    }
}
