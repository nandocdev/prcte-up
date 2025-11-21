<?php

namespace Database\Factories;

use App\Models\OrganizationalUnit;
use App\Models\User;
use App\Models\WorkOfExtension;
use App\Models\WorkStatus;
use App\Models\WorkType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkOfExtension>
 */
class WorkOfExtensionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'work_type_id' => WorkType::inRandomOrder()->first()?->id ?? 1,
            'primary_responsible_user_id' => User::factory(),
            'organizational_unit_id' => OrganizationalUnit::first()?->id ?? OrganizationalUnit::factory()->create()->id,
            'current_status_id' => WorkStatus::where('name', 'Borrador')->first()?->id ?? 1,
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'description' => fake()->paragraph(),
            'academic_period' => fake()->randomElement(['2025-1', '2025-2', '2026-1']),
            'publication_consent' => fake()->boolean(),
            'is_draft' => true,
            'submitted_at' => null,
        ];
    }

    /**
     * Indicate that the work is submitted.
     */
    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_draft' => false,
            'submitted_at' => now(),
            'current_status_id' => WorkStatus::where('name', 'En Coordinador Extensión')->first()?->id ?? $attributes['current_status_id'],
        ]);
    }

    /**
     * Indicate that the work is certified.
     */
    public function certified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_draft' => false,
            'submitted_at' => now(),
            'current_status_id' => WorkStatus::where('name', 'Certificado')->first()?->id ?? $attributes['current_status_id'],
        ]);
    }

    /**
     * Set specific work type.
     */
    public function withWorkType(WorkType $workType): static
    {
        return $this->state(fn (array $attributes) => [
            'work_type_id' => $workType->id,
        ]);
    }

    /**
     * Set specific organizational unit.
     */
    public function withOrganizationalUnit(OrganizationalUnit $unit): static
    {
        return $this->state(fn (array $attributes) => [
            'organizational_unit_id' => $unit->id,
        ]);
    }

    /**
     * Set specific status.
     */
    public function withStatus(WorkStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'current_status_id' => $status->id,
        ]);
    }
}