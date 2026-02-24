<?php

use App\Models\User;

describe('Authentication', function () {
    test('the login page loads correctly', function () {
        $page = visit('/admin/login');

        $page->assertSee('Usuario');
    });

    test('can login with valid credentials', function () {
        $user = User::factory()->create([
            'username' => 'admin',
        ]);

        $page = visit('/admin/login');

        $page->type('#form.username', $user->username)
            ->type('#form.password', 'password')
            ->press('Entrar');

        $page->assertSee('Dashboard');
    });
});
