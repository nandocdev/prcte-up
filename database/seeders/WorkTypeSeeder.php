<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkTypeSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $workTypes = [
            [
                'name' => 'Proyecto de Extensión',
                'description' => 'Proyectos institucionales, de unidades académicas y de servicio social orientados a la vinculación con la comunidad.',
                'is_active' => true,
            ],
            [
                'name' => 'Actividad de Extensión',
                'description' => 'Actividades de educación continua e intervenciones puntuales no retribuidas a título personal.',
                'is_active' => true,
            ],
            [
                'name' => 'Publicación',
                'description' => 'Artículos, libros, audiolibros que generen conocimiento para la comunidad universitaria y sociedad.',
                'is_active' => true,
            ],
            [
                'name' => 'Asistencia Técnica',
                'description' => 'Asesorías y consultorías especializadas a otras unidades de la UP, al Estado o a organizaciones privadas.',
                'is_active' => true,
            ]
        ];

        // Usar la tabla correcta según la migración
        foreach ($workTypes as $workType) {
            DB::table('work_type')->insertOrIgnore($workType);
        }

        echo "✓ Tipos de trabajo de extensión creados exitosamente\n";
        echo "📋 Total: " . count($workTypes) . " tipos de trabajo\n\n";
    }
}
