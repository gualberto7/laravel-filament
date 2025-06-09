<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gym;
use App\Models\User;

class GymSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Owner',
            'username' => 'owner',
            'email' => 'owner@test.com'
        ]);

        $gym = Gym::factory()->create([
            'name' => 'Gym 1',
            'address' => '123 Main St',
            'phone' => '1234567890',
            'email' => 'gym1@example.com',
            'user_id' => $user->id
        ]);

        $user2 = User::factory()->create([
            'name' => 'Owner 2',
            'username' => 'owner2',
            'email' => 'owner2@test.com'
        ]);

        $gym2 = Gym::factory()->create([
            'name' => 'Gym 2',
            'address' => '123 Main St',
            'phone' => '1234567890',
            'email' => 'gym2@example.com',
            'user_id' => $user2->id
        ]);
    }
}
