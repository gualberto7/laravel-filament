<?php

namespace Database\Seeders;

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

        $user = User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@test.com',
            'gym_id' => $gym->id,
        ])->assignRole('admin');

        $user2 = User::factory()->create([
            'name' => 'Trainer',
            'username' => 'trainer',
            'email' => 'trainer@test.com',
            'gym_id' => $gym->id,
        ])->assignRole('trainer');

        $gym2 = Gym::where('email', 'gym2@example.com')->first();

        User::factory()->create([
            'name' => 'Admin 2',
            'username' => 'admin2',
            'email' => 'admin2@test.com',
            'gym_id' => $gym2->id,
        ])->assignRole('admin');

        User::factory()->create([
            'name' => 'Trainer 2',
            'username' => 'trainer2',
            'email' => 'trainer2@test.com',
            'gym_id' => $gym2->id,
        ])->assignRole('trainer');

        User::factory()->create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'super.admin@test.com',
        ])->assignRole('super_admin');
    }
}
