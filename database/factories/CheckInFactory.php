<?php

namespace Database\Factories;

use App\Models\Gym;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CheckIn>
 */
class CheckInFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gym = Gym::factory()->create();
        $client = Client::factory()->create(['gym_id' => $gym->id]);

        return [
            'locker_number' => $this->faker->word(),
            'gym_id' => $gym->id,
            'client_id' => $client->id,
            'created_by' => 'Seeder',
            'updated_by' => 'Seeder',
        ];
    }
}
