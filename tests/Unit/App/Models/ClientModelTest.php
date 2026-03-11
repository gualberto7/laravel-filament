<?php

use App\Models\Client;
use App\Models\Gym;
use App\Models\Membership;
use App\Models\Subscription;
use App\Models\User;

beforeEach(function () {
    $this->gym = Gym::factory()->create();
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->user->setPreference('current_gym', $this->gym->id);
    $this->membership = Membership::factory()->create(['gym_id' => $this->gym->id]);
});

describe('Client active scope', function () {
    it('returns clients with a non-expired subscription', function () {
        $client = Client::factory()->create(['gym_id' => $this->gym->id]);
        $subscription = Subscription::factory()->create([
            'gym_id' => $this->gym->id,
            'membership_id' => $this->membership->id,
            'end_date' => now()->addDays(10),
        ]);
        $subscription->clients()->attach($client);

        expect(Client::active()->get()->pluck('id'))->toContain($client->id);
    });

    it('excludes clients with only expired subscriptions', function () {
        $client = Client::factory()->create(['gym_id' => $this->gym->id]);
        $subscription = Subscription::factory()->create([
            'gym_id' => $this->gym->id,
            'membership_id' => $this->membership->id,
            'end_date' => now()->subDay(),
        ]);
        $subscription->clients()->attach($client);

        expect(Client::active()->get()->pluck('id'))->not->toContain($client->id);
    });

    it('excludes clients with no subscriptions', function () {
        $client = Client::factory()->create(['gym_id' => $this->gym->id]);

        expect(Client::active()->get()->pluck('id'))->not->toContain($client->id);
    });
});

describe('Client inactive scope', function () {
    it('returns clients with no subscriptions', function () {
        $client = Client::factory()->create(['gym_id' => $this->gym->id]);

        expect(Client::inactive()->get()->pluck('id'))->toContain($client->id);
    });

    it('returns clients with only expired subscriptions', function () {
        $client = Client::factory()->create(['gym_id' => $this->gym->id]);
        $subscription = Subscription::factory()->create([
            'gym_id' => $this->gym->id,
            'membership_id' => $this->membership->id,
            'end_date' => now()->subDay(),
        ]);
        $subscription->clients()->attach($client);

        expect(Client::inactive()->get()->pluck('id'))->toContain($client->id);
    });

    it('excludes clients with active subscriptions', function () {
        $client = Client::factory()->create(['gym_id' => $this->gym->id]);
        $subscription = Subscription::factory()->create([
            'gym_id' => $this->gym->id,
            'membership_id' => $this->membership->id,
            'end_date' => now()->addDays(10),
        ]);
        $subscription->clients()->attach($client);

        expect(Client::inactive()->get()->pluck('id'))->not->toContain($client->id);
    });
});
