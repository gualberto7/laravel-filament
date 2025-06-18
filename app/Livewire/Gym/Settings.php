<?php

namespace App\Livewire\Gym;

use Filament\Forms;
use Livewire\Component;
use Filament\Forms\Form;
use App\Enums\GymPreferences;
use Livewire\Attributes\Computed;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;

class Settings extends Component implements HasForms
{
    use InteractsWithForms;

    public $currentGym;
    public $preferences;
    public $initialState = [];
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

        $this->initialState = $this->form->getState();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\CheckboxList::make('preferences')
                    ->options(GymPreferences::class)
                    ->label('')
                    ->live(),
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

        Notification::make()
            ->title('Cambios guardados')
            ->success()
            ->send();
    }

    #[Computed]
    public function isDirty()
    {
        return $this->initialState == $this->form->getState();
    }

    public function render()
    {
        return view('livewire.gym.settings');
    }
}
