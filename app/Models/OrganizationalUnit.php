<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para las unidades organizacionales
 * Representa la jerarquía de la universidad (Facultades, Departamentos, etc.)
 * Tabla: organizational_units
 */
class OrganizationalUnit extends Model {
    public const TYPE_LABELS = [
        'Main Campus' => 'Campus central',
        'Faculty' => 'Facultad',
        'Department' => 'Departamento',
        'School' => 'Escuela',
        'Regional Center' => 'Centro regional',
        'University Extension' => 'Extension universitaria',
        'Extension Unit' => 'Unidad de extension',
        'Directorate' => 'Direccion',
    ];

    protected $fillable = [
        'name',
        'type',
        'parent_id',
    ];

    // Relaciones

    /**
     * Unidad padre en la jerarquía
     */
    public function parent() {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Unidades hijas en la jerarquía
     */
    public function children() {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Usuarios que pertenecen a esta unidad
     */
    public function users() {
        return $this->hasMany(User::class, 'main_organizational_unit_id');
    }

    /**
     * Trabajos de extensión presentados por esta unidad
     */
    public function works() {
        return $this->hasMany(WorkOfExtension::class);
    }

    // Scopes

    /**
     * Obtener los IDs de esta unidad y todas sus unidades descendientes.
     * Se usa para filtrar trabajos accesibles por jerarquías (coordinadores, decanos).
     */
    public static function descendantIds(int $unitId): array
    {
        $ids = [$unitId];

        $children = self::where('parent_id', $unitId)->pluck('id');

        foreach ($children as $childId) {
            $ids = array_merge($ids, self::descendantIds((int) $childId));
        }

        return array_values(array_unique($ids));
    }

    /**
     * Obtener solo las unidades de tipo específico
     */
    public function scopeOfType($query, $type) {
        return $query->where('type', $type);
    }

    /**
     * Obtener solo las unidades raíz (sin padre)
     */
    public function scopeRoots($query) {
        return $query->whereNull('parent_id');
    }

    /**
     * Obtener unidades organizacionales para selectores en formularios
     */
    public static function getUnitsForSelection(): Collection
    {
        return self::orderBy('type')
            ->orderBy('name')
            ->get()
            ->groupBy('type');
    }

    public function typeLabel(): string
    {
        return self::TYPE_LABELS[$this->type] ?? $this->type;
    }

    public function isDescendantOf(int $ancestorId): bool
    {
        $parent = $this->parent;

        while ($parent instanceof self) {
            if ($parent->getKey() === $ancestorId) {
                return true;
            }

            $parent = $parent->parent;
        }

        return false;
    }

    public static function typeOptions(): array
    {
        return self::TYPE_LABELS;
    }
}
