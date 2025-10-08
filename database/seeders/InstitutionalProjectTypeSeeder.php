<?php

namespace Database\Seeders;

use App\Models\InstitutionalProjectType;
use Illuminate\Database\Seeder;

class InstitutionalProjectTypeSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $institutionalTypes = [
            [
                'name' => 'Fortalecimiento integral de las comunidades',
                'description' => 'Proyectos orientados al desarrollo y fortalecimiento de capacidades comunitarias.',
            ],
            [
                'name' => 'Estudio y diagnóstico de problemas',
                'description' => 'Investigación y análisis de problemáticas sociales para proponer soluciones.',
            ],
            [
                'name' => 'Atención a grupos sociales desfavorecidos',
                'description' => 'Intervenciones dirigidas a poblaciones vulnerables y en situación de riesgo.',
            ],
            [
                'name' => 'Vinculación y seguimiento de graduados',
                'description' => 'Programas de conexión y acompañamiento a egresados universitarios.',
            ],
            [
                'name' => 'Cultura ambiental para el desarrollo sostenible',
                'description' => 'Iniciativas de educación y concienciación ambiental para la sustentabilidad.',
            ]
        ];

        foreach ($institutionalTypes as $type) {
            InstitutionalProjectType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }

        echo "✓ Tipos de proyectos institucionales creados exitosamente\n";
        echo "🏛️ Total: " . count($institutionalTypes) . " tipos institucionales\n\n";
    }
}
