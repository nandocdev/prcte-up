<?php

namespace Database\Factories;

use App\Models\OrganizationalUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrganizationalUnit>
 */
class OrganizationalUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'type' => fake()->randomElement(['Faculty', 'Department', 'Regional Center', 'Main Campus']),
            'parent_id' => null,
        ];
    }

    /**
     * Indicate that the unit is a faculty.
     */
    public function faculty(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Faculty',
        ]);
    }

    /**
     * Indicate that the unit is a department.
     */
    public function department(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Department',
        ]);
    }

    /**
     * Indicate that the unit has a parent.
     */
    public function withParent(?OrganizationalUnit $parent = null): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent?->id ?? OrganizationalUnit::factory()->create()->id,
        ]);
    }
}