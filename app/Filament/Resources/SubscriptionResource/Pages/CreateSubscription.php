<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSubscription extends CreateRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['clients']);
        $data['gym_id'] = auth()->user()->gym->id;
        $data['created_by'] = auth()->user()->name;
        $data['updated_by'] = auth()->user()->name;
        return $data;
    }

    /*protected function afterCreate(): void
    {
        $subscription = $this->record;
        $clientIds = $this->data['client_ids'];
        $subscription->clients()->attach($clientIds);
    }*/
}
