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

    public function mount($currentGym)
    {
        $this->currentGym = $currentGym;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\CheckboxList::make('preferences')
                    ->options(GymPreferences::class)
                    ->label(''),
            ]);
    }

    public function render()
    {
        return view('livewire.gym.settings');
    }
}
