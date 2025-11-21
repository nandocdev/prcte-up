<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\WorkOfExtension;
use App\Models\WorkStatus;
use App\Services\Authorization\WorkAuthorizationService;
use App\Services\Dashboard\CoordinatorDashboardService;
use App\Services\Dashboard\DeanDirectorDashboardService;
use App\Services\Dashboard\ViexDashboardService;
use App\Services\Dashboard\WorkListingService;
use App\Services\WorkOfExtension\PublicationService;
use Tests\TestCase;
use Tests\TestsWithSeeders;

class ServicesTest extends TestCase
{
    use TestsWithSeeders;

    public function test_work_authorization_service_checks_permissions(): void
    {
        $service = app(WorkAuthorizationService::class);

        $coordinator = User::factory()->create();
        $coordinator->assignRole('coordinador_extension');

        $dean = User::factory()->create();
        $dean->assignRole('decano_director');

        $viexAdmin = User::factory()->create();
        $viexAdmin->assignRole('viex_admin');

        $professor = User::factory()->create();
        $professor->assignRole('profesor');

        // Test coordinator permissions
        $this->assertTrue($service->canApproveByCoordinator($coordinator));
        $this->assertTrue($service->canRejectByCoordinator($coordinator));
        $this->assertTrue($service->canRequestChangesByCoordinator($coordinator));

        // Test dean permissions
        $this->assertTrue($service->canApproveByDeanDirector($dean));
        $this->assertTrue($service->canRejectByDeanDirector($dean));

        // Test viex permissions
        $this->assertTrue($service->canApproveByViex($viexAdmin));
        $this->assertTrue($service->canRejectByViex($viexAdmin));
        $this->assertTrue($service->canRequestChangesByViex($viexAdmin));
        $this->assertTrue($service->canAuthorizePublication($viexAdmin));

        // Test professor permissions
        $this->assertTrue($service->canCreateWork($professor));
        $this->assertTrue($service->canEditOwnWork($professor));
        $this->assertTrue($service->canSubmitWork($professor));
    }

    public function test_coordinator_dashboard_service_returns_correct_data(): void
    {
        $service = app(CoordinatorDashboardService::class);

        $coordinator = User::factory()->create();
        $coordinator->assignRole('coordinador_extension');

        // Create sample works
        $this->createSampleWorksForDashboard();

        $stats = $service->getDashboardStats($coordinator);

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('pending_works_count', $stats);
        $this->assertArrayHasKey('approved_this_month_count', $stats);
        $this->assertArrayHasKey('rejected_this_month_count', $stats);
    }

    public function test_work_listing_service_filters_correctly(): void
    {
        $service = app(WorkListingService::class);

        $user = User::factory()->create();
        $user->assignRole('viex_admin');

        // Create sample works
        $this->createSampleWorksForListing();

        $filters = ['status' => 'borrador'];
        $works = $service->getFilteredWorks($user, $filters, 10);

        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $works);

        foreach ($works as $work) {
            $this->assertEquals('Borrador', $work->currentStatus->name);
        }
    }

    public function test_publication_service_authorizes_publication(): void
    {
        $service = app(PublicationService::class);

        $viexAdmin = User::factory()->create();
        $viexAdmin->assignRole('viex_admin');

        $work = WorkOfExtension::factory()->create([
            'current_status_id' => WorkStatus::where('name', 'Certificado')->first()->id,
            'publication_consent' => true,
        ]);

        $result = $service->authorizePublication($work, $viexAdmin, 'Publicación autorizada');

        $this->assertTrue($result);
        $work->refresh();
        // Check if publication was authorized (this would depend on your implementation)
    }

    private function createSampleWorksForDashboard(): void
    {
        $pendingStatus = WorkStatus::where('name', 'En Coordinador Extensión')->first();
        $approvedStatus = WorkStatus::where('name', 'Aprobado por Coordinador')->first();
        $rejectedStatus = WorkStatus::where('name', 'Rechazado por Coordinador')->first();

        $responsible = User::factory()->create();

        WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $responsible->id,
            'current_status_id' => $pendingStatus->id,
        ]);

        WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $responsible->id,
            'current_status_id' => $approvedStatus->id,
        ]);

        WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $responsible->id,
            'current_status_id' => $rejectedStatus->id,
        ]);
    }

    private function createSampleWorksForListing(): void
    {
        $draftStatus = WorkStatus::where('name', 'Borrador')->first();
        $submittedStatus = WorkStatus::where('name', 'En Coordinador Extensión')->first();

        $responsible = User::factory()->create();

        WorkOfExtension::factory()->create([
            'title' => 'Trabajo en borrador',
            'primary_responsible_user_id' => $responsible->id,
            'current_status_id' => $draftStatus->id,
        ]);

        WorkOfExtension::factory()->create([
            'title' => 'Trabajo enviado',
            'primary_responsible_user_id' => $responsible->id,
            'current_status_id' => $submittedStatus->id,
        ]);
    }
}