<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Modelo para las certificaciones emitidas
 * Tabla: certifications
 */
class Certification extends Model {
    protected $fillable = [
        'work_of_extension_id',
        'certification_number',
        'issue_date',
        'valid_until',
        'issued_by_user_id',
        'comments',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'valid_until' => 'date',
    ];

    // Relaciones

    /**
     * Trabajo de extensión certificado
     */
    public function work() {
        return $this->belongsTo(WorkOfExtension::class, 'work_of_extension_id');
    }

    /**
     * Usuario de VIEX que emitió la certificación
     */
    public function issuedByUser() {
        return $this->belongsTo(User::class, 'issued_by_user_id');
    }

    // Métodos

    /**
     * Generar número único de certificación
     */
    public static function generateCertificationNumber(): string {
        $year = date('Y');
        $count = self::whereYear('issue_date', $year)->count() + 1;
        return sprintf('VIEX-%s-%04d', $year, $count);
    }

    /**
     * Verificar si la certificación está vigente
     */
    public function isValid(): bool {
        return $this->valid_until >= now()->toDateString();
    }

    /**
     * URL para verificar la certificación
     */
    public function getVerificationUrlAttribute(): string {
        return route('certifications.verify', $this->getAttribute('certification_number'));
    }

    // Boot method para generar número automáticamente
    protected static function boot() {
        parent::boot();

        static::creating(function ($certification) {
            if (empty($certification->certification_number)) {
                $certification->certification_number = self::generateCertificationNumber();
            }
        });
    }
}
