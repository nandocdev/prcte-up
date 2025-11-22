<?php

declare(strict_types=1);

namespace App\Services\PersonalReports;

use App\Models\User;
use App\Models\WorkOfExtension;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PersonalWorksReportService
{
    /**
     * Generar reporte PDF de trabajos certificados del profesor
     */
    public function generatePdfReport(User $user): \Barryvdh\DomPDF\PDF
    {
        $certifiedWorks = $this->getCertifiedWorks($user);

        $data = [
            'user' => $user,
            'works' => $certifiedWorks,
            'generated_at' => now(),
            'total_works' => $certifiedWorks->count(),
        ];

        return Pdf::loadView('reports.personal-works-pdf', $data);
    }

    /**
     * Generar reporte Excel de trabajos certificados del profesor
     */
    public function generateExcelReport(User $user): StreamedResponse
    {
        $certifiedWorks = $this->getCertifiedWorks($user);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new PersonalWorksExport($certifiedWorks, $user),
            sprintf('trabajos_certificados_%s_%s.xlsx', $user->name, now()->format('Y-m-d'))
        );
    }

    /**
     * Obtener trabajos certificados del profesor
     */
    private function getCertifiedWorks(User $user): Collection
    {
        return WorkOfExtension::where('primary_responsible_user_id', $user->getKey())
            ->whereHas('currentStatus', function ($query) {
                $query->where('name', 'Certificado');
            })
            ->with([
                'workType',
                'organizationalUnit',
                'certification',
                'projectDetail',
                'activityDetail',
                'publicationDetail',
                'technicalAssistanceDetail'
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}