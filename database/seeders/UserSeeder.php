<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        // Limpiar tabla de usuarios existentes si es necesario
        // User::truncate();

        $users = [
            // === SUPER ADMINISTRADOR ===
            [
                'name' => 'Super Administrador VIEX',
                'email' => 'admin@up.ac.pa',
                'password' => Hash::make('admin123'),
                'cedula' => '8-888-8888',
                'professor_code' => null,
                'main_organizational_unit_id' => 100, // Campus Central
                'is_active' => true,
                'role' => 'super_admin'
            ],

            // === ADMINISTRADOR VIEX ===
            [
                'name' => 'Dr. María Elena Vásquez',
                'email' => 'maria.vasquez@up.ac.pa',
                'password' => Hash::make('viex2025'),
                'cedula' => '8-111-1111',
                'professor_code' => 'VX001',
                'main_organizational_unit_id' => 100, // Campus Central
                'is_active' => true,
                'role' => 'viex_admin'
            ],

            // === COORDINADOR DE EXTENSIÓN ===
            [
                'name' => 'Prof. Luis Fernando García',
                'email' => 'luis.garcia@up.ac.pa',
                'password' => Hash::make('coord2025'),
                'cedula' => '8-666-6666',
                'professor_code' => 'FI101',
                'main_organizational_unit_id' => 201, // Facultad de Ingeniería
                'is_active' => true,
                'role' => 'coordinador_extension'
            ],

            // === PROFESOR ===
            [
                'name' => 'Prof. Alejandra Morales',
                'email' => 'alejandra.morales@up.ac.pa',
                'password' => Hash::make('prof2025'),
                'cedula' => '8-101-1010',
                'professor_code' => 'FI201',
                'main_organizational_unit_id' => 201, // Dept. Ingeniería Civil
                'is_active' => true,
                'role' => 'profesor'
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']); // Remover role del array antes de crear usuario

            // Crear usuario
            $user = User::create($userData);

            // Asignar rol específico si se proporcionó, si no, usar lógica por defecto
            if ($role) {
                $user->assignRole($role);
            } else {
                $user->assignDefaultRole();
            }

            echo "✓ Usuario creado: {$user->name} ({$user->email}) - Rol: " .
                ($user->roles->first()?->name ?? 'Sin rol') . "\n";
        }

        echo "\n🎉 Seeder de usuarios completado exitosamente!\n";
        echo "📊 Total de usuarios creados: " . count($users) . "\n\n";

        echo "🔐 Credenciales de acceso:\n";
        echo "Super Admin: admin@up.ac.pa / admin123\n";
        echo "VIEX Admin: maria.vasquez@up.ac.pa / viex2025\n";
        echo "Coord. Ext. Ingeniería: luis.garcia@up.ac.pa / coord2025\n";
        echo "Profesor: alejandra.morales@up.ac.pa / prof2025\n";
    }
}