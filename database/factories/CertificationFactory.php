<?php

namespace Database\Factories;

use App\Models\Certification;
use App\Models\WorkOfExtension;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Certification>
 */
class CertificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Certification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'work_of_extension_id' => WorkOfExtension::factory(),
            'certification_number' => 'VIEX-' . date('Y') . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'issue_date' => $this->faker->date(),
            'valid_until' => $this->faker->dateTimeBetween('+1 year', '+5 years'),
            'issued_by_user_id' => null, // Se puede asignar después
            'comments' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the certification is issued by a specific user.
     */
    public function issuedBy($userId)
    {
        return $this->state(fn (array $attributes) => [
            'issued_by_user_id' => $userId,
        ]);
    }

    /**
     * Indicate that the certification is expired.
     */
    public function expired()
    {
        return $this->state(fn (array $attributes) => [
            'valid_until' => $this->faker->dateTimeBetween('-5 years', '-1 year'),
        ]);
    }
}