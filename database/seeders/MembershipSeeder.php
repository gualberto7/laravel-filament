<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Membership;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Membership::factory()->create([
            'name' => 'Mensual',
            'price' => 200,
            'duration' => 30,
            'gym_id' => 1
        ]);

        Membership::factory()->create([
            'name' => 'Anual',
            'price' => 2000,
            'duration' => 365,
            'gym_id' => 1
        ]);
    }
}
