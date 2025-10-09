<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WorkOfExtension;
use App\Models\User;
use App\Models\WorkType;
use App\Models\OrganizationalUnit;
use App\Models\WorkStatus;

class WorkOfExtensionSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $this->createSampleWorks();
    }

    /**
     * Crear trabajos de extensión de muestra
     */
    private function createSampleWorks(): void {
        // Obtener usuarios profesores para asignar trabajos
        $professors = User::whereHas('roles', function ($query) {
            $query->where('name', 'profesor');
        })->get();

        if ($professors->isEmpty()) {
            $this->command->warn('No hay profesores en la base de datos. Ejecute primero UserSeeder.');
            return;
        }

        // Obtener datos necesarios
        $workTypes = WorkType::all();
        $organizationalUnits = OrganizationalUnit::all();
        $draftStatus = WorkStatus::where('name', 'Borrador')->first();
    $submittedStatus = WorkStatus::where('name', 'En Revisión Coordinador')->first();

        // Debug
        $this->command->info("Estados encontrados: " . WorkStatus::count());
        $allStatuses = WorkStatus::pluck('name');
        $this->command->info("Estados: " . $allStatuses->join(', '));

        if (!$draftStatus || !$submittedStatus) {
            $this->command->warn('No se encontraron estados de trabajo específicos. Usando estados disponibles...');
            $draftStatus = WorkStatus::first();
            $submittedStatus = WorkStatus::skip(1)->first() ?? $draftStatus;
        }

        // Trabajos de muestra
        $sampleWorks = [
            [
                'title' => 'Programa de Alfabetización Digital para Adultos Mayores',
                'description' => 'Programa educativo dirigido a adultos mayores de 60 años para enseñarles el uso básico de computadoras, internet y aplicaciones móviles. El programa incluye clases teóricas y prácticas, material educativo adaptado y seguimiento personalizado.',
                'work_type' => 'Educación Continua',
                'academic_period' => '2024-I',
                'start_date' => '2024-09-01',
                'end_date' => '2024-12-15',
                'publication_consent' => '1',
                'status' => 'Borrador',
                'unit_type' => 'Faculty'
            ],
            [
                'title' => 'Asesoría Técnica en Sistemas de Información para PYMES',
                'description' => 'Programa de asesoría técnica especializada para pequeñas y medianas empresas del sector comercial y de servicios. Incluye análisis de necesidades, diseño de soluciones tecnológicas, implementación y capacitación del personal.',
                'work_type' => 'Asistencia Técnica',
                'academic_period' => '2024-I',
                'start_date' => '2024-08-15',
                'end_date' => '2025-02-28',
                'publication_consent' => '1',
                'status' => 'En Revisión Coordinador',
                'unit_type' => 'School'
            ],
            [
                'title' => 'Investigación sobre Impacto Ambiental de Residuos Plásticos',
                'description' => 'Proyecto de investigación aplicada para evaluar el impacto de los residuos plásticos en ecosistemas locales y proponer estrategias de mitigación. Incluye trabajo de campo, análisis de laboratorio y propuestas de políticas públicas.',
                'work_type' => 'Proyecto de Investigación',
                'academic_period' => '2024-I',
                'start_date' => '2024-07-01',
                'end_date' => '2025-06-30',
                'publication_consent' => '1',
                'status' => 'Borrador',
                'unit_type' => 'Regional Center'
            ],
            [
                'title' => 'Servicio Social Universitario en Comunidades Rurales',
                'description' => 'Programa integral de servicio social que involucra a estudiantes de últimos años en proyectos de desarrollo comunitario en áreas rurales. Incluye salud comunitaria, educación básica y desarrollo económico local.',
                'work_type' => 'Servicio Social',
                'academic_period' => '2024-I',
                'start_date' => '2024-09-15',
                'end_date' => '2025-01-31',
                'publication_consent' => '0',
                'status' => 'Borrador',
                'unit_type' => 'Faculty'
            ],
        ];

        foreach ($sampleWorks as $index => $workData) {
            // Seleccionar profesor responsable
            $professor = $professors->random();

            // Buscar tipo de trabajo
            $workType = $workTypes->where('name', $workData['work_type'])->first();
            if (!$workType) {
                $workType = $workTypes->first(); // Fallback
            }

            // Buscar unidad organizacional apropiada
            $orgUnit = $organizationalUnits->where('type', $workData['unit_type'])->first();
            if (!$orgUnit) {
                $orgUnit = $organizationalUnits->first(); // Fallback
            }

            // Determinar estado
            $status = $workData['status'] === 'Borrador' ? $draftStatus : $submittedStatus;
            $isDraft = $workData['status'] === 'Borrador' ? '1' : '0';
            $submittedAt = $workData['status'] !== 'Borrador' ? now()->subDays(rand(1, 30)) : null;

            WorkOfExtension::create([
                'title' => $workData['title'],
                'work_type_id' => $workType->getKey(),
                'primary_responsible_user_id' => $professor->getKey(),
                'organizational_unit_id' => $orgUnit->getKey(),
                'current_status_id' => $status->getKey(),
                'description' => $workData['description'],
                'start_date' => $workData['start_date'],
                'end_date' => $workData['end_date'],
                'academic_period' => $workData['academic_period'],
                'publication_consent' => $workData['publication_consent'],
                'is_draft' => $isDraft,
                'submitted_at' => $submittedAt,
            ]);

            $this->command->info("✅ Trabajo creado: {$workData['title']}");
        }

        $this->command->info("📋 Se crearon " . count($sampleWorks) . " trabajos de extensión de muestra");
    }
}
