<?php

use App\Models\Gym;
use Livewire\Livewire;
use App\Enums\GymPreferences;
use App\Livewire\Gym\Settings;

describe('Gym Settings Component', function () {
    test('it can render', function () {
        $gym = Gym::factory()->create();

        Livewire::test(Settings::class, ['currentGym' => $gym])
            ->assertOk();
    });

    test('it saves preferences', function () {
        $gym = Gym::factory()->create();

        // Ensure no preferences exist initially
        expect($gym->preferences()->count())->toBe(0);

        Livewire::test(Settings::class, ['currentGym' => $gym])
            ->set('data.preferences', [GymPreferences::RegisterKey->value])
            ->call('update')
            ->assertHasNoErrors();

        // Verify it was saved
        expect((bool) $gym->refresh()->getPreference(GymPreferences::RegisterKey->value))->toBeTrue();
    });

    test('it updates existing preferences', function () {
        $gym = Gym::factory()->create();
        $gym->setPreference(GymPreferences::RegisterKey->value, true);

        Livewire::test(Settings::class, ['currentGym' => $gym])
            // Uncheck it
            ->set('data.preferences', [])
            ->call('update')
            ->assertHasNoErrors();

        // Verify it was updated to false
        expect((bool) $gym->refresh()->getPreference(GymPreferences::RegisterKey->value))->toBeFalse();
    });
});
