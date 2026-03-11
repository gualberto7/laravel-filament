<?php

use App\Models\CheckIn;
use App\Models\Client;
use App\Models\Membership;
use App\Models\Subscription;

beforeEach(function () {
    ['user' => $this->user, 'gym' => $this->gym] = loginAs('admin');

    $this->client = Client::factory()->create(['gym_id' => $this->gym->id]);
});

describe('Client::addCheckIn() validation', function () {
    test('throws exception when client has no active subscription', function () {
        expect(fn () => $this->client->addCheckIn())
            ->toThrow(\RuntimeException::class, 'El cliente no tiene una suscripción activa.');
    });

    test('throws exception when subscription is expired', function () {
        $membership = Membership::factory()->create(['gym_id' => $this->gym->id, 'max_checkins' => null]);

        $subscription = Subscription::factory()->create([
            'membership_id' => $membership->id,
            'gym_id' => $this->gym->id,
            'start_date' => now()->subDays(30),
            'end_date' => now()->subDay(),
        ]);
        $subscription->clients()->attach($this->client);

        expect(fn () => $this->client->addCheckIn())
            ->toThrow(\RuntimeException::class, 'El cliente no tiene una suscripción activa.');
    });

    test('allows check-in when membership has no max_checkins limit', function () {
        $membership = Membership::factory()->create(['gym_id' => $this->gym->id, 'max_checkins' => null]);

        $subscription = Subscription::factory()->create([
            'membership_id' => $membership->id,
            'gym_id' => $this->gym->id,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(30),
        ]);
        $subscription->clients()->attach($this->client);

        $checkIn = $this->client->addCheckIn();

        expect($checkIn)->toBeInstanceOf(CheckIn::class);
        expect(CheckIn::where('client_id', $this->client->id)->count())->toBe(1);
    });

    test('allows check-in when client has remaining check-ins in the period', function () {
        $membership = Membership::factory()->create(['gym_id' => $this->gym->id, 'max_checkins' => 15]);

        $subscription = Subscription::factory()->create([
            'membership_id' => $membership->id,
            'gym_id' => $this->gym->id,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(30),
        ]);
        $subscription->clients()->attach($this->client);

        CheckIn::factory()->count(14)->create([
            'client_id' => $this->client->id,
            'gym_id' => $this->gym->id,
            'subscription_id' => $subscription->id,
        ]);

        $checkIn = $this->client->addCheckIn();

        expect($checkIn)->toBeInstanceOf(CheckIn::class);
        expect(CheckIn::where('client_id', $this->client->id)->count())->toBe(15);
    });

    test('blocks check-in when client has exhausted max_checkins for the period', function () {
        $membership = Membership::factory()->create(['gym_id' => $this->gym->id, 'max_checkins' => 15]);

        $subscription = Subscription::factory()->create([
            'membership_id' => $membership->id,
            'gym_id' => $this->gym->id,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(30),
        ]);
        $subscription->clients()->attach($this->client);

        CheckIn::factory()->count(15)->create([
            'client_id' => $this->client->id,
            'gym_id' => $this->gym->id,
            'subscription_id' => $subscription->id,
        ]);

        expect(fn () => $this->client->addCheckIn())
            ->toThrow(\RuntimeException::class, 'El cliente ha agotado sus 15 ingresos disponibles para este período.');
    });

    test('check-ins from a different subscription do not count toward the limit', function () {
        $membership = Membership::factory()->create(['gym_id' => $this->gym->id, 'max_checkins' => 5]);

        $oldSubscription = Subscription::factory()->create([
            'membership_id' => $membership->id,
            'gym_id' => $this->gym->id,
            'start_date' => now()->subMonths(2),
            'end_date' => now()->subMonth(),
        ]);
        $oldSubscription->clients()->attach($this->client);

        $newSubscription = Subscription::factory()->create([
            'membership_id' => $membership->id,
            'gym_id' => $this->gym->id,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(30),
        ]);
        $newSubscription->clients()->attach($this->client);

        CheckIn::factory()->count(5)->create([
            'client_id' => $this->client->id,
            'gym_id' => $this->gym->id,
            'subscription_id' => $oldSubscription->id,
        ]);

        $checkIn = $this->client->addCheckIn();

        expect($checkIn)->toBeInstanceOf(CheckIn::class);
    });
});
