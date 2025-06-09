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
            'name' => 'Administrador',
            'username' => 'admin1',
            'email' => 'admin1@test.com',
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
    }
}
