<?php

namespace Database\Seeders;

use App\Models\Gym;
use App\Models\User;
use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ShieldSeeder::class);

        $user = User::updateOrCreate(
            ['email' => 'super.admin@test.com'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'password' => Hash::make(env('SUPER_ADMIN_PASSWORD', 'Password/123')),
                'is_active' => true,
            ]
        );

        $user->assignRole('super_admin');

        $this->command->info("Super Admin creado: {$user->email}");

        $owner = User::factory()->create([
            'name' => 'Mario Cuiza',
            'username' => 'mariocuiza',
            'email' => 'cuizagc@gmail.com',
        ])->assignRole('owner');

        $gym = Gym::factory()->create([
            'name' => 'Marios Gym',
            'user_id' => $owner->id,
        ]);

        $owner->update(['gym_id' => $gym->id]);

        Client::factory()->count(1000)->create(['gym_id' => $gym->id]);
    }
}
