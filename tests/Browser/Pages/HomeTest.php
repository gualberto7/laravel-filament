<?php

use App\Models\Gym;
use App\Models\User;
use App\Models\Client;

describe('Home', function () {

    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->gym = Gym::factory()->create(['user_id' => $this->user->id]);
        setupUser($this->user, $this->gym, 'admin');
        test()->actingAs($this->user);
    });

    test('the home page loads correctly', function () {
        $page = visit('/admin/dashboard');

        $page->assertSee('Inicio');
        $page->assertSee('Bienvenido de nuevo, '.$this->user->name);
    });

    test('can search for a client', function () {
        $client = Client::factory()->create(['gym_id' => $this->gym->id]);

        $page = visit('/admin/dashboard');

        $page->assertDontSee($client->name)
            ->click('.fi-select-input-btn')
            ->assertSee($client->name);
    });

    test('can select a client and see client card information', function () {
        ['client' => $client, 'membership' => $membership, 'subscription' => $subscription] = createClientWithPlan($this->gym);
        $page = visit('/admin/dashboard');

        $page->click('.fi-select-input-btn')
            ->assertSee($client->name)
            ->click($client->name)
            ->assertSee('Celular: '.$client->phone)
            ->assertSee('Estado')
            ->assertSee('Plan')
            ->assertSee('Fecha Inicio')
            ->assertSee('Fecha Fin');
    });

    test('can add a checkin to a client', function () {
        ['client' => $client, 'membership' => $membership, 'subscription' => $subscription] = createClientWithPlan($this->gym);
        $page = visit('/admin/dashboard');

        $page->click('.fi-select-input-btn')
            ->assertSee($client->name)
            ->click($client->name)
            ->assertSee('Celular: '.$client->phone)
            ->click('Check In')
            ->assertSee('Check-in registrado correctamente');
    });
});
