<?php

declare(strict_types=1);

namespace Tests\Feature\Workflow;

use App\Models\User;
use App\Models\WorkOfExtension;
use App\Models\WorkStatus;
use Tests\TestCase;
use Tests\TestsWithSeeders;

class DeanDirectorWorkflowTest extends TestCase
{
    use TestsWithSeeders;

    public function test_dean_director_can_view_pending_works_dashboard(): void
    {
        $dean = User::factory()->create([
            'cedula' => '8-999-5000',
            'is_active' => true,
        ]);
        $dean->assignRole('decano_director');

        // Create some works in different statuses
        $this->createSampleWorksForDean();

        $response = $this->actingAs($dean)->get(route('dean-director.dashboard'));

        $response->assertOk();
        $response->assertSeeText(__('dean_director.dashboard.title'));
    }

    public function test_dean_director_can_approve_work(): void
    {
        $dean = User::factory()->create([
            'cedula' => '8-999-5000',
            'is_active' => true,
        ]);
        $dean->assignRole('decano_director');

        $work = WorkOfExtension::factory()->create([
            'current_status_id' => WorkStatus::where('name', 'En Decano/Director')->first()->id,
        ]);

        $response = $this->actingAs($dean)->post(route('dean-director.works.approve', $work), [
            'comments' => 'Trabajo aprobado por decano',
        ]);

        $response->assertRedirect(route('dean-director.works.show', $work));
        $response->assertSessionHas('success');

        $work->refresh();
        $this->assertEquals('En VIEX', $work->currentStatus->name);
    }

    public function test_dean_director_can_reject_work(): void
    {
        $dean = User::factory()->create([
            'cedula' => '8-999-5000',
            'is_active' => true,
        ]);
        $dean->assignRole('decano_director');

        $work = WorkOfExtension::factory()->create([
            'current_status_id' => WorkStatus::where('name', 'En Decano/Director')->first()->id,
        ]);

        $response = $this->actingAs($dean)->post(route('dean-director.works.reject', $work), [
            'comments' => 'Trabajo rechazado por decano',
        ]);

        $response->assertRedirect(route('dean-director.works.show', $work));
        $response->assertSessionHas('success');

        $work->refresh();
        $this->assertEquals('Rechazado por Decano/Director', $work->currentStatus->name);
    }

    private function createSampleWorksForDean(): void
    {
        $pendingStatus = WorkStatus::where('name', 'En Decano/Director')->first();
        $approvedStatus = WorkStatus::where('name', 'Aprobado Internamente')->first();

        $responsible = User::factory()->create([
            'cedula' => '8-999-5001',
            'is_active' => true,
        ]);

        // Create works in different statuses
        WorkOfExtension::factory()->create([
            'title' => 'Trabajo pendiente de aprobación decanal',
            'primary_responsible_user_id' => $responsible->id,
            'current_status_id' => $pendingStatus->id,
            'organizational_unit_id' => 100,
        ]);

        WorkOfExtension::factory()->create([
            'title' => 'Trabajo aprobado por decano',
            'primary_responsible_user_id' => $responsible->id,
            'current_status_id' => $approvedStatus->id,
            'organizational_unit_id' => 100,
        ]);
    }
}