<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WorkOfExtension;
use App\Models\WorkStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkDeletionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function profesor_can_delete_own_draft_work()
    {
        // Crear usuario profesor
        $profesor = User::factory()->create();
        $profesor->assignRole('profesor');

        // Crear estado borrador
        $draftStatus = WorkStatus::factory()->create(['name' => 'Borrador']);

        // Crear trabajo en borrador
        $work = WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $profesor->id,
            'current_status_id' => $draftStatus->id,
            'is_draft' => true
        ]);

        // Ejecutar eliminación
        $response = $this->actingAs($profesor)
            ->delete(route('works.destroy', $work));

        // Verificar redirección y mensaje
        $response->assertRedirect(route('works.index'))
            ->assertSessionHas('success');

        // Verificar que el trabajo fue eliminado
        $this->assertDatabaseMissing('work_of_extensions', ['id' => $work->id]);
    }

    /** @test */
    public function profesor_cannot_delete_work_not_in_draft()
    {
        // Crear usuario profesor
        $profesor = User::factory()->create();
        $profesor->assignRole('profesor');

        // Crear estado enviado
        $submittedStatus = WorkStatus::factory()->create(['name' => 'Enviado']);

        // Crear trabajo enviado
        $work = WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $profesor->id,
            'current_status_id' => $submittedStatus->id,
            'is_draft' => false
        ]);

        // Intentar eliminar
        $response = $this->actingAs($profesor)
            ->delete(route('works.destroy', $work));

        // Verificar que no se permite
        $response->assertRedirect(route('works.show', $work))
            ->assertSessionHas('error', 'Solo se pueden eliminar trabajos en estado borrador.');

        // Verificar que el trabajo sigue existiendo
        $this->assertDatabaseHas('work_of_extensions', ['id' => $work->id]);
    }

    /** @test */
    public function profesor_cannot_delete_others_work()
    {
        // Crear dos profesores
        $profesor1 = User::factory()->create();
        $profesor1->assignRole('profesor');

        $profesor2 = User::factory()->create();
        $profesor2->assignRole('profesor');

        // Crear estado borrador
        $draftStatus = WorkStatus::factory()->create(['name' => 'Borrador']);

        // Crear trabajo de profesor1
        $work = WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $profesor1->id,
            'current_status_id' => $draftStatus->id,
            'is_draft' => true
        ]);

        // Intentar que profesor2 elimine el trabajo de profesor1
        $response = $this->actingAs($profesor2)
            ->delete(route('works.destroy', $work));

        // Verificar que se deniega el acceso
        $response->assertForbidden();

        // Verificar que el trabajo sigue existiendo
        $this->assertDatabaseHas('work_of_extensions', ['id' => $work->id]);
    }
}