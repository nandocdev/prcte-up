<?php

namespace App\Jobs;

use App\Models\Certification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;

class GenerateCertificationPdf implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Identificador de la certificación a procesar.
     */
    private int $certificationId;

    public function __construct(int $certificationId)
    {
        $this->certificationId = $certificationId;
    }

    public function handle(): void
    {
        $certification = Certification::with([
            'work.workType',
            'work.organizationalUnit',
            'work.primaryResponsible',
            'issuedByUser',
        ])->find($this->certificationId);

        if (!$certification || !$certification->work) {
            Log::warning('No se encontró certificación válida para generar PDF.', [
                'certification_id' => $this->certificationId,
            ]);

            return;
        }

    $pdf = app('dompdf.wrapper');
        $pdf->loadView('certifications.pdf', [
            'certification' => $certification,
            'work' => $certification->work,
        ]);
        $pdf->setPaper('a4');

        $fileName = sprintf('certificacion-%s.pdf', $certification->getAttribute('certification_number'));

        try {
            $certification->clearMediaCollection('certificates');
            $certification
                ->addMediaFromString($pdf->output())
                ->usingFileName($fileName)
                ->withCustomProperties([
                    'generated_at' => now()->toIso8601String(),
                ])
                ->toMediaCollection('certificates');
        } catch (FileCannotBeAdded $exception) {
            Log::error('No se pudo adjuntar el PDF de certificación.', [
                'certification_id' => $certification->getKey(),
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
