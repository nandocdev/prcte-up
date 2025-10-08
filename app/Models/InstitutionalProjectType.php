<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para los tipos de proyectos institucionales
 * Tabla: institutional_project_types
 */
class InstitutionalProjectType extends Model {
    protected $fillable = [
        'name',
        'description',
    ];

    // Relaciones

    /**
     * Proyectos que utilizan este tipo institucional
     */
    public function projectDetails() {
        return $this->hasMany(ProjectDetail::class);
    }
}
