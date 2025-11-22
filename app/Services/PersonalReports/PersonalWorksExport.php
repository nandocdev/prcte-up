<?php

declare(strict_types=1);

namespace App\Services\PersonalReports;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PersonalWorksExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    private Collection $works;
    private User $user;

    public function __construct(Collection $works, User $user)
    {
        $this->works = $works;
        $this->user = $user;
    }

    public function collection(): Collection
    {
        return $this->works->map(function (WorkOfExtension $work) {
            return [
                'ID' => $work->getKey(),
                'Título' => $work->title,
                'Tipo de Trabajo' => $work->workType->name ?? 'N/A',
                'Unidad Académica' => $work->organizationalUnit->name ?? 'N/A',
                'Fecha Inicio' => $work->start_date?->format('d/m/Y'),
                'Fecha Fin' => $work->end_date?->format('d/m/Y'),
                'Fecha Certificación' => $work->certification?->created_at?->format('d/m/Y'),
                'Número de Certificación' => $work->certification?->certification_number,
                'Estado' => $work->currentStatus->name ?? 'N/A',
                'Consentimiento Publicación' => $work->publication_consent ? 'Sí' : 'No',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Título',
            'Tipo de Trabajo',
            'Unidad Académica',
            'Fecha Inicio',
            'Fecha Fin',
            'Fecha Certificación',
            'Número de Certificación',
            'Estado',
            'Consentimiento Publicación',
        ];
    }

    public function title(): string
    {
        return 'Trabajos Certificados - ' . $this->user->name;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]], // Primera fila en negrita
        ];
    }
}