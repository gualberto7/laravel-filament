<?php

describe('Authentication', function () {
    test('the login page loads correctly', function () {
        $page = visit('/admin/login');

        $page->assertSee('Usuario');
    });

    test('can login with valid credentials', function () {
        $page = visit('/admin/login');

        $page->type('@username-input', 'admin')
            ->type('@password-input', 'password')
            ->press('Entrar');

        $page->assertSee('Credenciales incorrectas');
    });
});
