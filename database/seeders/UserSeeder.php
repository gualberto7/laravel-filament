<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Gym;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gym = Gym::where('email', 'gym1@example.com')->first();

        User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@test.com',
            'gym_id' => $gym->id
        ]);

        User::factory()->create([
            'name' => 'Trainer',
            'username' => 'trainer',
            'email' => 'trainer@test.com',
            'gym_id' => $gym->id
        ]);

        User::factory()->create([
            'name' => 'Instructor',
            'username' => 'instructor',
            'email' => 'instructor@test.com',
            'gym_id' => $gym->id
        ]);

        $gym2 = Gym::where('email', 'gym2@example.com')->first();

        User::factory()->create([
            'name' => 'Admin 2',
            'username' => 'admin2',
            'email' => 'admin2@test.com',
            'gym_id' => $gym2->id
        ]);

        User::factory()->create([
            'name' => 'Trainer 2',
            'username' => 'trainer2',
            'email' => 'trainer2@test.com',
            'gym_id' => $gym2->id
        ]);

        User::factory()->create([
            'name' => 'Instructor 2',
            'username' => 'instructor2',
            'email' => 'instructor2@test.com',
            'gym_id' => $gym2->id
        ]);
    }
}
