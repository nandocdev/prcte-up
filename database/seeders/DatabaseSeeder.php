<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     */
    public function run(): void {
        echo "\n🌱 Iniciando proceso de seeding para VIEX con Oracle...\n\n";

        // 1. Seeders de tablas base (sin dependencias de FK)
        echo "📋 Seeders de datos maestros básicos...\n";
        $this->call([
            WorkTypesSeeder::class,             // work_type (sin FK) - Usa Eloquent, más moderno
            WorkStatusSeeder::class,            // work_statuses (sin FK)
            InstitutionalProjectTypeSeeder::class, // institutional_project_types (sin FK)
            SdgGoalSeeder::class,
        ]);

        // 2. Seeders de estructura organizacional (auto-referencia)
        echo "🏢 Seeders de estructura organizacional...\n";
        $this->call([
            OrganizationalUnitSeeder::class,    // organizational_units (parent_id auto-ref)
        ]);

        // 3. Seeders de autenticación y permisos (Spatie)
        echo "🔐 Seeders de roles y permisos...\n";
        $this->call([
            RolesAndPermissionsSeeder::class,   // permissions + roles + role_has_permissions
        ]);

        // 4. Seeders de usuarios (dependen de organizational_units + roles)
        echo "👥 Seeders de usuarios...\n";
        $this->call([
            UserSeeder::class,                  // users (FK: main_organizational_unit_id + roles via pivot)
        ]);

        // 5. Seeders de datos transaccionales (dependen de users, work_types, etc.)
        echo "📄 Seeders de trabajos de extensión...\n";
        $this->call([
            WorkOfExtensionSeeder::class,       // work_of_extensions (FK: work_type_id, primary_responsible_user_id, organizational_unit_id, current_status_id)
        ]);

        echo "\n✅ Proceso de seeding completado exitosamente!\n";
        echo "🎯 Base de datos Oracle lista para VIEX - Universidad de Panamá\n\n";
        echo "📊 Resumen de datos creados:\n";
        echo "   ✓ Tipos de trabajo y estados del workflow\n";
        echo "   ✓ Estructura organizacional completa (45 unidades)\n";
        echo "   ✓ Roles y permisos del sistema\n";
        echo "   ✓ Usuarios de prueba (16 usuarios)\n";
        echo "   ✓ Trabajos de extensión de ejemplo\n\n";
        echo "🔐 Credenciales de acceso disponibles en UserSeeder\n\n";
    }
}
