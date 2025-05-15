<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gym;

class GymSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Gym::factory()->create([
            'name' => 'Gym 1',
            'address' => '123 Main St',
            'phone' => '1234567890',
            'email' => 'gym1@example.com',
            'user_id' => 1
        ]);
    }
}
