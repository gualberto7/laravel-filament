<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gym;
use App\Models\Membership;
use App\Models\Client;
use App\Models\Subscription;
use Carbon\Carbon;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gym = Gym::first();
        $memberships = Membership::all();
        $clients = Client::factory()->count(30)->create();

        foreach ($clients as $client) {
            $membership = $memberships->random();
            $startDate = Carbon::now()->subDays(rand(0, 30));
            $endDate = $startDate->copy()->addDays(rand(30, 365));

            $subscription = Subscription::create([
                'membership_id' => $membership->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'created_by' => 'Seeder',
                'updated_by' => 'Seeder',
                'price' => $membership->price,
                'gym_id' => $gym->id,
            ]);

            $subscription->clients()->attach($client);
        }
        
    }
}
