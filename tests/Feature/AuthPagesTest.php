<?php

use function Pest\Laravel\get;

describe('Auth pages', function () {
    test('admin login page loads successfully', function () {
        get('/admin/login')
            ->assertOk()
            ->assertSee('Usuario');
    });

    test('super admin login page loads successfully', function () {
        get('/super-admin/login')
            ->assertOk()
            ->assertSee('Usuario');
    });

    test('unauthenticated admin access redirects to login', function () {
        get('/admin')
            ->assertRedirect('/admin/login');
    });

    test('unauthenticated super admin access redirects to login', function () {
        get('/super-admin')
            ->assertRedirect('/super-admin/login');
    });
});
