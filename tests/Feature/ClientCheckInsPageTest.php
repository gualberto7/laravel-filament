<?php

use App\Filament\Resources\Clients\Pages\ClientCheckIns;
use App\Models\CheckIn;
use App\Models\Client;
use Livewire\Livewire;

beforeEach(function () {
    ['user' => $this->user, 'gym' => $this->gym] = loginAs('admin');

    $this->client = Client::factory()->create(['gym_id' => $this->gym->id]);
});

describe('ClientCheckIns page', function () {
    test('page loads successfully', function () {
        Livewire::test(ClientCheckIns::class, ['record' => $this->client->id])
            ->assertOk();
    });

    test('page title includes the client name', function () {
        Livewire::test(ClientCheckIns::class, ['record' => $this->client->id])
            ->assertSee('Check-ins de '.$this->client->name);
    });

    test('shows check-ins belonging to the client', function () {
        $checkIns = CheckIn::factory()->count(3)->create([
            'gym_id' => $this->gym->id,
            'client_id' => $this->client->id,
        ]);

        Livewire::test(ClientCheckIns::class, ['record' => $this->client->id])
            ->assertCanSeeTableRecords($checkIns);
    });

    test('does not show check-ins from other clients', function () {
        $otherClient = Client::factory()->create(['gym_id' => $this->gym->id]);

        $clientCheckIn = CheckIn::factory()->create([
            'gym_id' => $this->gym->id,
            'client_id' => $this->client->id,
        ]);

        $otherCheckIn = CheckIn::factory()->create([
            'gym_id' => $this->gym->id,
            'client_id' => $otherClient->id,
        ]);

        Livewire::test(ClientCheckIns::class, ['record' => $this->client->id])
            ->assertCanSeeTableRecords([$clientCheckIn])
            ->assertCanNotSeeTableRecords([$otherCheckIn]);
    });

    test('filters check-ins by date range', function () {
        $oldCheckIn = CheckIn::factory()->create([
            'gym_id' => $this->gym->id,
            'client_id' => $this->client->id,
            'created_at' => now()->subYear(),
        ]);

        $recentCheckIn = CheckIn::factory()->create([
            'gym_id' => $this->gym->id,
            'client_id' => $this->client->id,
            'created_at' => now(),
        ]);

        Livewire::test(ClientCheckIns::class, ['record' => $this->client->id])
            ->filterTable('created_at', [
                'created_from' => now()->subWeek()->toDateString(),
                'created_until' => now()->toDateString(),
            ])
            ->assertCanSeeTableRecords([$recentCheckIn])
            ->assertCanNotSeeTableRecords([$oldCheckIn]);
    });

    test('shows empty table when client has no check-ins', function () {
        Livewire::test(ClientCheckIns::class, ['record' => $this->client->id])
            ->assertOk()
            ->assertCountTableRecords(0);
    });
});
