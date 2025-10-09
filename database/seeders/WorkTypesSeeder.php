<?php

namespace Database\Seeders;

use App\Models\WorkType;
use App\Models\OrganizationalUnit;
use Illuminate\Database\Seeder;

class WorkTypesSeeder extends Seeder {
    /**
     * Run the database seeder.
     */
    public function run(): void {
        // Crear tipos de trabajo
        WorkType::updateOrCreate(['id' => 1], [
            'name' => 'Proyecto de Extensión',
            'description' => 'Proyectos de extensión universitaria con objetivos específicos',
            'is_active' => WorkType::ACTIVE,
        ]);

        WorkType::updateOrCreate(['id' => 2], [
            'name' => 'Actividad de Extensión',
            'description' => 'Actividades formativas como cursos, talleres, seminarios',
            'is_active' => WorkType::ACTIVE,
        ]);

        WorkType::updateOrCreate(['id' => 3], [
            'name' => 'Publicación',
            'description' => 'Publicaciones de divulgación, manuales, guías',
            'is_active' => WorkType::ACTIVE,
        ]);

        WorkType::updateOrCreate(['id' => 4], [
            'name' => 'Asistencia Técnica Especializada',
            'description' => 'Consultorías, asesorías y servicios técnicos especializados',
            'is_active' => WorkType::ACTIVE,
        ]);

        // Crear unidades organizacionales
        OrganizationalUnit::updateOrCreate(['id' => 1], [
            'name' => 'Facultad de Informática, Electrónica y Comunicación',
            'type' => 'facultad',
        ]);

        OrganizationalUnit::updateOrCreate(['id' => 2], [
            'name' => 'Facultad de Medicina',
            'type' => 'facultad',
        ]);

        OrganizationalUnit::updateOrCreate(['id' => 3], [
            'name' => 'Facultad de Ingeniería',
            'type' => 'facultad',
        ]);

        OrganizationalUnit::updateOrCreate(['id' => 4], [
            'name' => 'Facultad de Ciencias Económicas',
            'type' => 'facultad',
        ]);

        OrganizationalUnit::updateOrCreate(['id' => 5], [
            'name' => 'Facultad de Humanidades',
            'type' => 'facultad',
        ]);
    }
}
