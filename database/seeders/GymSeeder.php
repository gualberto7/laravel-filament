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
        $user = User::where('email', 'admin@test.com')->first();

        $gym = Gym::factory()->create([
            'name' => 'Gym 1',
            'address' => '123 Main St',
            'phone' => '1234567890',
            'email' => 'gym1@example.com',
            'user_id' => $user->id
        ]);

        $user2 = User::where('email', 'admin1@test.com')->first();
        $user3 = User::where('email', 'trainer@test.com')->first();

        $gym->staff()->attach($user2->id, ['role' => 'admin']);
        $gym->staff()->attach($user3->id, ['role' => 'trainer']);

        $owner = User::where('email', 'owner@test.com')->first();

        $gym2 = Gym::factory()->create([
            'name' => 'Gym 2',
            'address' => '123 Main St',
            'phone' => '1234567890',
            'email' => 'gym2@example.com',
            'user_id' => $owner->id
        ]);

        $user4 = User::where('email', 'trainer1@test.com')->first();

        $gym2->staff()->attach($user4->id, ['role' => 'trainer']);
    }
}
