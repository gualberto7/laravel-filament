<?php

use App\Models\Gym;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature', 'Unit', 'Browser')
    ->beforeEach(function () {
        $this->seed(\Database\Seeders\ShieldSeeder::class);
    });

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/**
 * Create and authenticate a user with a gym and role.
 *
 * @return array{user: \App\Models\User, gym: \App\Models\Gym}
 */
function loginAs(string $role = 'admin'): array
{
    $user = User::factory()->create();
    $gym = Gym::factory()->create(['user_id' => $user->id]);

    test()->actingAs($user);
    $user->assignRole($role);
    $user->setPreference('current_gym', $gym->id);

    \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

    return compact('user', 'gym');
}

/**
 * Set up an existing user with a role and current gym preference.
 * Use this in browser tests where actingAs() is not available.
 *
 * @return array{user: \App\Models\User, gym: \App\Models\Gym}
 */
function setupUser(User $user, Gym $gym, string $role = 'admin'): array
{
    $user->assignRole($role);
    $user->setPreference('current_gym', $gym->id);

    return compact('user', 'gym');
}
