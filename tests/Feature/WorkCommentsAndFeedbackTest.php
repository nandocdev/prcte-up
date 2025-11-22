<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WorkOfExtension;
use App\Models\WorkStatus;
use App\Models\WorkStatusHistory;
use App\Models\WorkType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class WorkCommentsAndFeedbackTest extends TestCase
{
    use RefreshDatabase;

    protected WorkType $defaultWorkType;
    protected WorkStatus $draftStatus;
    protected WorkStatus $approvedStatus;
    protected WorkStatus $rejectedStatus;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Role::firstOrCreate(['name' => 'profesor']);
        Role::firstOrCreate(['name' => 'coordinador_extension']);

        $this->defaultWorkType = WorkType::factory()->create([
            'name' => 'Proyecto de Extensión',
            'description' => 'Proyecto de prueba',
            'is_active' => true,
        ]);

        $this->draftStatus = WorkStatus::factory()->create([
            'name' => 'Borrador',
            'description' => 'Estado inicial',
            'is_active' => true,
        ]);

        $this->approvedStatus = WorkStatus::factory()->create([
            'name' => 'Aprobado por Coordinador',
            'description' => 'Aprobado por coordinador',
            'is_active' => true,
        ]);

        $this->rejectedStatus = WorkStatus::factory()->create([
            'name' => 'Rechazado por Coordinador',
            'description' => 'Rechazado por coordinador',
            'is_active' => true,
        ]);
    }

    public function test_getCommentsAndFeedback_returns_only_comments_with_content(): void
    {
        $user = User::factory()->create();
        $user->assignRole('profesor');

        $coordinator = User::factory()->create();
        $coordinator->assignRole('coordinador_extension');

        $work = WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $user->getKey(),
            'current_status_id' => $this->draftStatus->getKey(),
            'work_type_id' => $this->defaultWorkType->getKey(),
        ]);

        // Crear historial con comentarios
        WorkStatusHistory::create([
            'work_of_extension_id' => $work->getKey(),
            'from_status_id' => $this->draftStatus->getKey(),
            'to_status_id' => $this->approvedStatus->getKey(),
            'changed_by_user_id' => $coordinator->getKey(),
            'comments' => 'Trabajo aprobado. Excelente documentación.',
        ]);

        // Crear historial sin comentarios (debe ser excluido)
        WorkStatusHistory::create([
            'work_of_extension_id' => $work->getKey(),
            'from_status_id' => $this->approvedStatus->getKey(),
            'to_status_id' => $this->approvedStatus->getKey(),
            'changed_by_user_id' => $coordinator->getKey(),
            'comments' => null,
        ]);

        // Crear historial con comentarios vacíos (debe ser excluido)
        WorkStatusHistory::create([
            'work_of_extension_id' => $work->getKey(),
            'from_status_id' => $this->approvedStatus->getKey(),
            'to_status_id' => $this->approvedStatus->getKey(),
            'changed_by_user_id' => $coordinator->getKey(),
            'comments' => '',
        ]);

        $comments = $work->getCommentsAndFeedback();

        $this->assertCount(1, $comments);
        $this->assertEquals('Trabajo aprobado. Excelente documentación.', $comments->first()->comments);
        $this->assertEquals($coordinator->getKey(), $comments->first()->changed_by_user_id);
    }

    public function test_getCommentsAndFeedback_orders_by_creation_date_descending(): void
    {
        $user = User::factory()->create();
        $user->assignRole('profesor');

        $coordinator = User::factory()->create();
        $coordinator->assignRole('coordinador_extension');

        $work = WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $user->getKey(),
            'current_status_id' => $this->draftStatus->getKey(),
            'work_type_id' => $this->defaultWorkType->getKey(),
        ]);

        // Crear comentarios con timestamps explícitos para asegurar orden
        $comment1 = WorkStatusHistory::create([
            'work_of_extension_id' => $work->getKey(),
            'from_status_id' => $this->draftStatus->getKey(),
            'to_status_id' => $this->approvedStatus->getKey(),
            'changed_by_user_id' => $coordinator->getKey(),
            'comments' => 'Primer comentario',
        ]);
        $comment1->update(['created_at' => now()->subMinutes(10)]);

        $comment2 = WorkStatusHistory::create([
            'work_of_extension_id' => $work->getKey(),
            'from_status_id' => $this->approvedStatus->getKey(),
            'to_status_id' => $this->approvedStatus->getKey(),
            'changed_by_user_id' => $coordinator->getKey(),
            'comments' => 'Segundo comentario',
        ]);
        $comment2->update(['created_at' => now()->subMinutes(5)]);

        $comment3 = WorkStatusHistory::create([
            'work_of_extension_id' => $work->getKey(),
            'from_status_id' => $this->approvedStatus->getKey(),
            'to_status_id' => $this->approvedStatus->getKey(),
            'changed_by_user_id' => $coordinator->getKey(),
            'comments' => 'Tercer comentario',
        ]);
        $comment3->update(['created_at' => now()->subMinutes(1)]);

        $comments = $work->getCommentsAndFeedback();

        $this->assertCount(3, $comments);

        // Verificar que están ordenados por fecha descendente (más reciente primero)
        // Los comentarios deberían estar en orden: más reciente -> menos reciente
        $this->assertTrue($comments->first()->created_at->greaterThanOrEqualTo($comments->get(1)->created_at));
        $this->assertTrue($comments->get(1)->created_at->greaterThanOrEqualTo($comments->get(2)->created_at));

        // Verificar que todos tienen comentarios
        foreach ($comments as $comment) {
            $this->assertNotEmpty(trim($comment->comments));
        }
    }

    public function test_professor_cannot_view_other_professors_work(): void
    {
        $userA = User::factory()->create();
        $userA->assignRole('profesor');

        $userB = User::factory()->create();
        $userB->assignRole('profesor');

        $work = WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $userA->getKey(),
            'current_status_id' => $this->draftStatus->getKey(),
            'work_type_id' => $this->defaultWorkType->getKey(),
        ]);

        $response = $this->actingAs($userB)->get(route('works.show', $work));

        $response->assertStatus(403); // Forbidden
    }
}