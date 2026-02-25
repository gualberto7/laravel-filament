<?php

use App\Filament\Resources\Clients\Pages\ClientSubscriptions;
use App\Models\Client;
use App\Models\Membership;
use App\Models\Subscription;
use Livewire\Livewire;

beforeEach(function () {
    ['user' => $this->user, 'gym' => $this->gym] = loginAs('admin');

    $this->membership = Membership::factory()->create(['gym_id' => $this->gym->id]);
    $this->client = Client::factory()->create(['gym_id' => $this->gym->id]);
});

describe('ClientSubscriptions page', function () {
    test('page loads successfully', function () {
        Livewire::test(ClientSubscriptions::class, ['record' => $this->client->id])
            ->assertOk();
    });

    test('page title includes the client name', function () {
        Livewire::test(ClientSubscriptions::class, ['record' => $this->client->id])
            ->assertSee('Suscripciones de '.$this->client->name);
    });

    test('shows subscriptions belonging to the client', function () {
        $subscriptions = Subscription::factory()->count(3)->create([
            'gym_id' => $this->gym->id,
            'membership_id' => $this->membership->id,
        ]);

        $subscriptions->each(fn (Subscription $s) => $s->clients()->attach($this->client->id));

        Livewire::test(ClientSubscriptions::class, ['record' => $this->client->id])
            ->assertCanSeeTableRecords($subscriptions);
    });

    test('does not show subscriptions from other clients', function () {
        $otherClient = Client::factory()->create(['gym_id' => $this->gym->id]);

        $clientSubscription = Subscription::factory()->create([
            'gym_id' => $this->gym->id,
            'membership_id' => $this->membership->id,
        ]);
        $clientSubscription->clients()->attach($this->client->id);

        $otherSubscription = Subscription::factory()->create([
            'gym_id' => $this->gym->id,
            'membership_id' => $this->membership->id,
        ]);
        $otherSubscription->clients()->attach($otherClient->id);

        Livewire::test(ClientSubscriptions::class, ['record' => $this->client->id])
            ->assertCanSeeTableRecords([$clientSubscription])
            ->assertCanNotSeeTableRecords([$otherSubscription]);
    });

    test('filters subscriptions by date range', function () {
        $oldSubscription = Subscription::factory()->create([
            'gym_id' => $this->gym->id,
            'membership_id' => $this->membership->id,
            'created_at' => now()->subYear(),
            'start_date' => now()->subYear(),
            'end_date' => now()->subYear()->addMonth(),
        ]);
        $oldSubscription->clients()->attach($this->client->id);

        $recentSubscription = Subscription::factory()->create([
            'gym_id' => $this->gym->id,
            'membership_id' => $this->membership->id,
            'created_at' => now(),
            'start_date' => now(),
            'end_date' => now()->addMonth(),
        ]);
        $recentSubscription->clients()->attach($this->client->id);

        Livewire::test(ClientSubscriptions::class, ['record' => $this->client->id])
            ->filterTable('created_at', [
                'created_from' => now()->subWeek()->toDateString(),
                'created_until' => now()->toDateString(),
            ])
            ->assertCanSeeTableRecords([$recentSubscription])
            ->assertCanNotSeeTableRecords([$oldSubscription]);
    });

    test('shows empty table when client has no subscriptions', function () {
        Livewire::test(ClientSubscriptions::class, ['record' => $this->client->id])
            ->assertOk()
            ->assertCountTableRecords(0);
    });
});
