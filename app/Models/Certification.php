<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Modelo para las certificaciones emitidas
 * Tabla: certifications
 */
class Certification extends Model implements HasMedia
{
    use InteractsWithMedia;
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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('certificates')->singleFile();
    }

    public function getCertificateMedia(): ?Media
    {
        return $this->getFirstMedia('certificates');
    }

    public function buildDownloadResponse(): BinaryFileResponse
    {
        $media = $this->getCertificateMedia();

        if (!$media) {
            throw new RuntimeException(__('certifications.download_missing_file'));
        }

        $absolutePath = $media->getPath();

        if (!$absolutePath || !is_file($absolutePath)) {
            Log::warning('Archivo de certificación no encontrado en disco.', [
                'certification_id' => $this->getKey(),
                'media_id' => $media->getKey(),
                'disk' => $media->disk,
                'path' => $media->getPathRelativeToRoot(),
            ]);

            throw new RuntimeException(__('certifications.download_missing_file'));
        }

        $fileName = sprintf('certificacion-%s.pdf', Str::slug($this->getAttribute('certification_number') ?? (string) $this->getKey(), '_'));

        return response()->download($absolutePath, $fileName);
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
