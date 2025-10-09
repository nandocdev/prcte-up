<?php

namespace Database\Seeders;

use App\Models\WorkStatus;
use Illuminate\Database\Seeder;

class WorkStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Borrador',
                'description' => 'Trabajo en edición por el profesor',
                'is_active' => true,
            ],
            [
                'name' => 'En Revisión Coordinador',
                'description' => 'Pendiente de revisión por el Coordinador de Extensión',
                'is_active' => true,
            ],
            [
                'name' => 'En Corrección',
                'description' => 'Devuelto al profesor para correcciones (estado transitorio)',
                'is_active' => true,
            ],
            [
                'name' => 'Pendiente Decano',
                'description' => 'Pendiente de aprobación por Decano/Director',
                'is_active' => true,
            ],
            [
                'name' => 'Pendiente VIEX',
                'description' => 'Pendiente de evaluación por VIEX',
                'is_active' => true,
            ],
            [
                'name' => 'Aprobado Internamente',
                'description' => 'Aprobado por VIEX, listo para certificación',
                'is_active' => true,
            ],
            [
                'name' => 'Certificado',
                'description' => 'Trabajo certificado oficialmente',
                'is_active' => true,
            ],
            [
                'name' => 'Rechazado',
                'description' => 'Trabajo rechazado definitivamente',
                'is_active' => true,
            ],
        ];
        $statusNames = [];

        foreach ($statuses as $status) {
            WorkStatus::updateOrCreate(
                ['name' => $status['name']],
                [
                    'description' => $status['description'],
                    'is_active' => $status['is_active'],
                ]
            );

            $statusNames[] = $status['name'];
        }

        // Marcar como inactivos los estados que ya no forman parte del catálogo base
        if (!empty($statusNames)) {
            WorkStatus::whereNotIn('name', $statusNames)->update(['is_active' => false]);
        }

        echo "✓ Estados de trabajo cargados via WorkStatusSeeder\n";
        echo "📊 Total: " . count($statuses) . " estados disponibles\n";
        echo "🔄 Flujo: Borrador → Coordinador → Decano → VIEX → Certificado\n\n";
    }
}
