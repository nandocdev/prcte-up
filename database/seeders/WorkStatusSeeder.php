<?php

namespace Database\Seeders;

use App\Models\WorkStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('work_statuses')->truncate();

        $now = now();

        $statuses = [
            [
                'name' => 'Borrador',
                'description' => 'Trabajo en edición por el profesor',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'En Revisión Coordinador',
                'description' => 'Pendiente de revisión por el Coordinador de Extensión',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'En Corrección',
                'description' => 'Devuelto al profesor para correcciones (estado transitorio)',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Pendiente Decano',
                'description' => 'Pendiente de aprobación por Decano/Director',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Pendiente VIEX',
                'description' => 'Pendiente de evaluación por VIEX',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Aprobado Internamente',
                'description' => 'Aprobado por VIEX, listo para certificación',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Certificado',
                'description' => 'Trabajo certificado oficialmente',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Rechazado',
                'description' => 'Trabajo rechazado definitivamente',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        WorkStatus::insert($statuses);

        echo "✓ Estados de trabajo cargados via WorkStatusSeeder\n";
        echo "📊 Total: " . count($statuses) . " estados disponibles\n";
        echo "🔄 Flujo: Borrador → Coordinador → Decano → VIEX → Certificado\n\n";
    }
}
