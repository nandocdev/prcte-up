<?php

namespace App\Services\WorkOfExtension;

use App\Models\WorkOfExtension;
use App\Models\User;
use App\Models\WorkStatus;
use App\Models\Certification;
use App\Jobs\GenerateCertificationPdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para manejar la certificación de trabajos de extensión
 * Genera certificaciones oficiales y maneja el estado de certificación
 */
class CertifyWorkService
{
    /**
     * CU15: Generar certificación oficial
     */
    public function generateCertification(
        WorkOfExtension $work,
        User $issuedBy,
        ?string $certificationNumber = null,
        ?string $comments = null,
        int $validityYears = 5
    ): Certification {
        DB::beginTransaction();

        try {
            $currentStatusName = $work->currentStatus?->getAttribute('name');

            if (!in_array($currentStatusName, ['En VIEX - Aprobado', 'En VIEX - En Evaluación', 'Certificado'], true)) {
                throw new \InvalidArgumentException('El trabajo debe estar en estado "En VIEX - Aprobado" o "En VIEX - En Evaluación" para generar certificación oficial.');
            }

            $issueDate = now();
            $validUntil = $issueDate->copy()->addYears($validityYears);
            $number = $certificationNumber ?: Certification::generateCertificationNumber();

            $statusComment = __(
                'certifications.status_comment',
                [
                    'number' => $number,
                    'date' => $validUntil->format('d/m/Y'),
                ]
            );

            if ($currentStatusName !== 'Certificado') {
                $certifiedStatus = WorkStatus::where('name', 'Certificado')->firstOrFail();
                $work->changeStatus($certifiedStatus, $issuedBy, $statusComment);
                $work->refresh();
            } else {
                $work->statusHistory()->create([
                    'work_of_extension_id' => $work->getKey(),
                    'from_status_id' => $work->getAttribute('current_status_id'),
                    'to_status_id' => $work->getAttribute('current_status_id'),
                    'changed_by_user_id' => $issuedBy->getKey(),
                    'comments' => $statusComment,
                ]);
            }

            $certification = Certification::create([
                'work_of_extension_id' => $work->getKey(),
                'certification_number' => $number,
                'issued_by_user_id' => $issuedBy->getKey(),
                'issue_date' => $issueDate,
                'valid_until' => $validUntil,
                'comments' => $comments,
            ]);

            try {
                GenerateCertificationPdf::dispatchSync($certification->getKey());
            } catch (\Throwable $exception) {
                Log::error('No se pudo generar el PDF de la certificación', [
                    'certification_id' => $certification->getKey(),
                    'work_id' => $work->getKey(),
                    'error' => $exception->getMessage(),
                ]);
            }

            Log::info('Certificación generada', [
                'work_id' => $work->getKey(),
                'certification_number' => $number,
                'issued_by' => $issuedBy->getKey(),
                'validity_years' => $validityYears,
            ]);

            DB::commit();

            return $certification->fresh();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al generar certificación', [
                'work_id' => $work->getKey(),
                'issued_by' => $issuedBy->getKey(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Verificar si el trabajo ya tiene certificación oficial
     */
    public function hasCertification(WorkOfExtension $work): bool
    {
        return $work->certification()->exists();
    }

    /**
     * Verificar si el trabajo está listo para certificación
     */
    public function isReadyForCertification(WorkOfExtension $work): bool
    {
        return $work->currentStatus->getAttribute('name') === 'En VIEX - Aprobado' && !$this->hasCertification($work);
    }

    /**
     * Obtener estadísticas de certificación del trabajo
     */
    public function getCertificationStatistics(WorkOfExtension $work): array
    {
        $certification = $work->certification;

        if (!$certification) {
            return [
                'has_certification' => false,
                'certification_number' => null,
                'issue_date' => null,
                'valid_until' => null,
                'days_until_expiry' => null,
                'is_expired' => null,
                'issued_by' => null,
            ];
        }

        $now = now();
        $validUntil = $certification->valid_until;
        $daysUntilExpiry = $now->diffInDays($validUntil, false);
        $isExpired = $daysUntilExpiry < 0;

        return [
            'has_certification' => true,
            'certification_number' => $certification->certification_number,
            'issue_date' => $certification->issue_date,
            'valid_until' => $validUntil,
            'days_until_expiry' => max(0, $daysUntilExpiry),
            'is_expired' => $isExpired,
            'issued_by' => $certification->issuedBy,
            'comments' => $certification->comments,
        ];
    }
}