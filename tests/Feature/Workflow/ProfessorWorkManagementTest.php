b<?php

declare(strict_types=1);

namespace Tests\Feature\Workflow;

use App\Models\User;
use App\Models\WorkOfExtension;
use App\Models\WorkStatus;
use App\Models\WorkType;
use Tests\TestCase;
use Tests\TestsWithSeeders;

class ProfessorWorkManagementTest extends TestCase
{
    use TestsWithSeeders;

    public function test_professor_can_create_work(): void
    {
        $professor = User::factory()->create([
            'cedula' => '8-999-7000',
            'is_active' => true,
        ]);
        $professor->assignRole('profesor');

        $workType = WorkType::first();

        $response = $this->actingAs($professor)->post(route('works.store'), [
            'title' => 'Nuevo trabajo de extensión',
            'work_type_id' => $workType->id,
            'organizational_unit_id' => 100,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonths(6)->format('Y-m-d'),
            'description' => 'Descripción del trabajo',
            'academic_period' => '2025-1',
            'publication_consent' => true,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('work_of_extensions', [
            'title' => 'Nuevo trabajo de extensión',
            'primary_responsible_user_id' => $professor->id,
            'is_draft' => true,
        ]);
    }

    public function test_professor_can_submit_work_for_review(): void
    {
        $professor = User::factory()->create([
            'cedula' => '8-999-7000',
            'is_active' => true,
        ]);
        $professor->assignRole('profesor');

        $work = WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $professor->id,
            'current_status_id' => WorkStatus::where('name', 'Borrador')->first()->id,
            'is_draft' => true,
        ]);

        $response = $this->actingAs($professor)->post(route('works.submit', $work));

        $response->assertRedirect(route('works.show', $work));
        $response->assertSessionHas('success');

        $work->refresh();
        $this->assertFalse($work->is_draft);
        $this->assertEquals('En Coordinador Extensión', $work->currentStatus->name);
    }

    public function test_professor_can_update_draft_work(): void
    {
        $professor = User::factory()->create([
            'cedula' => '8-999-7000',
            'is_active' => true,
        ]);
        $professor->assignRole('profesor');

        $work = WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $professor->id,
            'current_status_id' => WorkStatus::where('name', 'Borrador')->first()->id,
            'is_draft' => true,
            'title' => 'Título original',
        ]);

        $response = $this->actingAs($professor)->put(route('works.update', $work), [
            'title' => 'Título actualizado',
            'work_type_id' => $work->work_type_id,
            'organizational_unit_id' => $work->organizational_unit_id,
            'start_date' => $work->start_date->format('Y-m-d'),
            'end_date' => $work->end_date->format('Y-m-d'),
            'description' => 'Descripción actualizada',
            'academic_period' => $work->academic_period,
            'publication_consent' => $work->publication_consent,
        ]);

        $response->assertRedirect(route('works.show', $work));
        $response->assertSessionHas('success');

        $work->refresh();
        $this->assertEquals('Título actualizado', $work->title);
        $this->assertEquals('Descripción actualizada', $work->description);
    }

    public function test_professor_can_resubmit_work_after_changes(): void
    {
        $professor = User::factory()->create([
            'cedula' => '8-999-7000',
            'is_active' => true,
        ]);
        $professor->assignRole('profesor');

        $work = WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $professor->id,
            'current_status_id' => WorkStatus::where('name', 'En Corrección')->first()->id,
            'is_draft' => false,
        ]);

        $response = $this->actingAs($professor)->post(route('works.resubmit', $work), [
            'comments' => 'Trabajo corregido y listo para revisión',
        ]);

        $response->assertRedirect(route('works.show', $work));
        $response->assertSessionHas('success');

        $work->refresh();
        $this->assertEquals('En Coordinador Extensión', $work->currentStatus->name);
    }

    public function test_professor_cannot_modify_submitted_work(): void
    {
        $professor = User::factory()->create([
            'cedula' => '8-999-7000',
            'is_active' => true,
        ]);
        $professor->assignRole('profesor');

        $work = WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $professor->id,
            'current_status_id' => WorkStatus::where('name', 'En Coordinador Extensión')->first()->id,
            'is_draft' => false,
        ]);

        $response = $this->actingAs($professor)->put(route('works.update', $work), [
            'title' => 'Intento de modificación no autorizada',
            'work_type_id' => $work->work_type_id,
            'organizational_unit_id' => $work->organizational_unit_id,
            'start_date' => $work->start_date->format('Y-m-d'),
            'end_date' => $work->end_date->format('Y-m-d'),
            'description' => $work->description,
            'academic_period' => $work->academic_period,
            'publication_consent' => $work->publication_consent,
        ]);

        $response->assertForbidden();
    }
}