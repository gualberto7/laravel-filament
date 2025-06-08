<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com'
        ]);

        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin1@test.com'
        ]);

        User::factory()->create([
            'name' => 'Trainer',
            'email' => 'trainer@test.com'
        ]);

        User::factory()->create([
            'name' => 'Owner',
            'email' => 'owner@test.com'
        ]);

        User::factory()->create([
            'name' => 'Trainer 1',
            'email' => 'trainer1@test.com'
        ]);
    }
}
