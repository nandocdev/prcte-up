<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para los detalles de actividades de extensión
 * Tabla: activity_details
 */
class ActivityDetail extends Model {
    protected $fillable = [
        'work_of_extension_id',
        'details_json',
        // Nuevos campos específicos
        'activity_type',
        'modality',
        'duration_hours',
        'expected_participants',
        'participant_profile',
        'offers_certificate',
    ];

    protected $casts = [
        'details_json' => 'json',
        'offers_certificate' => 'boolean',
    ];

    // Relaciones

    /**
     * Trabajo de extensión al que pertenecen estos detalles
     */
    public function work() {
        return $this->belongsTo(WorkOfExtension::class, 'work_of_extension_id');
    }
}
