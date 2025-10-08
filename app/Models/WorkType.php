<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para los tipos de trabajos de extensión
 * Tabla: work_type
 */
class WorkType extends Model {
    protected $table = 'work_type';

    protected $fillable = [
        'name',
        'description',
    ];

    // Relaciones

    /**
     * Trabajos de extensión de este tipo
     */
    public function works() {
        return $this->hasMany(WorkOfExtension::class);
    }

    /**
     * Obtener tipos de trabajo activos para formularios
     */
    public static function getActiveTypes() {
        return self::orderBy('name')->get();
    }
}
