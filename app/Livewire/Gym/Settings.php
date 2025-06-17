<?php

namespace App\Livewire\Gym;

use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms;

class Settings extends Component implements HasForms
{
    use InteractsWithForms;

    public $currentGym;

    public function mount($currentGym)
    {
        $this->currentGym = $currentGym;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\CheckboxList::make('preferences')
                    ->options(function () {
                        return $this->currentGym->preferences()->pluck('key', 'key');
                    })
                    ->descriptions([
                        'register_key' => 'Al registrar una entrada del cliente, se debe ingresar el numero de llave / casillero del cliente.',
                    ])
                    ->label(''),
            ]);
    }

    public function render()
    {
        return view('livewire.gym.settings');
    }
}
