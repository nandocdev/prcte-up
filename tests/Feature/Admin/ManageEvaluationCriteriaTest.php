<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\EvaluationCriteria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ManageEvaluationCriteriaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_super_admin_can_create_evaluation_criterion(): void
    {
        Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create([
            'cedula' => '8-999-2000',
            'is_active' => true,
        ]);
        $user->assignRole('super_admin');

        $response = $this->actingAs($user)->post(route('admin.evaluation-criteria.store'), [
            'name' => 'Impacto regional',
            'description' => 'Criterio para medir el crecimiento del impacto en regiones priorizadas.',
            'category' => 'Impacto',
            'max_score' => 10,
            'weight' => 20,
            'order' => 15,
            'is_active' => true,
            'is_required' => true,
        ]);

        $response->assertRedirect(route('admin.evaluation-criteria.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('evaluation_criteria', [
            'name' => 'Impacto regional',
            'weight' => 20,
            'is_active' => true,
            'is_required' => true,
        ]);
    }

    public function test_viex_admin_can_update_evaluation_criterion(): void
    {
        Role::create([
            'name' => 'viex_admin',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create([
            'cedula' => '8-999-2001',
            'is_active' => true,
        ]);
        $user->assignRole('viex_admin');

        $criterion = EvaluationCriteria::create([
            'name' => 'Calidad de evidencias',
            'description' => 'Evalua la pertinencia de las evidencias adjuntas.',
            'category' => 'Documentacion',
            'max_score' => 10,
            'weight' => 25,
            'order' => 5,
            'is_active' => true,
            'is_required' => true,
        ]);

        $response = $this->actingAs($user)->put(route('admin.evaluation-criteria.update', $criterion), [
            'name' => 'Calidad de evidencias',
            'description' => 'Evalua la pertinencia y calidad de las evidencias suministradas.',
            'category' => 'Documentacion',
            'max_score' => 20,
            'weight' => 15,
            'order' => 4,
            'is_active' => false,
            'is_required' => false,
        ]);

        $response->assertRedirect(route('admin.evaluation-criteria.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('evaluation_criteria', [
            'id' => $criterion->id,
            'max_score' => 20,
            'weight' => 15,
            'order' => 4,
            'is_active' => false,
            'is_required' => false,
        ]);
    }

    public function test_sync_updates_weights_and_requires_total_of_one_hundred(): void
    {
        Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create([
            'cedula' => '8-999-2002',
            'is_active' => true,
        ]);
        $user->assignRole('super_admin');

        $criterionA = EvaluationCriteria::create([
            'name' => 'Pertinencia',
            'description' => 'Analiza la pertinencia del trabajo.',
            'category' => 'Pertinencia',
            'max_score' => 10,
            'weight' => 40,
            'order' => 1,
            'is_active' => true,
            'is_required' => true,
        ]);

        $criterionB = EvaluationCriteria::create([
            'name' => 'Innovacion',
            'description' => 'Valora la innovacion del trabajo.',
            'category' => 'Metodologia',
            'max_score' => 10,
            'weight' => 30,
            'order' => 2,
            'is_active' => true,
            'is_required' => true,
        ]);

        $criterionC = EvaluationCriteria::create([
            'name' => 'Evidencias',
            'description' => 'Evalua las evidencias.',
            'category' => 'Documentacion',
            'max_score' => 10,
            'weight' => 30,
            'order' => 3,
            'is_active' => true,
            'is_required' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.evaluation-criteria.sync'), [
            'criteria' => [
                [
                    'id' => $criterionA->id,
                    'weight' => 30,
                    'order' => 1,
                    'is_active' => true,
                ],
                [
                    'id' => $criterionB->id,
                    'weight' => 40,
                    'order' => 2,
                    'is_active' => true,
                ],
                [
                    'id' => $criterionC->id,
                    'weight' => 30,
                    'order' => 3,
                    'is_active' => true,
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.evaluation-criteria.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('evaluation_criteria', [
            'id' => $criterionB->id,
            'weight' => 40,
            'order' => 2,
        ]);
    }

    public function test_sync_fails_when_weights_do_not_add_up_to_one_hundred(): void
    {
        Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create([
            'cedula' => '8-999-2003',
            'is_active' => true,
        ]);
        $user->assignRole('super_admin');

        $criterion = EvaluationCriteria::create([
            'name' => 'Cobertura',
            'description' => 'Mide la cobertura alcanzada.',
            'category' => 'Impacto',
            'max_score' => 10,
            'weight' => 100,
            'order' => 1,
            'is_active' => true,
            'is_required' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.evaluation-criteria.sync'), [
            'criteria' => [
                [
                    'id' => $criterion->id,
                    'weight' => 90,
                    'order' => 1,
                    'is_active' => true,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('criteria');
        $this->assertDatabaseHas('evaluation_criteria', [
            'id' => $criterion->id,
            'weight' => 100,
        ]);
    }
}
