<?php

namespace App\Livewire\Gym;

use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms;
use App\Enums\GymPreferences;

class Settings extends Component implements HasForms
{
    use InteractsWithForms;

    public $currentGym;
    public $data = [];

    public function mount($currentGym)
    {
        $this->currentGym = $currentGym;
 
        $preferences = $this->currentGym->preferences->filter(function ($item) {
            return $item->value == true;
        })->pluck('key')->toArray();

        $this->form->fill([
            'preferences' => $preferences,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\CheckboxList::make('preferences')
                    ->options(GymPreferences::class)
                    ->label(''),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        dd($this->form->getState());
    }

    public function render()
    {
        return view('livewire.gym.settings');
    }
}
