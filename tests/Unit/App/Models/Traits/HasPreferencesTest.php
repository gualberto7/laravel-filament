<?php

use App\Models\User;
use Illuminate\Support\Facades\Cache;

describe('HasPreferences Trait', function () {
    test('it can set and get a preference', function () {
        $user = User::factory()->create();

        $user->setPreference('theme', 'dark');

        expect($user->getPreference('theme'))->toBe('dark');

        // Verify it's in the database
        $this->assertDatabaseHas('preferences', [
            'preferable_id' => $user->id,
            'preferable_type' => User::class,
            'key' => 'theme',
            'value' => 'dark',
        ]);
    });

    test('it caches the preference', function () {
        $user = User::factory()->create();
        $user->setPreference('language', 'en');

        // First call should cache it
        expect($user->getPreference('language'))->toBe('en');

        // Manually change DB to verify cache is used
        $user->preferences()->where('key', 'language')->update(['value' => 'es']);

        // Should still return 'en' from cache
        expect($user->getPreference('language'))->toBe('en');

        // Clear cache and check again
        Cache::forget("preferable.{$user->id}.language");
        expect($user->getPreference('language'))->toBe('es');
    });

    test('it clears cache on set', function () {
        $user = User::factory()->create();
        $user->setPreference('notifications', 'enabled');

        expect($user->getPreference('notifications'))->toBe('enabled');

        // Update preference
        $user->setPreference('notifications', 'disabled');

        // Should return new value (cache cleared)
        expect($user->getPreference('notifications'))->toBe('disabled');
    });

    test('it returns null for non-existent preference', function () {
        $user = User::factory()->create();

        expect($user->getPreference('non_existent_key'))->toBeNull();
    });

    test('it can get preference value alias', function () {
        $user = User::factory()->create();
        $user->setPreference('timezone', 'UTC');

        expect($user->getPreferenceValue('timezone'))->toBe('UTC');
    });
});
