<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para los participantes de trabajos de extensión
 * Tabla: work_participants
 */
class WorkParticipant extends Model {
    protected $fillable = [
        'work_of_extension_id',
        'user_id',
        'external_participant_name',
        'role',
        'name',
        'email',
        'phone',
        'institution',
        'is_internal',
        'is_primary',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'is_primary' => 'boolean',
    ];

    // Relaciones

    /**
     * Trabajo al que pertenece este participante
     */
    public function work() {
        return $this->belongsTo(WorkOfExtension::class, 'work_of_extension_id');
    }

    /**
     * Usuario del sistema (si es participante interno)
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Accessors

    /**
     * Obtener el nombre del participante (interno o externo)
     */
    public function getParticipantNameAttribute() {
        if ($this->user) {
            return $this->user->name;
        }

        if (!empty($this->getAttribute('name'))) {
            return $this->getAttribute('name');
        }

        return $this->getAttribute('external_participant_name');
    }

    // Scopes

    /**
     * Solo participantes internos (usuarios del sistema)
     */
    public function scopeInternal($query) {
        return $query->whereNotNull('user_id');
    }

    /**
     * Solo participantes externos
     */
    public function scopeExternal($query) {
        return $query->whereNull('user_id');
    }

    /**
     * Participantes con un rol específico
     */
    public function scopeWithRole($query, $role) {
        return $query->where('role', $role);
    }
}
