<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para los detalles de proyectos de extensión
 * Tabla: project_details
 */
class ProjectDetail extends Model {
    protected $fillable = [
        'work_of_extension_id',
        'project_category',
        'institutional_project_type_id',
        'details_json',
        'schedule_json',
        'resources_json',
        'costs_json',
        'ss_tutor_user_id',
        'ss_intervention_summary',
        // Nuevos campos específicos
        'objectives',
        'methodology',
        'direct_beneficiaries',
        'indirect_beneficiaries',
        'geographic_area',
    ];

    protected $casts = [
        'details_json' => 'json',
        'schedule_json' => 'json',
        'resources_json' => 'json',
        'costs_json' => 'json',
    ];

    // Relaciones

    /**
     * Trabajo de extensión al que pertenecen estos detalles
     */
    public function work() {
        return $this->belongsTo(WorkOfExtension::class, 'work_of_extension_id');
    }

    /**
     * Tipo de proyecto institucional (si aplica)
     */
    public function institutionalProjectType() {
        return $this->belongsTo(InstitutionalProjectType::class);
    }

    /**
     * Usuario tutor (para proyectos de servicio social)
     */
    public function tutor() {
        return $this->belongsTo(User::class, 'ss_tutor_user_id');
    }

    // Scopes

    /**
     * Proyectos por categoría
     */
    public function scopeByCategory($query, $category) {
        return $query->where('project_category', $category);
    }

    /**
     * Solo proyectos institucionales
     */
    public function scopeInstitutional($query) {
        return $query->where('project_category', 'Institucional');
    }

    /**
     * Solo proyectos de unidad académica
     */
    public function scopeAcademicUnit($query) {
        return $query->where('project_category', 'Unidad Académica');
    }

    /**
     * Solo proyectos de servicio social
     */
    public function scopeSocialService($query) {
        return $query->where('project_category', 'Servicio Social');
    }

    // Constantes
    public const CATEGORY_INSTITUTIONAL = 'Institucional';
    public const CATEGORY_ACADEMIC_UNIT = 'Unidad Académica';
    public const CATEGORY_SOCIAL_SERVICE = 'Servicio Social';
}
