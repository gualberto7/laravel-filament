<?php

use App\Models\Gym;
use App\Models\User;

describe('Memberships', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->gym = Gym::factory()->create(['user_id' => $this->user->id]);
        setupUser($this->user, $this->gym, 'super_admin');
        test()->actingAs($this->user);
    });

    test('should show memberships page', function () {
        $page = visit('/admin/memberships');
        $page->assertSee('Membresías')
            ->assertSee('Crear Membresía');
    });

    test('should see no records table message', function () {
        $page = visit('/admin/memberships');
        $page->assertSee('No se encontraron registros');
    });

    test('should see table columns', function () {
        $page = visit('/admin/memberships');
        $page->assertSee('Nombre')
            ->assertSee('Precio Bs.')
            ->assertSee('Duración días')
            ->assertSee('Tipo')
            ->assertSee('Activo?');
    });

    test('should redirect to Membreships create page', function () {
        $page = visit('/admin/memberships');
        $page->click('Crear Membresía')
            ->assertPathIs('/admin/memberships/create');
    });

    test('should create a membership and show it in the table', function () {
        ['user' => $user, 'gym' => $gym] = loginAs('super_admin');

        $page = visit('/admin/memberships/create');

        $page->type('@name-input', 'Mensual')
            ->type('@price-input', '200')
            ->type('@duration-input', '30')
            ->press('Activo?')
            ->click('@create-membership-button')
            ->assertSee('Membresía creado');

        $page->navigate('/admin/memberships')
            ->assertSee('Mensual')
            ->assertSee('200')
            ->assertSee('30')
            ->assertSee('Normal');
    });

    test('should create a promo and show it int the table', function () {
        $page = visit('/admin/memberships/create');

        $page->type('@name-input', 'Promo 2x1')
            ->type('@price-input', '300')
            ->type('@duration-input', '30')
            ->press('Activo?')
            ->press('Promo?')
            ->type('@promo_start_date-input', now()->format('Y-d-m'))
            ->type('@promo_end_date-input', now()->addDays(30)->format('Y-d-m'))
            ->type('@max_clients-input', '2')
            ->click('@create-membership-button')
            ->assertSee('Membresía creado');

        $page->navigate('/admin/memberships')
            ->assertSee('Promo 2x1')
            ->assertSee('300')
            ->assertSee('30')
            ->assertSee('Promoción');
    });
});
