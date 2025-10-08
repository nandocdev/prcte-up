<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para las unidades organizacionales
 * Representa la jerarquía de la universidad (Facultades, Departamentos, etc.)
 * Tabla: organizational_units
 */
class OrganizationalUnit extends Model {
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
        return $this->belongsTo(OrganizationalUnit::class, 'parent_id');
    }

    /**
     * Unidades hijas en la jerarquía
     */
    public function children() {
        return $this->hasMany(OrganizationalUnit::class, 'parent_id');
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
    public static function getUnitsForSelection() {
        return self::orderBy('type')
            ->orderBy('name')
            ->get()
            ->groupBy('type');
    }
}
