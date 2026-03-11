<?php

use App\Exports\ClientsExport;
use App\Exports\Sheets\ClientsSheet;
use App\Exports\Sheets\SubscriptionsSheet;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Models\Client;
use App\Models\Gym;
use App\Models\Membership;
use App\Models\Subscription;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;

beforeEach(function () {
    ['user' => $this->user, 'gym' => $this->gym] = loginAs('owner');
});

describe('ClientsExport', function () {
    test('export button exists in the header', function () {
        Livewire::test(ListClients::class)
            ->assertActionExists(TestAction::make('export'));
    });

    test('downloads an xlsx file with the correct name', function () {
        Excel::fake();

        Livewire::test(ListClients::class)
            ->callAction(TestAction::make('export'));

        Excel::assertDownloaded('clientes-'.now()->format('Y-m-d').'.xlsx', function (ClientsExport $export) {
            return true;
        });
    });

    test('clients sheet only includes clients from the current gym', function () {
        $ownClient = Client::factory()->create(['gym_id' => $this->gym->id]);
        $otherGym = Gym::factory()->create();
        $otherClient = Client::factory()->create(['gym_id' => $otherGym->id]);

        $sheet = new ClientsSheet($this->gym->id);
        $results = $sheet->query()->get();

        expect($results->pluck('id'))->toContain($ownClient->id)
            ->not->toContain($otherClient->id);
    });

    test('subscriptions sheet omits clients without subscriptions', function () {
        $clientWithSub = Client::factory()->create(['gym_id' => $this->gym->id]);
        $clientWithoutSub = Client::factory()->create(['gym_id' => $this->gym->id]);

        $membership = Membership::factory()->create(['gym_id' => $this->gym->id]);
        $subscription = Subscription::factory()->create([
            'gym_id' => $this->gym->id,
            'membership_id' => $membership->id,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(25),
        ]);
        $subscription->clients()->attach($clientWithSub);

        $sheet = new SubscriptionsSheet($this->gym->id);
        $rows = $sheet->collection();
        $clientIds = $rows->pluck('client')->map->id;

        expect($clientIds)->toContain($clientWithSub->id)
            ->not->toContain($clientWithoutSub->id);
    });

    test('subscriptions sheet prioritizes active subscription over most recent expired', function () {
        $client = Client::factory()->create(['gym_id' => $this->gym->id]);
        $membership = Membership::factory()->create(['gym_id' => $this->gym->id]);

        $expiredSubscription = Subscription::factory()->create([
            'gym_id' => $this->gym->id,
            'membership_id' => $membership->id,
            'start_date' => now()->subDays(60),
            'end_date' => now()->subDays(10),
        ]);

        $activeSubscription = Subscription::factory()->create([
            'gym_id' => $this->gym->id,
            'membership_id' => $membership->id,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(25),
        ]);

        $client->subscriptions()->attach([$expiredSubscription->id, $activeSubscription->id]);

        $sheet = new SubscriptionsSheet($this->gym->id);
        $rows = $sheet->collection();
        $row = $rows->first(fn ($r) => $r['client']->id === $client->id);

        expect($row['subscription']->id)->toBe($activeSubscription->id);
    });
});
