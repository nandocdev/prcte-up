<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\InstitutionalProjectType;
use App\Models\OrganizationalUnit;
use App\Models\User;
use App\Models\WorkOfExtension;
use App\Models\WorkStatus;
use App\Models\WorkType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ManageCatalogsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_super_admin_can_create_work_type(): void
    {
        $role = Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create([
            'cedula' => '8-999-1000',
            'is_active' => true,
        ]);
        $user->assignRole($role);

        $response = $this->actingAs($user)->post(route('admin.work-types.store'), [
            'name' => 'Programa piloto',
            'description' => 'Pruebas de integracion con nuevas dependencias.',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.work-types.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('work_type', [
            'name' => 'Programa piloto',
            'is_active' => '1',
        ]);
    }

    public function test_viex_admin_can_update_work_status(): void
    {
        $role = Role::create([
            'name' => 'viex_admin',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create([
            'cedula' => '8-999-1001',
            'is_active' => true,
        ]);
        $user->assignRole($role);

        $status = WorkStatus::create([
            'name' => 'En evaluacion interna',
            'description' => 'Etapa de analisis por la comision.',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->put(route('admin.work-statuses.update', $status), [
            'name' => 'En evaluacion interna',
            'description' => 'Evaluacion por parte de la comision tecnica.',
            'is_active' => false,
        ]);

        $response->assertRedirect(route('admin.work-statuses.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('work_statuses', [
            'id' => $status->id,
            'description' => 'Evaluacion por parte de la comision tecnica.',
            'is_active' => '0',
        ]);
    }

    public function test_cannot_delete_work_type_with_associated_work(): void
    {
        $role = Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create([
            'cedula' => '8-999-1002',
            'is_active' => true,
        ]);
        $user->assignRole($role);

        $workType = WorkType::create([
            'name' => 'Proyecto especial',
            'description' => 'Catalogo temporal para pilotos.',
            'is_active' => true,
        ]);

        $status = WorkStatus::create([
            'name' => 'En ejecucion',
            'description' => 'Trabajo aprobado en proceso de ejecucion.',
            'is_active' => true,
        ]);

        $organizationalUnit = OrganizationalUnit::create([
            'name' => 'Facultad de Pruebas',
            'type' => 'Faculty',
        ]);

        $responsible = User::factory()->create([
            'cedula' => '8-999-1003',
            'is_active' => true,
        ]);

        WorkOfExtension::create([
            'title' => 'Diagnostico comunitario',
            'work_type_id' => $workType->id,
            'primary_responsible_user_id' => $responsible->id,
            'organizational_unit_id' => $organizationalUnit->id,
            'current_status_id' => $status->id,
            'description' => 'Trabajo de campo con levantamiento de informacion.',
        ]);

        $response = $this->actingAs($user)->delete(route('admin.work-types.destroy', $workType));

        $response->assertRedirect(route('admin.work-types.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('work_type', [
            'id' => $workType->id,
        ]);
    }

    public function test_super_admin_can_deactivate_institutional_project_type(): void
    {
        $role = Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create([
            'cedula' => '8-999-1004',
            'is_active' => true,
        ]);
        $user->assignRole($role);

        $type = InstitutionalProjectType::create([
            'name' => 'Programa ambiental universitario',
            'description' => 'Iniciativas de sensibilizacion y reforestacion.',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->put(route('admin.institutional-project-types.update', $type), [
            'name' => 'Programa ambiental universitario',
            'description' => 'Iniciativas de sensibilizacion, reforestacion y educacion.',
            'is_active' => false,
        ]);

        $response->assertRedirect(route('admin.institutional-project-types.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('institutional_project_types', [
            'id' => $type->id,
            'is_active' => '0',
        ]);
    }
}
