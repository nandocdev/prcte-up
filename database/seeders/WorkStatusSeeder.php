<?php

namespace Database\Seeders;

use App\Models\WorkStatus;
use Illuminate\Database\Seeder;

class WorkStatusSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $statuses = [
            [
                'name' => 'Borrador',
                'description' => 'Trabajo en proceso de elaboración por el docente.',
            ],
            [
                'name' => 'Enviado a Coordinador',
                'description' => 'Trabajo enviado al Coordinador de Extensión para revisión.',
            ],
            [
                'name' => 'En Revisión Coordinador',
                'description' => 'Trabajo bajo revisión del Coordinador de Extensión.',
            ],
            [
                'name' => 'Aprobado por Coordinador',
                'description' => 'Trabajo aprobado por el Coordinador, enviado a Decano/Director.',
            ],
            [
                'name' => 'Enviado a Decano/Director',
                'description' => 'Trabajo enviado al Decano o Director para evaluación institucional.',
            ],
            [
                'name' => 'En Revisión Decano/Director',
                'description' => 'Trabajo bajo evaluación del Decano o Director de la Unidad Académica.',
            ],
            [
                'name' => 'Aprobado por Decano/Director',
                'description' => 'Trabajo aprobado por Decano/Director, enviado a VIEX.',
            ],
            [
                'name' => 'Enviado a VIEX',
                'description' => 'Trabajo enviado a la Vicerrectoría de Extensión para evaluación final.',
            ],
            [
                'name' => 'En VIEX - Pendiente Asignación',
                'description' => 'Trabajo recibido en VIEX, pendiente de asignación a evaluador.',
            ],
            [
                'name' => 'En VIEX - En Evaluación',
                'description' => 'Trabajo asignado a evaluador especializado para dictamen.',
            ],
            [
                'name' => 'En VIEX - Aprobado',
                'description' => 'Trabajo aprobado por VIEX, listo para certificación.',
            ],
            [
                'name' => 'Certificado',
                'description' => 'Trabajo certificado oficialmente por VIEX.',
            ],
            [
                'name' => 'Rechazado por Coordinador',
                'description' => 'Trabajo rechazado por el Coordinador con observaciones.',
            ],
            [
                'name' => 'Rechazado por Decano/Director',
                'description' => 'Trabajo rechazado por el Decano o Director con observaciones.',
            ],
            [
                'name' => 'Rechazado por VIEX',
                'description' => 'Trabajo rechazado por VIEX con observaciones para corrección.',
            ],
            [
                'name' => 'Devuelto para Corrección',
                'description' => 'Trabajo devuelto al docente para realizar correcciones solicitadas.',
            ]
        ];

        foreach ($statuses as $status) {
            WorkStatus::updateOrCreate(
                ['name' => $status['name']],
                $status
            );
        }

        echo "✓ Estados de trabajo creados exitosamente
";
        echo "📊 Total: " . count($statuses) . " estados disponibles
";
        echo "🔄 Flujo: Borrador → Coordinador → Decano → VIEX → Certificado

";
    }
}
