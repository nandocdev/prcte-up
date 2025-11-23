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
                'description' => 'Trabajo en edición por el profesor.',
                'is_active' => true,
            ],
            [
                'name' => 'Enviado a Coordinador',
                'description' => 'Trabajo enviado por el profesor para revisión del coordinador.',
                'is_active' => true,
            ],
            [
                'name' => 'En Coordinador de Extensión',
                'description' => 'Nombre alterno utilizado históricamente para la bandeja del coordinador.',
                'is_active' => false,
            ],
            [
                'name' => 'En Coordinador Extensión',
                'description' => 'Nombre alterno utilizado históricamente para la bandeja del coordinador.',
                'is_active' => false,
            ],
            [
                'name' => 'En Revisión Coordinador',
                'description' => 'Coordinador de extensión revisando el trabajo.',
                'is_active' => true,
            ],
            [
                'name' => 'En Corrección',
                'description' => 'Trabajo observado por el coordinador y pendiente de correcciones.',
                'is_active' => true,
            ],
            [
                'name' => 'Devuelto para Corrección',
                'description' => 'Trabajo devuelto al profesor para subsanar observaciones.',
                'is_active' => true,
            ],
            [
                'name' => 'Rechazado por Coordinador',
                'description' => 'Trabajo rechazado por el coordinador de extensión.',
                'is_active' => true,
            ],
            [
                'name' => 'Pendiente Decano',
                'description' => 'Trabajo a la espera de decisión del Decano/Director.',
                'is_active' => false, // Desactivado según diagrama de flujo correcto
            ],
            [
                'name' => 'Enviado a Decano/Director',
                'description' => 'Trabajo remitido al Decano/Director para revisión.',
                'is_active' => false, // Desactivado según diagrama de flujo correcto
            ],
            [
                'name' => 'En Revisión Decano/Director',
                'description' => 'Decano/Director revisando el trabajo.',
                'is_active' => false, // Desactivado según diagrama de flujo correcto
            ],
            [
                'name' => 'Rechazado por Decano/Director',
                'description' => 'Trabajo rechazado por el Decano/Director.',
                'is_active' => false, // Desactivado según diagrama de flujo correcto
            ],
            [
                'name' => 'Aprobado por Coordinador',
                'description' => 'Trabajo aprobado por el coordinador y remitido a la siguiente etapa.',
                'is_active' => true,
            ],
            [
                'name' => 'Enviado a VIEX',
                'description' => 'Trabajo remitido al equipo VIEX para evaluación.',
                'is_active' => true,
            ],
            [
                'name' => 'Pendiente VIEX',
                'description' => 'Trabajo recibido por VIEX y pendiente de evaluación.',
                'is_active' => true,
            ],
            [
                'name' => 'En VIEX - Pendiente Asignación',
                'description' => 'VIEX debe asignar evaluadores al trabajo.',
                'is_active' => true,
            ],
            [
                'name' => 'En VIEX - En Evaluación',
                'description' => 'Evaluadores de VIEX realizando dictamen.',
                'is_active' => true,
            ],
            [
                'name' => 'En Evaluación VIEX',
                'description' => 'Nombre alterno utilizado para la evaluación en VIEX.',
                'is_active' => false,
            ],
            [
                'name' => 'En VIEX - Aprobado',
                'description' => 'Trabajo aprobado por VIEX, pendiente de certificación.',
                'is_active' => true,
            ],
            [
                'name' => 'Aprobado Internamente',
                'description' => 'Trabajo aprobado internamente por VIEX.',
                'is_active' => true,
            ],
            [
                'name' => 'Certificado',
                'description' => 'Trabajo certificado oficialmente.',
                'is_active' => true,
            ],
            [
                'name' => 'Rechazado por VIEX',
                'description' => 'Trabajo rechazado por el equipo VIEX.',
                'is_active' => true,
            ],
            [
                'name' => 'Rechazado',
                'description' => 'Trabajo rechazado definitivamente.',
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
        echo "🔄 Flujo: Borrador → Coordinador → VIEX → Certificado\n\n";
    }
}
