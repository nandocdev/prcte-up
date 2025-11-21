<?php

declare(strict_types=1);

namespace Tests\Feature\Workflow;

use App\Models\User;
use App\Models\WorkOfExtension;
use App\Models\WorkStatus;
use Tests\TestCase;
use Tests\TestsWithSeeders;

class ViexWorkflowTest extends TestCase
{
    use TestsWithSeeders;

    public function test_viex_admin_can_view_dashboard(): void
    {
        $viexAdmin = User::factory()->create([
            'cedula' => '8-999-6000',
            'is_active' => true,
        ]);
        $viexAdmin->assignRole('viex_admin');

        // Create some works in different statuses
        $this->createSampleWorksForViex();

        $response = $this->actingAs($viexAdmin)->get(route('viex.dashboard'));

        $response->assertOk();
        $response->assertSeeText(__('viex.dashboard.title'));
    }

    public function test_viex_admin_can_approve_work(): void
    {
        $viexAdmin = User::factory()->create([
            'cedula' => '8-999-6000',
            'is_active' => true,
        ]);
        $viexAdmin->assignRole('viex_admin');

        $work = WorkOfExtension::factory()->create([
            'current_status_id' => WorkStatus::where('name', 'En VIEX')->first()->id,
        ]);

        $response = $this->actingAs($viexAdmin)->post(route('viex.works.approve', $work), [
            'comments' => 'Trabajo aprobado por VIEX',
        ]);

        $response->assertRedirect(route('viex.works.show', $work));
        $response->assertSessionHas('success');

        $work->refresh();
        $this->assertEquals('Certificado', $work->currentStatus->name);
    }

    public function test_viex_admin_can_reject_work(): void
    {
        $viexAdmin = User::factory()->create([
            'cedula' => '8-999-6000',
            'is_active' => true,
        ]);
        $viexAdmin->assignRole('viex_admin');

        $work = WorkOfExtension::factory()->create([
            'current_status_id' => WorkStatus::where('name', 'En VIEX')->first()->id,
        ]);

        $response = $this->actingAs($viexAdmin)->post(route('viex.works.reject', $work), [
            'comments' => 'Trabajo rechazado por VIEX',
        ]);

        $response->assertRedirect(route('viex.works.show', $work));
        $response->assertSessionHas('success');

        $work->refresh();
        $this->assertEquals('Rechazado por VIEX', $work->currentStatus->name);
    }

    public function test_viex_admin_can_request_changes(): void
    {
        $viexAdmin = User::factory()->create([
            'cedula' => '8-999-6000',
            'is_active' => true,
        ]);
        $viexAdmin->assignRole('viex_admin');

        $work = WorkOfExtension::factory()->create([
            'current_status_id' => WorkStatus::where('name', 'En VIEX')->first()->id,
        ]);

        $response = $this->actingAs($viexAdmin)->post(route('viex.works.request-changes', $work), [
            'comments' => 'Se requieren cambios en el trabajo',
        ]);

        $response->assertRedirect(route('viex.works.show', $work));
        $response->assertSessionHas('success');

        $work->refresh();
        $this->assertEquals('En Corrección', $work->currentStatus->name);
    }

    public function test_viex_admin_can_authorize_publication(): void
    {
        $viexAdmin = User::factory()->create([
            'cedula' => '8-999-6000',
            'is_active' => true,
        ]);
        $viexAdmin->assignRole('viex_admin');

        $work = WorkOfExtension::factory()->create([
            'current_status_id' => WorkStatus::where('name', 'Certificado')->first()->id,
            'publication_consent' => true,
        ]);

        $response = $this->actingAs($viexAdmin)->post(route('viex.works.authorize-publication', $work), [
            'comments' => 'Publicación autorizada',
        ]);

        $response->assertRedirect(route('viex.works.show', $work));
        $response->assertSessionHas('success');
    }

    private function createSampleWorksForViex(): void
    {
        $pendingStatus = WorkStatus::where('name', 'En VIEX')->first();
        $certifiedStatus = WorkStatus::where('name', 'Certificado')->first();

        $responsible = User::factory()->create([
            'cedula' => '8-999-6001',
            'is_active' => true,
        ]);

        // Create works in different statuses
        WorkOfExtension::factory()->create([
            'title' => 'Trabajo en evaluación VIEX',
            'primary_responsible_user_id' => $responsible->id,
            'current_status_id' => $pendingStatus->id,
            'organizational_unit_id' => 100,
        ]);

        WorkOfExtension::factory()->create([
            'title' => 'Trabajo certificado',
            'primary_responsible_user_id' => $responsible->id,
            'current_status_id' => $certifiedStatus->id,
            'organizational_unit_id' => 100,
        ]);
    }
}