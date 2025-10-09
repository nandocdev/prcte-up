<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\OrganizationalUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ManageOrganizationalUnitsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_super_admin_can_create_organizational_unit(): void
    {
        $role = Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create([
            'cedula' => '8-555-1234',
            'is_active' => true,
        ]);
        $user->assignRole($role);

        $type = array_key_first(OrganizationalUnit::typeOptions());

        $response = $this->actingAs($user)
            ->post(route('admin.organizational-units.store'), [
                'name' => 'Facultad de Pruebas',
                'type' => $type,
                'parent_id' => null,
            ]);

        $response->assertRedirect(route('admin.organizational-units.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('organizational_units', [
            'name' => 'Facultad de Pruebas',
            'type' => $type,
        ]);
    }

    public function test_super_admin_can_update_organizational_unit(): void
    {
        $role = Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create([
            'cedula' => '8-555-1235',
            'is_active' => true,
        ]);
        $user->assignRole($role);

        $parent = OrganizationalUnit::create([
            'name' => 'Campus Central',
            'type' => 'Main Campus',
        ]);

        $unit = OrganizationalUnit::create([
            'name' => 'Facultad de Ciencias',
            'type' => 'Faculty',
        ]);

        $response = $this->actingAs($user)
            ->put(route('admin.organizational-units.update', $unit), [
                'name' => 'Facultad de Ciencias Actualizada',
                'type' => 'Faculty',
                'parent_id' => $parent->id,
            ]);

        $response->assertRedirect(route('admin.organizational-units.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('organizational_units', [
            'id' => $unit->id,
            'name' => 'Facultad de Ciencias Actualizada',
            'parent_id' => $parent->id,
        ]);
    }

    public function test_cannot_assign_descendant_as_parent(): void
    {
        $role = Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create([
            'cedula' => '8-555-1236',
            'is_active' => true,
        ]);
        $user->assignRole($role);

        $root = OrganizationalUnit::create([
            'name' => 'Campus Central',
            'type' => 'Main Campus',
        ]);

        $child = OrganizationalUnit::create([
            'name' => 'Facultad de Ciencias',
            'type' => 'Faculty',
            'parent_id' => $root->id,
        ]);

        $response = $this->actingAs($user)
            ->from(route('admin.organizational-units.edit', $root))
            ->put(route('admin.organizational-units.update', $root), [
                'name' => 'Campus Central',
                'type' => 'Main Campus',
                'parent_id' => $child->id,
            ]);

        $response->assertRedirect(route('admin.organizational-units.edit', $root));
        $response->assertSessionHasErrors('parent_id');

        $this->assertDatabaseHas('organizational_units', [
            'id' => $root->id,
            'parent_id' => null,
        ]);
    }
}
