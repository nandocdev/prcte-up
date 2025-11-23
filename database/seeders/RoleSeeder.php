<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        // Crear roles del sistema VIEX
        $roles = [
            'super_admin' => 'Super Administrador del Sistema',
            'viex_admin' => 'Administrador VIEX',
            'coordinador_extension' => 'Coordinador de Extensión',
            'profesor' => 'Profesor'
        ];

        foreach ($roles as $name => $description) {
            Role::firstOrCreate(['name' => $name]);
        }

        $this->command->info('✅ Roles del sistema VIEX creados exitosamente.');
    }
}
