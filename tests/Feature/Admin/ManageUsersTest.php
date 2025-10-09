<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\OrganizationalUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ManageUsersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_super_admin_can_deactivate_user(): void
    {
        $superAdminRole = Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $organizationalUnit = OrganizationalUnit::create([
            'name' => 'Facultad de Pruebas',
            'type' => 'facultad',
        ]);

        $superAdmin = User::factory()->create([
            'cedula' => '8-123-4567',
            'main_organizational_unit_id' => $organizationalUnit->id,
            'is_active' => true,
        ]);
        $superAdmin->assignRole($superAdminRole);

        $user = User::factory()->create([
            'cedula' => '8-765-4321',
            'professor_code' => 'PRF-100',
            'main_organizational_unit_id' => $organizationalUnit->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($superAdmin)
            ->put(route('admin.users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'professor_code' => $user->professor_code,
                'organizational_unit_id' => $organizationalUnit->id,
                'roles' => [],
                'is_active' => false,
            ]);

        $response->assertRedirect(route('admin.users.show', $user));
        $response->assertSessionHas('success');

        $this->assertFalse($user->fresh()->is_active);
    }
}
