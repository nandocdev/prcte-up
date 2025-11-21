<?php

declare(strict_types=1);

namespace Tests\Feature\Workflow;

use App\Models\User;
use App\Models\WorkOfExtension;
use App\Models\WorkStatus;
use App\Services\Dashboard\CoordinatorDashboardService;
use Tests\TestCase;
use Tests\TestsWithSeeders;

class CoordinatorWorkflowTest extends TestCase
{
    use TestsWithSeeders;

    public function test_coordinator_can_view_pending_works_dashboard(): void
    {
        $coordinator = User::factory()->create([
            'cedula' => '8-999-4000',
            'is_active' => true,
        ]);
        $coordinator->assignRole('coordinador_extension');

        // Create some works in different statuses
        $this->createSampleWorksForCoordinator();

        $response = $this->actingAs($coordinator)->get(route('coordinator.dashboard'));

        $response->assertOk();
        $response->assertSeeText(__('coordinator.dashboard.title'));
    }

    public function test_coordinator_can_approve_work(): void
    {
        $coordinator = User::factory()->create([
            'cedula' => '8-999-4000',
            'is_active' => true,
        ]);
        $coordinator->assignRole('coordinador_extension');

        $work = WorkOfExtension::factory()->create([
            'current_status_id' => WorkStatus::where('name', 'En Coordinador Extensión')->first()->id,
        ]);

        $response = $this->actingAs($coordinator)->post(route('coordinator.works.approve', $work), [
            'comments' => 'Trabajo aprobado por coordinador',
        ]);

        $response->assertRedirect(route('coordinator.works.show', $work));
        $response->assertSessionHas('success');

        $work->refresh();
        $this->assertEquals('En Decano/Director', $work->currentStatus->name);
    }

    public function test_coordinator_can_reject_work(): void
    {
        $coordinator = User::factory()->create([
            'cedula' => '8-999-4000',
            'is_active' => true,
        ]);
        $coordinator->assignRole('coordinador_extension');

        $work = WorkOfExtension::factory()->create([
            'current_status_id' => WorkStatus::where('name', 'En Coordinador Extensión')->first()->id,
        ]);

        $response = $this->actingAs($coordinator)->post(route('coordinator.works.reject', $work), [
            'comments' => 'Trabajo rechazado por coordinador',
        ]);

        $response->assertRedirect(route('coordinator.works.show', $work));
        $response->assertSessionHas('success');

        $work->refresh();
        $this->assertEquals('Rechazado por Coordinador', $work->currentStatus->name);
    }

    public function test_coordinator_can_request_changes(): void
    {
        $coordinator = User::factory()->create([
            'cedula' => '8-999-4000',
            'is_active' => true,
        ]);
        $coordinator->assignRole('coordinador_extension');

        $work = WorkOfExtension::factory()->create([
            'current_status_id' => WorkStatus::where('name', 'En Coordinador Extensión')->first()->id,
        ]);

        $response = $this->actingAs($coordinator)->post(route('coordinator.works.request-changes', $work), [
            'comments' => 'Se requieren cambios en el trabajo',
        ]);

        $response->assertRedirect(route('coordinator.works.show', $work));
        $response->assertSessionHas('success');

        $work->refresh();
        $this->assertEquals('En Corrección', $work->currentStatus->name);
    }

    private function createSampleWorksForCoordinator(): void
    {
        $pendingStatus = WorkStatus::where('name', 'En Coordinador Extensión')->first();
        $approvedStatus = WorkStatus::where('name', 'Aprobado por Coordinador')->first();

        $responsible = User::factory()->create([
            'cedula' => '8-999-4001',
            'is_active' => true,
        ]);

        // Create works in different statuses
        WorkOfExtension::factory()->create([
            'title' => 'Trabajo pendiente de revisión',
            'primary_responsible_user_id' => $responsible->id,
            'current_status_id' => $pendingStatus->id,
            'organizational_unit_id' => 100,
        ]);

        WorkOfExtension::factory()->create([
            'title' => 'Trabajo aprobado recientemente',
            'primary_responsible_user_id' => $responsible->id,
            'current_status_id' => $approvedStatus->id,
            'organizational_unit_id' => 100,
        ]);
    }
}