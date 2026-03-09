<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

describe('User::availableRoles()', function () {
    it('returns all roles for super_admin', function () {
        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $this->actingAs($user);

        $roles = $user->availableRoles();

        expect($roles)->toHaveCount(Role::count());
    });

    it('returns all roles except super_admin and owner for owner', function () {
        $user = User::factory()->create();
        $user->assignRole('owner');
        $this->actingAs($user);

        $roles = $user->availableRoles();

        expect($roles->pluck('name'))
            ->not->toContain('super_admin')
            ->not->toContain('owner');
    });

    it('returns no roles for non-privileged roles', function () {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);

        $roles = $user->availableRoles();

        expect($roles)->toBeEmpty();
    });

    it('returns no roles when unauthenticated', function () {
        $user = User::factory()->create();

        $roles = $user->availableRoles();

        expect($roles)->toBeEmpty();
    });
});
