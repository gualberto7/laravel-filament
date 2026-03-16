<?php

use App\Enums\SubscriptionStatus;
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

describe('Subscription thisMonth scope', function () {
    it('returns subscriptions created this month', function () {
        $subscription = Subscription::factory()->create([
            'gym_id' => $this->gym->id,
            'membership_id' => $this->membership->id,
            'created_at' => now(),
        ]);

        expect(Subscription::thisMonth()->get()->pluck('id'))->toContain($subscription->id);
    });

    it('excludes subscriptions from last month', function () {
        $subscription = Subscription::factory()->create([
            'gym_id' => $this->gym->id,
            'membership_id' => $this->membership->id,
            'created_at' => now()->subMonth(),
        ]);

        expect(Subscription::thisMonth()->get()->pluck('id'))->not->toContain($subscription->id);
    });

    it('excludes subscriptions from last year same month', function () {
        $subscription = Subscription::factory()->create([
            'gym_id' => $this->gym->id,
            'membership_id' => $this->membership->id,
            'created_at' => now()->subYear(),
        ]);

        expect(Subscription::thisMonth()->get()->pluck('id'))->not->toContain($subscription->id);
    });

    it('counts only this months subscriptions', function () {
        Subscription::factory()->count(3)->create([
            'gym_id' => $this->gym->id,
            'membership_id' => $this->membership->id,
            'created_at' => now(),
        ]);

        Subscription::factory()->create([
            'gym_id' => $this->gym->id,
            'membership_id' => $this->membership->id,
            'created_at' => now()->subMonth(),
        ]);

        expect(Subscription::thisMonth()->count())->toBe(3);
    });
});

describe('Subscription status attribute', function () {
    it('returns active for subscriptions ending in more than 3 days', function () {
        $subscription = Subscription::factory()->make(['end_date' => now()->addDays(10)]);

        expect($subscription->status)->toBe(SubscriptionStatus::Active);
    });

    it('returns expires_soon for subscriptions ending in 1 to 3 days', function () {
        $subscription = Subscription::factory()->make(['end_date' => now()->addDays(2)]);

        expect($subscription->status)->toBe(SubscriptionStatus::ExpiresSoon);
    });

    it('returns expires_today for subscriptions ending today', function () {
        $subscription = Subscription::factory()->make(['end_date' => now()]);

        expect($subscription->status)->toBe(SubscriptionStatus::ExpiresToday);
    });

    it('returns expired for subscriptions ending yesterday', function () {
        $subscription = Subscription::factory()->make(['end_date' => now()->subDay()]);

        expect($subscription->status)->toBe(SubscriptionStatus::Expired);
    });
});
