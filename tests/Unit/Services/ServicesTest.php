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

      // Create a sample work for testing using correct status for coordinator approval
      $work = WorkOfExtension::factory()->create([
         'current_status_id' => WorkStatus::where('name', 'En Revisión Coordinador')->first()->id,
      ]);

      // Test coordinator permissions
      $this->assertTrue($service->canCoordinatorReviewWork($coordinator, $work));
      $this->assertTrue($service->canCoordinatorApproveWork($work));
      $this->assertTrue($service->canCoordinatorRequestChanges($work));

      // Test dean permissions - create work in correct status for dean
      $deanWork = WorkOfExtension::factory()->create([
         'current_status_id' => WorkStatus::where('name', 'Enviado a Decano/Director')->first()->id,
      ]);
      $this->assertTrue($service->canDeanDirectorReviewWork($dean, $deanWork));
      $this->assertTrue($service->canDeanDirectorApproveWork($deanWork));
      $this->assertTrue($service->canDeanDirectorRequestChanges($deanWork));

      // Test viex permissions - create work in correct status for viex
      $viexWork = WorkOfExtension::factory()->create([
         'current_status_id' => WorkStatus::where('name', 'En VIEX - En Evaluación')->first()->id,
      ]);
      $this->assertTrue($service->canViexApproveWork($viexWork));
      $this->assertTrue($service->canViexRejectWork($viexWork));
      $this->assertTrue($service->canViexRequestChanges($viexWork));
    }

    public function test_work_listing_service_filters_correctly(): void
    {
        $service = app(WorkListingService::class);

        $user = User::factory()->create();
        $user->assignRole('viex_admin');

        // Create sample works
        $this->createSampleWorksForListing();

      // Create a request with filters
      $request = new \Illuminate\Http\Request();
      $request->merge(['status' => 'draft']);

      $result = $service->getWorksListing($request, $user);

      $this->assertArrayHasKey('works', $result);
      $this->assertArrayHasKey('statistics', $result);

      // All works should be drafts since we filtered by 'draft'
      foreach ($result['works'] as $work) {
         $this->assertTrue($work->is_draft);
        }
    }

    public function test_publication_service_authorizes_publication(): void
    {
        $service = app(PublicationService::class);

        $viexAdmin = User::factory()->create();
        $viexAdmin->assignRole('viex_admin');

        $work = WorkOfExtension::factory()->create([
            'current_status_id' => WorkStatus::where('name', 'Certificado')->first()->id,
         'publication_consent' => false, // Start with false
      ]);

      // Authorize publication
      $result = $service->authorizePublication($work, $viexAdmin, true);

      $this->assertInstanceOf(WorkOfExtension::class, $result);
        $work->refresh();
      $this->assertTrue($work->publication_consent);
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