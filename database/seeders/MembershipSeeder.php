<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Membership;
use App\Models\Gym;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gym = Gym::where('email', 'gym1@example.com')->first();

        Membership::factory()->create([
            'name' => 'Mensual',
            'price' => 200,
            'duration' => 30,
            'gym_id' => $gym->id
        ]);

        Membership::factory()->create([
            'name' => 'Anual',
            'price' => 2000,
            'duration' => 365,
            'gym_id' => $gym->id
        ]);
    }
}
