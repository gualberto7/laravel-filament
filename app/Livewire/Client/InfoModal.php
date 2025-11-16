<?php

namespace App\Livewire\Client;

use App\Models\Client;
use Livewire\Component;
use Livewire\Attributes\On;

class InfoModal extends Component
{
    public $client;
    public $subscription;

    #[On('addCheckin')] 
    public function addCheckin($client)
    {
        $this->client = Client::find($client);
        $this->subscription = $this->client->subscriptions->first() ?? null;
        $this->dispatch('open-modal', id: 'search-client');
    }
    public function render()
    {
        return view('livewire.client.info-modal');
    }
}
