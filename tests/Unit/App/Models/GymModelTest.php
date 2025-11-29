<?php

use App\Models\Gym;
use App\Models\User;
use App\Models\Client;
use App\Models\CheckIn;
use App\Models\Membership;


beforeEach(function () {
    $this->user = User::factory()->create();
    $this->gym = Gym::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);

    $this->user->setPreference('current_gym', $this->gym->id);
});

// A gym has a owner
describe('Gym relationships', function () {
    test('gym has a owner', function () {
        $user = User::factory()->create();
        $gym = Gym::factory()->create(['user_id' => $user->id]);

        expect($gym->owner)->toBeInstanceOf(User::class);
        expect($gym->owner->id)->toBe($user->id);
    });

    test('gym has many staff', function () {
        User::factory(2)->create(['gym_id' => $this->gym->id]);
        expect($this->gym->staff->first())->toBeInstanceOf(User::class);
    });

    test('gym has many clients', function () {
        Client::factory(2)->create(['gym_id' => $this->gym->id]);
        expect($this->gym->clients->first())->toBeInstanceOf(Client::class);
    });

    test('gym has many memberships', function () {
        Membership::factory(2)->create(['gym_id' => $this->gym->id]);
        expect($this->gym->memberships->first())->toBeInstanceOf(Membership::class);
    });

    test('gym has many check-ins', function () {
        CheckIn::factory(2)->create(['gym_id' => $this->gym->id]);
        expect($this->gym->checkIns->first())->toBeInstanceOf(CheckIn::class);
    });
});
