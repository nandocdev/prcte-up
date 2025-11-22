<?php

namespace Tests\Feature;

use App\Models\OrganizationalUnit;
use App\Models\User;
use App\Models\WorkOfExtension;
use App\Models\WorkStatus;
use App\Models\WorkType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class WorkCorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_canBeEditedForCorrection_returns_true_for_draft_and_rejected_states(): void
    {
        // Crear datos necesarios
        $user = User::factory()->create();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Role::firstOrCreate(['name' => 'profesor']);

        $orgUnit = OrganizationalUnit::create([
            'name' => 'Facultad de Ingeniería',
            'type' => 'faculty',
        ]);

        $draftStatus = WorkStatus::create([
            'name' => 'Borrador',
            'description' => 'Estado inicial',
            'is_active' => true,
        ]);

        $rejectedStatus = WorkStatus::create([
            'name' => 'Rechazado por Coordinador',
            'description' => 'Rechazado por coordinador',
            'is_active' => true,
        ]);

        $workType = WorkType::create([
            'name' => 'Proyecto',
            'description' => 'Proyecto de prueba',
            'is_active' => true,
        ]);

        // Trabajo en borrador
        $draftWork = WorkOfExtension::create([
            'title' => 'Trabajo en borrador',
            'work_type_id' => $workType->id,
            'primary_responsible_user_id' => $user->id,
            'organizational_unit_id' => $orgUnit->id,
            'current_status_id' => $draftStatus->id,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(30),
            'description' => 'Descripción del trabajo en borrador',
            'academic_period' => '2025-I',
            'is_draft' => true,
        ]);

        // Trabajo rechazado
        $rejectedWork = WorkOfExtension::create([
            'title' => 'Trabajo rechazado',
            'work_type_id' => $workType->id,
            'primary_responsible_user_id' => $user->id,
            'organizational_unit_id' => $orgUnit->id,
            'current_status_id' => $rejectedStatus->id,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(30),
            'description' => 'Descripción del trabajo rechazado',
            'academic_period' => '2025-I',
            'is_draft' => false,
        ]);

        $this->assertTrue($draftWork->canBeEditedForCorrection());
        $this->assertTrue($rejectedWork->canBeEditedForCorrection());
    }

    public function test_canBeEditedForCorrection_returns_false_for_approved_states(): void
    {
        // Crear datos necesarios
        $user = User::factory()->create();

        $orgUnit = OrganizationalUnit::create([
            'name' => 'Facultad de Ingeniería',
            'type' => 'faculty',
        ]);

        $approvedStatus = WorkStatus::create([
            'name' => 'Certificado',
            'is_active' => true,
        ]);

        $workType = WorkType::create([
            'name' => 'Proyecto',
            'description' => 'Proyecto de prueba',
            'is_active' => true,
        ]);

        $work = WorkOfExtension::create([
            'title' => 'Trabajo aprobado',
            'work_type_id' => $workType->id,
            'primary_responsible_user_id' => $user->id,
            'organizational_unit_id' => $orgUnit->id,
            'current_status_id' => $approvedStatus->id,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(30),
            'description' => 'Descripción del trabajo aprobado',
            'academic_period' => '2025-I',
            'is_draft' => false,
        ]);

        $this->assertFalse($work->canBeEditedForCorrection());
    }
}
