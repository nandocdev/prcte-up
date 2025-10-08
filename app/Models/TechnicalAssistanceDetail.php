<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para los detalles de asistencias técnicas
 * Tabla: technical_assistance_details
 */
class TechnicalAssistanceDetail extends Model {
    protected $fillable = [
        'work_of_extension_id',
        'assistance_type',
        'collaborating_institution',
        'details_json',
        // Nuevos campos específicos
        'specialization_area',
        'expected_products',
        'work_modality',
        'estimated_hours',
    ];

    protected $casts = [
        'details_json' => 'json',
    ];

    // Relaciones

    /**
     * Trabajo de extensión al que pertenecen estos detalles
     */
    public function work() {
        return $this->belongsTo(WorkOfExtension::class, 'work_of_extension_id');
    }

    // Scopes

    /**
     * Asistencias por tipo
     */
    public function scopeByType($query, $type) {
        return $query->where('assistance_type', $type);
    }

    /**
     * Solo asesorías
     */
    public function scopeAdvisories($query) {
        return $query->where('assistance_type', self::TYPE_ADVISORY);
    }

    /**
     * Solo consultorías
     */
    public function scopeConsultancies($query) {
        return $query->where('assistance_type', self::TYPE_CONSULTANCY);
    }

    // Constantes
    public const TYPE_ADVISORY = 'Asesoría';
    public const TYPE_CONSULTANCY = 'Consultoría';
}
