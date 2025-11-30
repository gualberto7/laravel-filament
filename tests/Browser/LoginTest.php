<?php

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

test('login page loads', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/admin/login')
            ->assertSee('Usuario');
    });
});
