<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para los detalles de publicaciones
 * Tabla: publication_details
 */
class PublicationDetail extends Model {
    protected $fillable = [
        'work_of_extension_id',
        'relevance_justification',
        'publication_date',
        'media_type',
        'media_nature',
        // Nuevos campos específicos
        'publication_type',
        'editorial',
        'isbn_issn',
        'target_audience',
        'language',
        'print_run',
    ];

    protected $casts = [
        'publication_date' => 'date',
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
     * Publicaciones por tipo de medio
     */
    public function scopeByMediaType($query, $mediaType) {
        return $query->where('media_type', $mediaType);
    }

    /**
     * Publicaciones por naturaleza del medio
     */
    public function scopeByMediaNature($query, $mediaNature) {
        return $query->where('media_nature', $mediaNature);
    }

    // Constantes
    public const MEDIA_TYPE_MAGAZINE = 'Revista';
    public const MEDIA_TYPE_PRESS = 'Prensa';
    public const MEDIA_TYPE_BOOK = 'Libro';

    public const MEDIA_NATURE_SPECIALIZED = 'Especializada';
    public const MEDIA_NATURE_INDEXED = 'Indexada';
}
