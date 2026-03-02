<?php

use App\Models\Gym;
use App\Models\User;

describe('Authentication', function () {
    test('the login page loads correctly', function () {
        $page = visit('/admin/login');

        $page->assertSee('Usuario');
    });

    test('cannot login with invalid credentials', function () {
        $page = visit('/admin/login');

        $page->type('@username-input', 'admin')
            ->type('@password-input', 'password')
            ->press('Entrar');

        $page->assertSee('Credenciales incorrectas');
    });

    test('can login with valid credentials', function () {
        $user = User::factory()->create();
        $gym = Gym::factory()->create(['user_id' => $user->id]);
        setupUser($user, $gym, 'admin');

        $page = visit('/admin/login');

        $page->type('@username-input', $user->username)
            ->type('@password-input', 'password')
            ->press('Entrar')
            ->assertSee('Inicio');
    });
});
