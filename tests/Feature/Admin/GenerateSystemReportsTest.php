<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\OrganizationalUnit;
use App\Models\User;
use App\Models\WorkEvaluation;
use App\Models\WorkOfExtension;
use App\Models\WorkStatus;
use App\Models\WorkType;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;
use Tests\TestsWithSeeders;

class GenerateSystemReportsTest extends TestCase
{
    use TestsWithSeeders;

    protected function setUp(): void
    {
        parent::setUp();

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_super_admin_can_access_reports_index(): void
    {
        $user = $this->makeSuperAdmin();

        $response = $this->actingAs($user)->get(route('admin.reports.index'));

        $response->assertOk();
        $response->assertSeeText(__('admin.reports.title'));
    }

    public function test_generating_works_by_status_report_displays_counts(): void
    {
        $user = $this->makeSuperAdmin();
        $this->createSampleWorks();

        $response = $this->actingAs($user)->get(route('admin.reports.show', [
            'report' => 'works-by-status',
            'apply' => 1,
        ]));

        $response->assertOk();
        $response->assertSeeText('Trabajos por estado');
        $response->assertSeeText('Certificado');
        $response->assertSeeText('Borrador');
    }

    public function test_download_report_returns_csv_file(): void
    {
        $user = $this->makeSuperAdmin();
        $this->createSampleWorks();

        $response = $this->actingAs($user)->get(route('admin.reports.download', [
            'report' => 'works-by-status',
            'format' => 'csv',
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Estado', $response->streamedContent());
    }

    public function test_evaluation_statistics_report_shows_summary(): void
    {
        $user = $this->makeSuperAdmin();
        $this->createSampleWorksWithEvaluations();

        $response = $this->actingAs($user)->get(route('admin.reports.show', [
            'report' => 'evaluation-statistics',
            'apply' => 1,
        ]));

        $response->assertOk();
        $response->assertSeeText('Estadísticas de evaluación');
        $response->assertSeeText('Aprobado');
        $response->assertSeeText('Promedio ponderado (global)');
    }

    private function makeSuperAdmin(): User
    {
        $user = User::factory()->create([
            'cedula' => '8-999-3000',
            'is_active' => true,
        ]);

        $user->assignRole('super_admin');

        return $user;
    }

    private function createSampleWorks(): void
    {
        $workType = WorkType::first(); // Use existing work type from seeders
        $draftStatus = WorkStatus::where('name', 'Borrador')->first();
        $certifiedStatus = WorkStatus::where('name', 'Certificado')->first();
        $unit = OrganizationalUnit::find(100); // Campus Central from seeders

        $responsible = User::factory()->create([
            'cedula' => '8-999-3001',
            'is_active' => true,
        ]);

        WorkOfExtension::create([
            'title' => 'Programa de Alfabetización Digital',
            'work_type_id' => $workType->id,
            'primary_responsible_user_id' => $responsible->id,
            'organizational_unit_id' => $unit->id,
            'current_status_id' => $draftStatus->id,
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->toDateString(),
            'publication_consent' => false,
            'is_draft' => true,
            'submitted_at' => null,
            'description' => 'Programa piloto.',
            'academic_period' => '2025-1',
        ]);

        WorkOfExtension::create([
            'title' => 'Campaña de Salud Comunitaria',
            'work_type_id' => $workType->id,
            'primary_responsible_user_id' => $responsible->id,
            'organizational_unit_id' => $unit->id,
            'current_status_id' => $certifiedStatus->id,
            'start_date' => now()->subMonths(2)->toDateString(),
            'end_date' => now()->subMonth()->toDateString(),
            'publication_consent' => true,
            'is_draft' => false,
            'submitted_at' => now()->subMonths(2),
            'description' => 'Intervención con personal médico.',
            'academic_period' => '2025-1',
        ]);
    }

    private function createSampleWorksWithEvaluations(): void
    {
        $this->createSampleWorks();

        $work = WorkOfExtension::latest('id')->firstOrFail();

        $evaluator = User::factory()->create([
            'cedula' => '8-999-3002',
            'is_active' => true,
        ]);

        WorkEvaluation::create([
            'work_of_extension_id' => $work->id,
            'evaluator_user_id' => $evaluator->id,
            'general_comments' => 'Evaluación positiva.',
            'total_score' => 85.5,
            'weighted_score' => 82.3,
            'final_decision' => WorkEvaluation::DECISION_APPROVE,
            'decision_justification' => 'Cumple con los criterios establecidos.',
            'started_at' => now()->subDays(5),
            'submitted_at' => now()->subDays(2),
            'status' => WorkEvaluation::STATUS_SUBMITTED,
        ]);
    }
}
