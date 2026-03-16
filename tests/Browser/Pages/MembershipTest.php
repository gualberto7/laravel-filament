<?php

use App\Models\Gym;
use App\Models\User;

describe('Memberships', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->gym = Gym::factory()->create(['user_id' => $this->user->id]);
        setupUser($this->user, $this->gym, 'owner');
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
            ->assertSee('Tiempo (días)')
            ->assertSee('Tipo')
            ->assertSee('Estado');
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
            ->press('Es promoción?')
            ->type('@promo_start_date-input', now()->format('Y-m-d'))
            ->type('@promo_end_date-input', now()->addDays(7)->format('Y-m-d'))
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
