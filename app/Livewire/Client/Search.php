<?php

namespace App\Livewire\Client;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use App\Models\Client;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class Search extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_id')
                    ->label('Cliente')
                    ->options(Client::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        if ($state) {
                            $this->dispatch('addCheckin', $state);
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
