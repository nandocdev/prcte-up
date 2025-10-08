<?php

namespace Database\Seeders;

use App\Models\OrganizationalUnit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationalUnitSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        // Desactiva temporalmente la revisión de claves foráneas para evitar problemas de orden
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DB::table('organizational_units')->truncate(); // Limpia la tabla antes de sembrar

        $units = [
            // --- CENTROS REGIONALES Y EXTENSIONES UNIVERSITARIAS (Nivel Superior) ---
            ['id' => 1, 'name' => 'Centro Regional Universitario de Azuero', 'type' => 'Regional Center', 'parent_id' => null],
            ['id' => 2, 'name' => 'Centro Regional Universitario de Bocas del Toro', 'type' => 'Regional Center', 'parent_id' => null],
            ['id' => 3, 'name' => 'Centro Regional Universitario de Coclé', 'type' => 'Regional Center', 'parent_id' => null],
            ['id' => 4, 'name' => 'Centro Regional Universitario de Colón', 'type' => 'Regional Center', 'parent_id' => null],
            ['id' => 5, 'name' => 'Centro Regional Universitario de Chepo', 'type' => 'Regional Center', 'parent_id' => null],
            ['id' => 6, 'name' => 'Centro Regional Universitario de Darién', 'type' => 'Regional Center', 'parent_id' => null],
            ['id' => 7, 'name' => 'Centro Regional Universitario de Los Santos', 'type' => 'Regional Center', 'parent_id' => null],
            ['id' => 8, 'name' => 'Centro Regional Universitario de Panamá Oeste', 'type' => 'Regional Center', 'parent_id' => null],
            ['id' => 9, 'name' => 'Centro Regional Universitario de San Miguelito', 'type' => 'Regional Center', 'parent_id' => null],
            ['id' => 10, 'name' => 'Centro Regional Universitario de Veraguas', 'type' => 'Regional Center', 'parent_id' => null],
            ['id' => 11, 'name' => 'Extensión Universitaria de Aguadulce', 'type' => 'University Extension', 'parent_id' => 3], // Hija de Coclé
            ['id' => 12, 'name' => 'Extensión Universitaria de Soná', 'type' => 'University Extension', 'parent_id' => 10], // Hija de Veraguas
            ['id' => 13, 'name' => 'Extensión Universitaria de Tortí', 'type' => 'University Extension', 'parent_id' => 5], // Hija de Chepo

            // --- CAMPUS CENTRAL OCTAVIO MENDEZ PEREIRA (Nivel Superior) ---
            ['id' => 100, 'name' => 'Campus Central Octavio Méndez Pereira', 'type' => 'Main Campus', 'parent_id' => null],

            // --- FACULTADES (Hijas del Campus Central) ---
            ['id' => 101, 'name' => 'Facultad de Administración de Empresas y Contabilidad', 'type' => 'Faculty', 'parent_id' => 100],
            ['id' => 102, 'name' => 'Facultad de Administración Pública', 'type' => 'Faculty', 'parent_id' => 100],
            ['id' => 103, 'name' => 'Facultad de Arquitectura y Diseño', 'type' => 'Faculty', 'parent_id' => 100],
            ['id' => 104, 'name' => 'Facultad de Bellas Artes', 'type' => 'Faculty', 'parent_id' => 100],
            ['id' => 105, 'name' => 'Facultad de Ciencias Agropecuarias', 'type' => 'Faculty', 'parent_id' => 100],
            ['id' => 106, 'name' => 'Facultad de Ciencias de la Educación', 'type' => 'Faculty', 'parent_id' => 100],
            ['id' => 107, 'name' => 'Facultad de Ciencias Naturales, Exactas y Tecnología', 'type' => 'Faculty', 'parent_id' => 100],
            ['id' => 108, 'name' => 'Facultad de Comunicación Social', 'type' => 'Faculty', 'parent_id' => 100],
            ['id' => 109, 'name' => 'Facultad de Derecho y Ciencias Políticas', 'type' => 'Faculty', 'parent_id' => 100],
            ['id' => 110, 'name' => 'Facultad de Economía', 'type' => 'Faculty', 'parent_id' => 100],
            ['id' => 111, 'name' => 'Facultad de Enfermería', 'type' => 'Faculty', 'parent_id' => 100],
            ['id' => 112, 'name' => 'Facultad de Farmacia', 'type' => 'Faculty', 'parent_id' => 100],
            ['id' => 113, 'name' => 'Facultad de Humanidades', 'type' => 'Faculty', 'parent_id' => 100],
            ['id' => 114, 'name' => 'Facultad de Informática, Electrónica y Comunicación', 'type' => 'Faculty', 'parent_id' => 100],
            ['id' => 115, 'name' => 'Facultad de Ingeniería', 'type' => 'Faculty', 'parent_id' => 100],
            ['id' => 116, 'name' => 'Facultad de Medicina', 'type' => 'Faculty', 'parent_id' => 100],
            ['id' => 117, 'name' => 'Facultad de Medicina Veterinaria', 'type' => 'Faculty', 'parent_id' => 100],
            ['id' => 118, 'name' => 'Facultad de Odontología', 'type' => 'Faculty', 'parent_id' => 100],
            ['id' => 119, 'name' => 'Facultad de Psicología', 'type' => 'Faculty', 'parent_id' => 100],

            // --- DEPARTAMENTOS (Hijos de Facultades) - Ejemplo con la Facultad de Ingeniería ---
            ['id' => 201, 'name' => 'Departamento de Ingeniería Civil', 'type' => 'Department', 'parent_id' => 115],
            ['id' => 202, 'name' => 'Departamento de Ingeniería Eléctrica', 'type' => 'Department', 'parent_id' => 115],
            ['id' => 203, 'name' => 'Departamento de Ingeniería Industrial', 'type' => 'Department', 'parent_id' => 115],
            ['id' => 204, 'name' => 'Departamento de Ingeniería Mecánica', 'type' => 'Department', 'parent_id' => 115],
            ['id' => 205, 'name' => 'Departamento de Ingeniería Geomática', 'type' => 'Department', 'parent_id' => 115],

            // --- DEPARTAMENTOS (Hijos de Facultades) - Ejemplo con la Facultad de Humanidades ---
            ['id' => 301, 'name' => 'Departamento de Español', 'type' => 'Department', 'parent_id' => 113],
            ['id' => 302, 'name' => 'Departamento de Filosofía', 'type' => 'Department', 'parent_id' => 113],
            ['id' => 303, 'name' => 'Departamento de Geografía', 'type' => 'Department', 'parent_id' => 113],
            ['id' => 304, 'name' => 'Departamento de Historia', 'type' => 'Department', 'parent_id' => 113],
            ['id' => 305, 'name' => 'Departamento de Inglés', 'type' => 'Department', 'parent_id' => 113],

            // --- UNIDADES DE EXTENSION (Podrían depender de Facultades o Centros Regionales) ---
            ['id' => 401, 'name' => 'Unidad de Extensión - Facultad de Ingeniería', 'type' => 'Extension Unit', 'parent_id' => 115],
            ['id' => 402, 'name' => 'Unidad de Extensión - CRU de Azuero', 'type' => 'Extension Unit', 'parent_id' => 1],
        ];

        // Usar updateOrCreate para evitar duplicados con Oracle
        foreach ($units as $unit) {
            OrganizationalUnit::updateOrCreate(
                ['id' => $unit['id']],
                $unit
            );
        }

        echo "✓ Unidades organizacionales creadas exitosamente\n";
        echo "🏛️ Total: " . count($units) . " unidades organizacionales\n\n";
    }
}
