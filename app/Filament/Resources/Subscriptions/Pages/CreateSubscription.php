<?php

namespace App\Filament\Resources\Subscriptions\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Subscriptions\SubscriptionResource;

class CreateSubscription extends CreateRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function getFormModel(): string|null
    {
        return null;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Preselect client if client_id is provided in URL
        if (request()->has('client_id')) {
            $data['clients'] = [request()->get('client_id')];
        }

        return $data;
    }

    public function mount(): void
    {
        parent::mount();

        // Preselect client if client_id is provided in URL
        if (request()->has('client_id')) {
            $this->form->fill([
                'clients' => [request()->get('client_id')]
            ]);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['clients']);
        $data['gym_id'] = auth()->user()->getCurrentGymId();
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
