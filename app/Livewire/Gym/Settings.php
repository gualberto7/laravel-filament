<?php

namespace App\Livewire\Gym;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Schema;
use Filament\Forms\Components\CheckboxList;
use Livewire\Component;
use App\Enums\GymPreferences;
use Livewire\Attributes\Computed;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;

class Settings extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                CheckboxList::make('preferences')
                    ->options(GymPreferences::class)
                    ->label('')
                    ->live(),
            ])
            ->statePath('data');
    }

    public function update(): void
    {
        $selectedPreferences = $this->form->getState()['preferences'] ?? [];

        $selectedValues = collect($selectedPreferences)->map(function ($item) {
            return $item instanceof \UnitEnum ? $item->value : $item;
        })->toArray();

        foreach (GymPreferences::cases() as $preference) {
            $key = $preference->value;
            $value = in_array($key, $selectedValues);

            $this->currentGym->setPreference($key, $value);
        }

        Notification::make()
            ->title('Cambios guardados')
            ->success()
            ->send();

        $this->initialState = $this->form->getState();
    }

    #[Computed]
    public function isDirty()
    {
        return $this->initialState === $this->form->getState();
    }

    public function render()
    {
        return view('livewire.gym.settings');
    }
}
