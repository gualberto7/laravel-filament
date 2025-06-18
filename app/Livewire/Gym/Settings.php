<?php

namespace App\Livewire\Gym;

use Filament\Forms;
use Livewire\Component;
use Filament\Forms\Form;
use App\Enums\GymPreferences;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class Settings extends Component implements HasForms
{
    use InteractsWithForms;

    public $currentGym;
    public $preferences;
    public $data = [];

    public function mount($currentGym)
    {
        $this->currentGym = $currentGym;
        $this->preferences = $this->currentGym->preferences;

        $preferences = $this->preferences->filter(function ($item) {
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

    public function update(): void
    {
        $this->preferences->each(function ($item) {
            if (in_array($item->key, $this->form->getState()['preferences'])) {
                $item->value = true;
            } else {
                $item->value = false;
            }
            $item->save();
        });
    }

    public function render()
    {
        return view('livewire.gym.settings');
    }
}
