<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Gym;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Membership>
 */
class MembershipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'price' => fake()->randomFloat(2, 10, 100),
            'duration' => fake()->numberBetween(30, 365),
            'max_installments' => fake()->numberBetween(1, 12),
            'max_checkins' => fake()->numberBetween(1, 1000),
            'created_by' => 'Seeder',
            'updated_by' => 'Seeder',
            'gym_id' => Gym::factory(),
        ];
    }
}
