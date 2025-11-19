<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder {
    public function run(): void {
        // Resetear cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            // Permisos de trabajos de extensión
            'works.create',
            'works.view.own',
            'works.edit.own',
            'works.delete.own',
            'works.submit',

            // Permisos de coordinación
            'works.coordinate',
            'works.review.unit',
            'works.approve.unit',
            'works.request-changes.unit',

            // Permisos de decano/director
            'works.manage.dean',
            'works.approve.dean',
            'works.request-changes.dean',

            // Permisos VIEX
            'works.manage.viex',
            'works.assign-evaluator',
            'works.evaluate',
            'works.certify',
            'works.generate-report',

            // Permisos de administración
            'users.manage',
            'users.create',
            'users.view.all',
            'users.edit.all',
            'users.delete',
            'roles.manage',
            'permissions.manage',
            'system.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles y asignar permisos

        // 1. Super Admin - Todos los permisos
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. Profesor - Permisos básicos para crear y gestionar sus trabajos
        $profesor = Role::firstOrCreate(['name' => 'profesor']);
        $profesor->givePermissionTo([
            'works.create',
            'works.view.own',
            'works.edit.own',
            'works.delete.own',
            'works.submit',
        ]);

        // 3. Coordinador de Extensión - Puede revisar trabajos de su unidad
        $coordinador = Role::firstOrCreate(['name' => 'coordinador_extension']);
        $coordinador->givePermissionTo([
            'works.create',
            'works.view.own',
            'works.edit.own',
            'works.delete.own',
            'works.submit',
            'works.coordinate',
            'works.review.unit',
            'works.approve.unit',
            'works.request-changes.unit',
        ]);

        // 4. Decano/Director - Puede aprobar trabajos para enviar a VIEX
        $decano = Role::firstOrCreate(['name' => 'decano_director']);
        $decano->givePermissionTo([
            'works.create',
            'works.view.own',
            'works.edit.own',
            'works.delete.own',
            'works.submit',
            'works.manage.dean',
            'works.approve.dean',
            'works.request-changes.dean',
        ]);

        // 5. VIEX Admin - Puede certificar y gestionar trabajos finales
        $viexAdmin = Role::firstOrCreate(['name' => 'viex_admin']);
        $viexAdmin->givePermissionTo([
            'works.create',
            'works.view.own',
            'works.edit.own',
            'works.delete.own',
            'works.submit',
            'works.manage.viex',
            'works.assign-evaluator',
            'works.evaluate',
            'works.certify',
            'works.generate-report',
        ]);

        // 6. Evaluador - Puede evaluar trabajos asignados por VIEX
        $evaluador = Role::firstOrCreate(['name' => 'evaluador']);
        $evaluador->givePermissionTo([
            'works.evaluate',
        ]);

        $this->command->info('Roles y permisos creados exitosamente.');
    }
}
