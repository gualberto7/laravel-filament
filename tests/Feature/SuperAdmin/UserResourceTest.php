<?php

use App\Filament\SuperAdmin\Resources\Users\Pages\CreateUser;
use App\Filament\SuperAdmin\Resources\Users\Pages\EditUser;
use App\Filament\SuperAdmin\Resources\Users\Pages\ListUsers;
use App\Models\Gym;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->superAdmin = User::factory()->create();
    $this->superAdmin->assignRole('super_admin');
    test()->actingAs($this->superAdmin);
    \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('superAdmin'));
});

describe('SuperAdmin UserResource', function () {
    test('list page renders successfully', function () {
        Livewire::test(ListUsers::class)
            ->assertOk();
    });

    test('list page shows existing users', function () {
        $users = User::factory()->count(3)->create();

        Livewire::test(ListUsers::class)
            ->assertCanSeeTableRecords($users);
    });

    test('create page renders successfully', function () {
        Livewire::test(CreateUser::class)
            ->assertOk();
    });

    test('can create a user with a role', function () {
        $role = \Spatie\Permission\Models\Role::where('name', 'admin')->first();

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Nuevo Usuario',
                'username' => 'nuevousuario',
                'email' => 'nuevo@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'roles' => [$role->id],
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::where('username', 'nuevousuario')->first();
        expect($user)->not->toBeNull();
        expect($user->hasRole('admin'))->toBeTrue();
    });

    test('edit page renders successfully', function () {
        $user = User::factory()->create();

        Livewire::test(EditUser::class, ['record' => $user->id])
            ->assertOk();
    });

    test('can create an owner user with a gym', function () {
        $ownerRole = \Spatie\Permission\Models\Role::where('name', 'owner')->first();

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Owner User',
                'username' => 'owneruser',
                'email' => 'owner@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'roles' => [$ownerRole->id],
                'ownedGym' => ['name' => 'Mi Gimnasio', 'address' => 'Calle 123'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::where('username', 'owneruser')->first();
        expect($user)->not->toBeNull();
        expect($user->hasRole('owner'))->toBeTrue();
        expect($user->ownedGym)->not->toBeNull();
        expect($user->ownedGym->name)->toBe('Mi Gimnasio');
        expect($user->gym_id)->toBe($user->ownedGym->id);
    });

    test('can toggle user active status', function () {
        $user = User::factory()->create(['is_active' => true]);

        Livewire::test(EditUser::class, ['record' => $user->id])
            ->fillForm(['is_active' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($user->fresh()->is_active)->toBeFalse();
    });
});
