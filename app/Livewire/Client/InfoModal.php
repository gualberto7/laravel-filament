<?php

namespace App\Livewire\Client;

use App\Models\Gym;
use App\Models\Client;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Filament\Resources\CheckIns\CheckInResource;
use Filament\Notifications\Notification;

class InfoModal extends Component
{
    public $client;

    public $subscription;

    public $key_number;

    public $gym;

    public $register_key;

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $this->gymId = $user->getCurrentGymId();

        if (! $this->gymId) {
            return;
        }

        $this->register_key = Gym::findOrFail($this->gymId)->getPreference('register_key');
    }

    #[On('addCheckin')]
    public function addCheckin($client)
    {
        $this->client = Client::find($client);
        $this->subscription = $this->client->latestSubscription->first() ?? null;
        $this->dispatch('open-modal', id: 'search-client');
    }

    public function checkIn()
    {
        $this->client->addCheckIn($this->key_number);

        // After registering the check-in, you might want to close the modal
        $this->dispatch('close-modal', id: 'search-client');

        // Optionally, you can also show a success notification
        Notification::make()
            ->title(CheckInResource::getModelLabel().' registrado correctamente')
            ->body('El '.CheckInResource::getModelLabel().' para '.$this->client->name.' ha sido registrado exitosamente.')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('livewire.client.info-modal');
    }
}
