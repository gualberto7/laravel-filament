<?php

use App\Models\Gym;
use App\Models\User;

describe('Gym Settings', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->gym = Gym::factory()->create(['user_id' => $this->user->id]);
        setupUser($this->user, $this->gym, 'admin');
        test()->actingAs($this->user);
    });

    test('should show gym settings page', function () {
        $page = visit('/admin/settings');
        $page->assertSee('Configuración')
            ->assertSee('Gimnasio')
            ->assertSee('Configuración del gimnasio');
    });
});
