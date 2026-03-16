<?php

use App\Models\Gym;
use App\Models\User;
use App\Models\Client;

describe('Clients', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->gym = Gym::factory()->create(['user_id' => $this->user->id]);
        setupUser($this->user, $this->gym, 'admin');
        test()->actingAs($this->user);
    });

    test('should show clients page', function () {
        $page = visit('/admin/clients');
        $page->assertSee('Clientes')
            ->assertSee('Crear Cliente');
    });

    test('should see no records table message', function () {
        $page = visit('/admin/clients');
        $page->assertSee('No se encontraron registros');
    });

    test('should see table columns', function () {
        $page = visit('/admin/clients');
        $page->assertSee('Nombre')
            ->assertSee('Membresía')
            ->assertSee('Estado')
            ->assertSee('Celular');
    });

    test('should see client card information without subscription', function () {
        $client = Client::factory()->create(['gym_id' => $this->gym->id]);
        $page = visit('/admin/clients');
        $page->assertSee($client->name)
            ->assertSee('-')
            ->assertSee($client->phone);
    });

    test('should see client card information with subscription', function () {
        ['client' => $client, 'membership' => $membership, 'subscription' => $subscription] = createClientWithPlan($this->gym);
        $page = visit('/admin/clients');
        $page->assertSee($client->name)
            ->assertSee($membership->name)
            ->assertSee($subscription->status->getLabel())
            ->assertSee($client->phone);
    });

    test('should redirect to client view page', function () {
        $client = Client::factory()->create(['gym_id' => $this->gym->id]);
        $page = visit('/admin/clients');
        $page->click($client->name)
            ->assertUrlIs('*/admin/clients/'.$client->id);
    });

    test('should open client checkin modal', function () {
        ['client' => $client, 'membership' => $membership, 'subscription' => $subscription] = createClientWithPlan($this->gym);
        $page = visit('/admin/clients');
        $page->click('Checkin')
            ->click('Check In')
            ->assertSee('Check-in registrado correctamente');
    });

    test('create button should redirect to create client page', function () {
        $page = visit('/admin/clients');
        $page->click('Crear Cliente')
            ->assertUrlIs('*/admin/clients/create');
    });
});
