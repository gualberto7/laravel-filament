<?php

use App\Models\CheckIn;
use App\Models\Client;
use App\Models\Gym;
use App\Models\User;

beforeEach(function () {
    $this->gym = Gym::factory()->create();
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->user->setPreference('current_gym', $this->gym->id);
    $this->client = Client::factory()->create(['gym_id' => $this->gym->id]);
});

describe('CheckIn today scope', function () {
    it('returns check-ins created today', function () {
        $todayCheckIn = CheckIn::factory()->create([
            'gym_id' => $this->gym->id,
            'client_id' => $this->client->id,
        ]);

        expect(CheckIn::today()->get()->pluck('id'))->toContain($todayCheckIn->id);
    });

    it('excludes check-ins from yesterday', function () {
        $yesterdayCheckIn = CheckIn::factory()->create([
            'gym_id' => $this->gym->id,
            'client_id' => $this->client->id,
            'created_at' => now()->subDay(),
        ]);

        expect(CheckIn::today()->get()->pluck('id'))->not->toContain($yesterdayCheckIn->id);
    });

    it('excludes check-ins from last month', function () {
        $oldCheckIn = CheckIn::factory()->create([
            'gym_id' => $this->gym->id,
            'client_id' => $this->client->id,
            'created_at' => now()->subMonth(),
        ]);

        expect(CheckIn::today()->get()->pluck('id'))->not->toContain($oldCheckIn->id);
    });

    it('counts only todays check-ins', function () {
        CheckIn::factory()->count(3)->create([
            'gym_id' => $this->gym->id,
            'client_id' => $this->client->id,
        ]);

        CheckIn::factory()->create([
            'gym_id' => $this->gym->id,
            'client_id' => $this->client->id,
            'created_at' => now()->subDay(),
        ]);

        expect(CheckIn::today()->count())->toBe(3);
    });
});
