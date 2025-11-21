<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WorkOfExtension;
use App\Models\WorkStatus;
use App\Models\WorkStatusHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkCommentsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que el profesor puede ver comentarios en la vista de detalle
     */
    public function test_professor_can_view_comments_in_work_detail(): void
    {
        $professor = User::factory()->create();
        $work = WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $professor->getKey(),
        ]);

        // Crear algunos comentarios en el historial
        $status = WorkStatus::factory()->create(['name' => 'Rechazado por Coordinador']);
        WorkStatusHistory::factory()->create([
            'work_of_extension_id' => $work->getKey(),
            'to_status_id' => $status->getKey(),
            'comments' => 'Este trabajo necesita correcciones en la metodología.',
            'changed_by_user_id' => User::factory()->create()->getKey(),
        ]);

        $response = $this->actingAs($professor)
            ->get(route('works.show', $work));

        $response->assertStatus(200);
        $response->assertSee('Comentarios y Retroalimentación');
        $response->assertSee('Este trabajo necesita correcciones en la metodología.');
    }

    /**
     * Test que el método getCommentsAndFeedback filtra correctamente
     */
    public function test_get_comments_and_feedback_filters_correctly(): void
    {
        $work = WorkOfExtension::factory()->create();

        // Crear comentario con texto
        $status1 = WorkStatus::factory()->create();
        WorkStatusHistory::factory()->create([
            'work_of_extension_id' => $work->getKey(),
            'to_status_id' => $status1->getKey(),
            'comments' => 'Comentario válido',
            'changed_by_user_id' => User::factory()->create()->getKey(),
        ]);

        // Crear entrada sin comentario (debe ser filtrada)
        $status2 = WorkStatus::factory()->create();
        WorkStatusHistory::factory()->create([
            'work_of_extension_id' => $work->getKey(),
            'to_status_id' => $status2->getKey(),
            'comments' => null,
            'changed_by_user_id' => User::factory()->create()->getKey(),
        ]);

        // Crear comentario vacío (debe ser filtrado)
        $status3 = WorkStatus::factory()->create();
        WorkStatusHistory::factory()->create([
            'work_of_extension_id' => $work->getKey(),
            'to_status_id' => $status3->getKey(),
            'comments' => '',
            'changed_by_user_id' => User::factory()->create()->getKey(),
        ]);

        $comments = $work->getCommentsAndFeedback();

        $this->assertCount(1, $comments);
        $this->assertEquals('Comentario válido', $comments->first()->comments);
    }

    /**
     * Test que getLastRejectionComment retorna el último comentario de rechazo
     */
    public function test_get_last_rejection_comment_returns_most_recent(): void
    {
        $work = WorkOfExtension::factory()->create();

        // Crear primer comentario de rechazo (más antiguo)
        $status1 = WorkStatus::factory()->create(['name' => 'Rechazado por Coordinador']);
        WorkStatusHistory::factory()->create([
            'work_of_extension_id' => $work->getKey(),
            'to_status_id' => $status1->getKey(),
            'comments' => 'Primer comentario de rechazo',
            'changed_by_user_id' => User::factory()->create()->getKey(),
            'created_at' => now()->subDays(2),
        ]);

        // Crear segundo comentario de rechazo (más reciente)
        $status2 = WorkStatus::factory()->create(['name' => 'Rechazado por VIEX']);
        WorkStatusHistory::factory()->create([
            'work_of_extension_id' => $work->getKey(),
            'to_status_id' => $status2->getKey(),
            'comments' => 'Último comentario de rechazo',
            'changed_by_user_id' => User::factory()->create()->getKey(),
            'created_at' => now()->subDay(),
        ]);

        $lastComment = $work->getLastRejectionComment();

        $this->assertNotNull($lastComment);
        $this->assertEquals('Último comentario de rechazo', $lastComment->comments);
        $this->assertEquals('Rechazado por VIEX', $lastComment->status->name);
    }

    /**
     * Test que solo el propietario puede ver los comentarios
     */
    public function test_only_owner_can_view_work_comments(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $work = WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $owner->getKey(),
        ]);

        // Crear comentario
        $status = WorkStatus::factory()->create();
        WorkStatusHistory::factory()->create([
            'work_of_extension_id' => $work->getKey(),
            'to_status_id' => $status->getKey(),
            'comments' => 'Comentario privado',
            'changed_by_user_id' => User::factory()->create()->getKey(),
        ]);

        // Propietario puede ver
        $response = $this->actingAs($owner)
            ->get(route('works.show', $work));
        $response->assertStatus(200);
        $response->assertSee('Comentario privado');

        // Otro usuario no puede ver
        $response = $this->actingAs($otherUser)
            ->get(route('works.show', $work));
        $response->assertStatus(403);
    }
}
