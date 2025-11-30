<?php

use App\Models\Gym;
use App\Models\User;
use App\Models\Client;
use App\Models\CheckIn;
use App\Models\Membership;




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

describe('Gym preferences', function () {
    test('gym can set and get a preference', function () {
        $gym = Gym::factory()->create();
        $gym->setPreference('theme', 'dark');
        expect($gym->getPreference('theme'))->toBe('dark');
    });
});
