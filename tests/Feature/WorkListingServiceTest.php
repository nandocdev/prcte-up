<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WorkOfExtension;
use App\Models\WorkStatus;
use App\Models\WorkType;
use App\Services\Dashboard\WorkListingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class WorkListingServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Cache de estados creados para evitar duplicados en los tests
     */
    protected array $statusCache = [];

    protected WorkType $defaultWorkType;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Role::firstOrCreate(['name' => 'profesor']);

        $this->defaultWorkType = WorkType::factory()->create([
            'name' => 'Proyecto de Extensión',
            'description' => 'Proyecto de prueba',
            'is_active' => true,
        ]);
    }

    public function test_professor_only_sees_own_works_in_listing(): void
    {
        $service = app(WorkListingService::class);

        $userA = User::factory()->create();
        $userA->assignRole('profesor');

        $userB = User::factory()->create();
        $userB->assignRole('profesor');

        $this->createWorkFor($userA, 'Borrador');
        $this->createWorkFor($userA, 'Enviado a Coordinador', [
            'is_draft' => false,
        ]);
        $this->createWorkFor($userB, 'Enviado a Coordinador', [
            'is_draft' => false,
        ]);

        $request = Request::create('/works', 'GET');

        $result = $service->getWorksListing($request, $userA);

        $this->assertCount(2, $result['works']);
        $this->assertTrue(
            $result['works']->every(
                fn (WorkOfExtension $work) => (int) $work->primary_responsible_user_id === $userA->getKey()
            )
        );
    }

    public function test_status_filter_sent_to_coordinator_only_returns_matching_records(): void
    {
        $service = app(WorkListingService::class);

        $user = User::factory()->create();
        $user->assignRole('profesor');

        $sent = $this->createWorkFor($user, 'Enviado a Coordinador', [
            'is_draft' => false,
            'title' => 'Proyecto enviado',
        ]);
        $this->createWorkFor($user, 'Devuelto para Corrección', [
            'is_draft' => false,
            'title' => 'Proyecto devuelto',
        ]);
        $this->createWorkFor($user, 'En VIEX - En Evaluación', [
            'is_draft' => false,
            'title' => 'Proyecto en VIEX',
        ]);

        $request = Request::create('/works', 'GET', ['status' => 'sent_to_coordinator']);

        $result = $service->getWorksListing($request, $user);

        $this->assertCount(1, $result['works']);
        $this->assertTrue($result['works']->first()->is($sent));
    }

    public function test_filters_by_work_type_and_academic_period(): void
    {
        $service = app(WorkListingService::class);

        $user = User::factory()->create();
        $user->assignRole('profesor');

        $activityType = WorkType::factory()->create([
            'name' => 'Actividad',
            'is_active' => true,
        ]);

        $this->createWorkFor($user, 'Enviado a Coordinador', [
            'is_draft' => false,
            'work_type_id' => $this->defaultWorkType->id,
            'academic_period' => '2025-I',
        ]);

        $targetWork = $this->createWorkFor($user, 'Enviado a Coordinador', [
            'is_draft' => false,
            'work_type_id' => $activityType->id,
            'academic_period' => '2025-II',
        ]);

        $request = Request::create('/works', 'GET', [
            'work_type' => $activityType->id,
            'academic_period' => '2025-II',
        ]);

        $result = $service->getWorksListing($request, $user);

        $this->assertCount(1, $result['works']);
        $this->assertTrue($result['works']->first()->is($targetWork));
    }

    public function test_search_filter_matches_title_or_description(): void
    {
        $service = app(WorkListingService::class);

        $user = User::factory()->create();
        $user->assignRole('profesor');

        $match = $this->createWorkFor($user, 'Enviado a Coordinador', [
            'is_draft' => false,
            'title' => 'Programa de alfabetización comunitaria',
            'description' => 'Este programa se enfoca en la educación básica',
        ]);

        $this->createWorkFor($user, 'Enviado a Coordinador', [
            'is_draft' => false,
            'title' => 'Proyecto de matemáticas avanzadas',
            'description' => 'Curso avanzado de cálculo diferencial',
        ]);

        $request = Request::create('/works', 'GET', [
            'search' => 'comunitaria',
        ]);

        $result = $service->getWorksListing($request, $user);

        $this->assertCount(1, $result['works']);
        $this->assertTrue($result['works']->first()->is($match));
    }

    public function test_date_filter_returns_works_within_date_range(): void
    {
        $service = app(WorkListingService::class);

        $user = User::factory()->create();
        $user->assignRole('profesor');

        // Crear trabajos con fechas específicas
        $oldWork = $this->createWorkFor($user, 'Enviado a Coordinador', [
            'is_draft' => false,
            'created_at' => now()->subDays(10),
        ]);

        $targetWork = $this->createWorkFor($user, 'Enviado a Coordinador', [
            'is_draft' => false,
            'created_at' => now()->subDays(5),
        ]);

        $newWork = $this->createWorkFor($user, 'Enviado a Coordinador', [
            'is_draft' => false,
            'created_at' => now()->subDays(1),
        ]);

        $request = Request::create('/works', 'GET', [
            'date_from' => now()->subDays(7)->format('Y-m-d'),
            'date_to' => now()->subDays(3)->format('Y-m-d'),
        ]);

        $result = $service->getWorksListing($request, $user);

        $this->assertCount(1, $result['works']);
        $this->assertTrue($result['works']->first()->is($targetWork));
    }

    private function createWorkFor(User $user, string $statusName, array $attributes = []): WorkOfExtension
    {
        $status = $this->ensureStatus($statusName);

        $defaults = [
            'primary_responsible_user_id' => $user->getKey(),
            'is_draft' => $statusName === 'Borrador',
            'submitted_at' => $statusName === 'Borrador' ? null : now(),
            'current_status_id' => $status->getKey(),
            'work_type_id' => $attributes['work_type_id'] ?? $this->defaultWorkType->getKey(),
            'academic_period' => $attributes['academic_period'] ?? '2025-I',
        ];

        $state = array_merge($defaults, $attributes);
        unset($state['status_name']);

        return WorkOfExtension::factory()
            ->for($user, 'responsibleUser')
            ->state($state)
            ->create();
    }

    private function ensureStatus(string $name): WorkStatus
    {
        if (isset($this->statusCache[$name])) {
            return $this->statusCache[$name];
        }

        return $this->statusCache[$name] = WorkStatus::factory()->create([
            'name' => $name,
            'description' => $name,
            'is_active' => true,
        ]);
    }
}
