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
                'is_active' => true,
            ],
            [
                'name' => 'Enviado a Coordinador',
                'description' => 'Trabajo enviado al Coordinador de Extensión para revisión.',
                'is_active' => true,
            ],
            [
                'name' => 'En Revisión Coordinador',
                'description' => 'Trabajo bajo revisión del Coordinador de Extensión.',
                'is_active' => true,
            ],
            [
                'name' => 'Aprobado por Coordinador',
                'description' => 'Trabajo aprobado por el Coordinador, enviado a Decano/Director.',
                'is_active' => true,
            ],
            [
                'name' => 'Enviado a Decano/Director',
                'description' => 'Trabajo enviado al Decano o Director para evaluación institucional.',
                'is_active' => true,
            ],
            [
                'name' => 'En Revisión Decano/Director',
                'description' => 'Trabajo bajo evaluación del Decano o Director de la Unidad Académica.',
                'is_active' => true,
            ],
            [
                'name' => 'Aprobado por Decano/Director',
                'description' => 'Trabajo aprobado por Decano/Director, enviado a VIEX.',
                'is_active' => true,
            ],
            [
                'name' => 'Enviado a VIEX',
                'description' => 'Trabajo enviado a la Vicerrectoría de Extensión para evaluación final.',
                'is_active' => true,
            ],
            [
                'name' => 'En VIEX - Pendiente Asignación',
                'description' => 'Trabajo recibido en VIEX, pendiente de asignación a evaluador.',
                'is_active' => true,
            ],
            [
                'name' => 'En VIEX - En Evaluación',
                'description' => 'Trabajo asignado a evaluador especializado para dictamen.',
                'is_active' => true,
            ],
            [
                'name' => 'En VIEX - Aprobado',
                'description' => 'Trabajo aprobado por VIEX, listo para certificación.',
                'is_active' => true,
            ],
            [
                'name' => 'Certificado',
                'description' => 'Trabajo certificado oficialmente por VIEX.',
                'is_active' => true,
            ],
            [
                'name' => 'Rechazado por Coordinador',
                'description' => 'Trabajo rechazado por el Coordinador con observaciones.',
                'is_active' => true,
            ],
            [
                'name' => 'Rechazado por Decano/Director',
                'description' => 'Trabajo rechazado por el Decano o Director con observaciones.',
                'is_active' => true,
            ],
            [
                'name' => 'Rechazado por VIEX',
                'description' => 'Trabajo rechazado por VIEX con observaciones para corrección.',
                'is_active' => true,
            ],
            [
                'name' => 'Devuelto para Corrección',
                'description' => 'Trabajo devuelto al docente para realizar correcciones solicitadas.',
                'is_active' => true,
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
