<?php

use App\Filament\Resources\Clients\Pages\ListClients;
use App\Models\Client;
use App\Models\Gym;
use App\Models\Membership;
use App\Models\Subscription;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach(function () {
    ['user' => $this->user, 'gym' => $this->gym] = loginAs('admin');
});

describe('ListClients page', function () {
    test('page loads successfully', function () {
        Livewire::test(ListClients::class)
            ->assertOk();
    });

    test('lists clients belonging to the gym', function () {
        $clients = Client::factory()->count(3)->create(['gym_id' => $this->gym->id]);

        Livewire::test(ListClients::class)
            ->assertCanSeeTableRecords($clients);
    });

    test('does not list clients from other gyms', function () {
        $otherGym = Gym::factory()->create();
        $ownClient = Client::factory()->create(['gym_id' => $this->gym->id]);
        $otherClient = Client::factory()->create(['gym_id' => $otherGym->id]);

        Livewire::test(ListClients::class)
            ->assertCanSeeTableRecords([$ownClient])
            ->assertCanNotSeeTableRecords([$otherClient]);
    });

    test('Activos tab shows only clients with active subscriptions', function () {
        $membership = Membership::factory()->create(['gym_id' => $this->gym->id]);

        $activeClient = Client::factory()->create(['gym_id' => $this->gym->id]);
        $subscription = Subscription::factory()->create([
            'gym_id' => $this->gym->id,
            'membership_id' => $membership->id,
            'end_date' => now()->addDays(30),
        ]);
        $subscription->clients()->attach($activeClient);

        $inactiveClient = Client::factory()->create(['gym_id' => $this->gym->id]);

        Livewire::test(ListClients::class)
            ->set('activeTab', 'active')
            ->assertCanSeeTableRecords([$activeClient])
            ->assertCanNotSeeTableRecords([$inactiveClient]);
    });

    test('Inactivos tab shows only clients without active subscriptions', function () {
        $membership = Membership::factory()->create(['gym_id' => $this->gym->id]);

        $activeClient = Client::factory()->create(['gym_id' => $this->gym->id]);
        $subscription = Subscription::factory()->create([
            'gym_id' => $this->gym->id,
            'membership_id' => $membership->id,
            'end_date' => now()->addDays(30),
        ]);
        $subscription->clients()->attach($activeClient);

        $inactiveClient = Client::factory()->create(['gym_id' => $this->gym->id]);

        Livewire::test(ListClients::class)
            ->set('activeTab', 'inactive')
            ->assertCanSeeTableRecords([$inactiveClient])
            ->assertCanNotSeeTableRecords([$activeClient]);
    });

    test('can search clients by name', function () {
        $clients = Client::factory()->count(3)->create(['gym_id' => $this->gym->id]);
        $target = $clients->first();

        Livewire::test(ListClients::class)
            ->searchTable($target->name)
            ->assertCanSeeTableRecords([$target])
            ->assertCanNotSeeTableRecords($clients->skip(1));
    });

    test('create action button exists in the header', function () {
        Livewire::test(ListClients::class)
            ->assertActionExists(TestAction::make('create'));
    });

    test('create action button opens the create form', function () {
        Livewire::test(ListClients::class)
            ->mountAction(TestAction::make('create'))
            ->assertActionMounted(TestAction::make('create'));
    });
});
