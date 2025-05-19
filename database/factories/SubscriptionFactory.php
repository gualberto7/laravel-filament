<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'created_by' => 'Seeder',
            'updated_by' => 'Seeder',
            'price' => $this->faker->randomFloat(2, 10, 100),
            'membership_id' => $this->faker->numberBetween(1, 4),
            'gym_id' => $this->faker->numberBetween(1),
        ];
    }
}
