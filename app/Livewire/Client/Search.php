<?php

namespace App\Livewire\Client;

use App\Models\Client;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Filament\Forms;
use Filament\Notifications\Notification;
use Livewire\Attributes\Computed;

class Search extends Component implements HasForms
{
    use InteractsWithForms;

    public $currentGym;
    public $search;
    public $data = [];
    public $client;
    public $subscription;

    public function mount($currentGym)
    {
        $this->currentGym = $currentGym;
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->label('Cliente')
                    ->options(Client::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        if ($state) {
                            $this->client = Client::find($state);
                            $this->subscription = $this->client->subscriptions()->latest()->first();
                        } else {
                            $this->client = null;
                            $this->subscription = null;
                        }
                    })
            ])
            ->statePath('data');
    }

    public function render()
    {
        return view('livewire.client.search');
    }
}
