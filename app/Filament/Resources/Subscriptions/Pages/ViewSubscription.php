<?php

namespace App\Filament\Resources\Subscriptions\Pages;

use App\Filament\Resources\Subscriptions\SubscriptionResource;
use Filament\Resources\Pages\ViewRecord;
use Livewire\Attributes\On;

class ViewSubscription extends ViewRecord
{
    protected static string $resource = SubscriptionResource::class;

    #[On('payment-created')]
    public function refreshRecord(): void
    {
        $this->record->refresh();
    }
}
