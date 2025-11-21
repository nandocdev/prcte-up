<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WorkOfExtension;
use App\Models\WorkStatus;
use App\Models\WorkType;
use App\Models\OrganizationalUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkSubmissionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function profesor_can_submit_complete_draft_work()
    {
        // Crear datos necesarios
        $profesor = User::factory()->create();
        $profesor->assignRole('profesor');

        $workType = WorkType::factory()->create(['id' => 1, 'name' => 'Proyecto']);
        $organizationalUnit = OrganizationalUnit::factory()->create();
        $draftStatus = WorkStatus::factory()->create(['name' => 'Borrador']);
        $submittedStatus = WorkStatus::factory()->create(['name' => 'Enviado a Coordinador']);

        // Crear trabajo completo en borrador
        $work = WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $profesor->id,
            'current_status_id' => $draftStatus->id,
            'is_draft' => true,
            'work_type_id' => $workType->id,
            'organizational_unit_id' => $organizationalUnit->id,
            'title' => 'Trabajo de prueba completo',
            'description' => 'Descripción completa del trabajo',
            'start_date' => now()->subDays(10),
            'end_date' => now()->addDays(10),
            'academic_period' => '2024-I',
        ]);

        // Crear detalle específico (proyecto)
        $work->projectDetail()->create([
            'objectives' => 'Objetivos del proyecto',
            'methodology' => 'Metodología del proyecto',
        ]);

        // Ejecutar envío
        $response = $this->actingAs($profesor)
            ->post(route('works.submit', $work));

        // Verificar respuesta
        $response->assertRedirect(route('works.show', $work))
            ->assertSessionHas('success');

        // Verificar cambios en BD
        $work->refresh();
        $this->assertFalse($work->is_draft);
        $this->assertNotNull($work->submitted_at);
        $this->assertEquals($submittedStatus->id, $work->current_status_id);

        // Verificar historial
        $this->assertDatabaseHas('work_status_history', [
            'work_of_extension_id' => $work->id,
            'from_status_id' => $draftStatus->id,
            'to_status_id' => $submittedStatus->id,
            'changed_by_user_id' => $profesor->id,
        ]);
    }

    /** @test */
    public function profesor_cannot_submit_incomplete_work()
    {
        // Crear profesor
        $profesor = User::factory()->create();
        $profesor->assignRole('profesor');

        $draftStatus = WorkStatus::factory()->create(['name' => 'Borrador']);

        // Crear trabajo incompleto (sin descripción)
        $work = WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $profesor->id,
            'current_status_id' => $draftStatus->id,
            'is_draft' => true,
            'description' => '', // Campo faltante
        ]);

        // Intentar enviar
        $response = $this->actingAs($profesor)
            ->post(route('works.submit', $work));

        // Verificar que falla
        $response->assertRedirect(route('works.show', $work))
            ->assertSessionHas('error');

        // Verificar que sigue en borrador
        $work->refresh();
        $this->assertTrue($work->is_draft);
        $this->assertNull($work->submitted_at);
    }

    /** @test */
    public function profesor_cannot_submit_already_submitted_work()
    {
        // Crear profesor
        $profesor = User::factory()->create();
        $profesor->assignRole('profesor');

        $submittedStatus = WorkStatus::factory()->create(['name' => 'Enviado a Coordinador']);

        // Crear trabajo ya enviado
        $work = WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $profesor->id,
            'current_status_id' => $submittedStatus->id,
            'is_draft' => false,
            'submitted_at' => now(),
        ]);

        // Intentar enviar nuevamente
        $response = $this->actingAs($profesor)
            ->post(route('works.submit', $work));

        // Verificar que falla
        $response->assertRedirect(route('works.show', $work))
            ->assertSessionHas('error');

        // Verificar que sigue enviado
        $work->refresh();
        $this->assertFalse($work->is_draft);
    }

    /** @test */
    public function profesor_cannot_submit_others_work()
    {
        // Crear dos profesores
        $profesor1 = User::factory()->create();
        $profesor1->assignRole('profesor');

        $profesor2 = User::factory()->create();
        $profesor2->assignRole('profesor');

        $draftStatus = WorkStatus::factory()->create(['name' => 'Borrador']);

        // Crear trabajo de profesor1
        $work = WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $profesor1->id,
            'current_status_id' => $draftStatus->id,
            'is_draft' => true,
        ]);

        // Intentar que profesor2 envíe el trabajo de profesor1
        $response = $this->actingAs($profesor2)
            ->post(route('works.submit', $work));

        // Verificar que se deniega
        $response->assertForbidden();

        // Verificar que sigue en borrador
        $work->refresh();
        $this->assertTrue($work->is_draft);
    }
}