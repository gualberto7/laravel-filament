<?php

use App\Models\Membership;
use App\Models\Gym;
use App\Models\User;

beforeEach(function () {
    $this->gym = Gym::factory()->create();
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    
    $this->user->setPreference('current_gym', $this->gym->id);
});

describe('Membership Active Status', function () {
    it('returns true for active non-promo memberships', function () {
        $membership = Membership::factory()->create([
            'active' => true,
            'is_promo' => false,
            'gym_id' => $this->gym->id,
        ]);

        expect($membership->is_active)->toBeTrue();
    });

    it('returns false for inactive non-promo memberships', function () {
        $membership = Membership::factory()->create([
            'active' => false,
            'is_promo' => false,
            'gym_id' => $this->gym->id,
        ]);

        expect($membership->is_active)->toBeFalse();
    });

    it('returns true for active promo within date range', function () {
        $membership = Membership::factory()->create([
            'active' => true,
            'is_promo' => true,
            'promo_start_date' => now()->subDays(5),
            'promo_end_date' => now()->addDays(5),
            'gym_id' => $this->gym->id,
        ]);

        expect($membership->is_active)->toBeTrue();
    });

    it('returns true for active promo on start date', function () {
        $membership = Membership::factory()->create([
            'active' => true,
            'is_promo' => true,
            'promo_start_date' => now(),
            'promo_end_date' => now()->addDays(5),
            'gym_id' => $this->gym->id,
        ]);

        expect($membership->is_active)->toBeTrue();
    });

    it('returns true for active promo on end date', function () {
        $membership = Membership::factory()->create([
            'active' => true,
            'is_promo' => true,
            'promo_start_date' => now()->subDays(5),
            'promo_end_date' => now(),
            'gym_id' => $this->gym->id,
        ]);

        expect($membership->is_active)->toBeTrue();
    });

    it('returns false for active promo before start date', function () {
        $membership = Membership::factory()->create([
            'active' => true,
            'is_promo' => true,
            'promo_start_date' => now()->addDays(1),
            'promo_end_date' => now()->addDays(5),
            'gym_id' => $this->gym->id,
        ]);

        expect($membership->is_active)->toBeFalse();
    });

    it('returns false for active promo after end date', function () {
        $membership = Membership::factory()->create([
            'active' => true,
            'is_promo' => true,
            'promo_start_date' => now()->subDays(10),
            'promo_end_date' => now()->subDays(1),
            'gym_id' => $this->gym->id,
        ]);

        expect($membership->is_active)->toBeFalse();
    });

    it('returns false for inactive promo even within date range', function () {
        $membership = Membership::factory()->create([
            'active' => false,
            'is_promo' => true,
            'promo_start_date' => now()->subDays(5),
            'promo_end_date' => now()->addDays(5),
            'gym_id' => $this->gym->id,
        ]);

        expect($membership->is_active)->toBeFalse();
    });
});

describe('Membership Scopes', function () {
    it('active scope returns only active memberships', function () {
        $activeMembership = Membership::factory()->create([
            'active' => true,
            'is_promo' => false,
            'gym_id' => $this->gym->id,
        ]);

        $inactiveMembership = Membership::factory()->create([
            'active' => false,
            'is_promo' => false,
            'gym_id' => $this->gym->id,
        ]);

        $activePromo = Membership::factory()->create([
            'active' => true,
            'is_promo' => true,
            'promo_start_date' => now()->subDays(5),
            'promo_end_date' => now()->addDays(5),
            'gym_id' => $this->gym->id,
        ]);

        $expiredPromo = Membership::factory()->create([
            'active' => true,
            'is_promo' => true,
            'promo_start_date' => now()->subDays(10),
            'promo_end_date' => now()->subDays(1),
            'gym_id' => $this->gym->id,
        ]);

        $activeMemberships = Membership::withoutGlobalScope('gym')->active()->where('gym_id', $this->gym->id)->get();

        expect($activeMemberships)->toHaveCount(2);
        expect($activeMemberships->pluck('id'))->toContain($activeMembership->id);
        expect($activeMemberships->pluck('id'))->toContain($activePromo->id);
        expect($activeMemberships->pluck('id'))->not->toContain($inactiveMembership->id);
        expect($activeMemberships->pluck('id'))->not->toContain($expiredPromo->id);
    });

    it('inactive scope returns only inactive memberships', function () {
        $activeMembership = Membership::factory()->create([
            'active' => true,
            'is_promo' => false,
            'gym_id' => $this->gym->id,
        ]);

        $inactiveMembership = Membership::factory()->create([
            'active' => false,
            'is_promo' => false,
            'gym_id' => $this->gym->id,
        ]);

        $expiredPromo = Membership::factory()->create([
            'active' => true,
            'is_promo' => true,
            'promo_start_date' => now()->subDays(10),
            'promo_end_date' => now()->subDays(1),
            'gym_id' => $this->gym->id,
        ]);

        $inactiveMemberships = Membership::withoutGlobalScope('gym')->inactive()->where('gym_id', $this->gym->id)->get();

        expect($inactiveMemberships)->toHaveCount(2);
        expect($inactiveMemberships->pluck('id'))->toContain($inactiveMembership->id);
        expect($inactiveMemberships->pluck('id'))->toContain($expiredPromo->id);
        expect($inactiveMemberships->pluck('id'))->not->toContain($activeMembership->id);
    });
});

describe('Active Promos Query', function () {
    it('includes non-promo memberships and active promos', function () {
        $nonPromo = Membership::factory()->create([
            'is_promo' => false,
            'gym_id' => $this->gym->id,
        ]);

        $activePromo = Membership::factory()->create([
            'is_promo' => true,
            'promo_start_date' => now()->subDays(5),
            'promo_end_date' => now()->addDays(5),
            'gym_id' => $this->gym->id,
        ]);

        $expiredPromo = Membership::factory()->create([
            'is_promo' => true,
            'promo_start_date' => now()->subDays(10),
            'promo_end_date' => now()->subDays(1),
            'gym_id' => $this->gym->id,
        ]);

        $query = Membership::withoutGlobalScope('gym')->where('gym_id', $this->gym->id);
        Membership::getActivePromosQuery($query);
        $results = $query->get();

        expect($results)->toHaveCount(2);
        expect($results->pluck('id'))->toContain($nonPromo->id);
        expect($results->pluck('id'))->toContain($activePromo->id);
        expect($results->pluck('id'))->not->toContain($expiredPromo->id);
    });

    it('includes promo ending today', function () {
        $promoEndingToday = Membership::factory()->create([
            'is_promo' => true,
            'promo_start_date' => now()->subDays(5),
            'promo_end_date' => now(),
            'gym_id' => $this->gym->id,
        ]);

        $query = Membership::withoutGlobalScope('gym')->where('gym_id', $this->gym->id);
        Membership::getActivePromosQuery($query);
        $results = $query->get();

        expect($results->pluck('id'))->toContain($promoEndingToday->id);
    });
});