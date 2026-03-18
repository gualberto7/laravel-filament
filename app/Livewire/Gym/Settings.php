<?php

namespace App\Livewire\Gym;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Enums\GymPreferences;

class Settings extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public $currentGym;

    public $preferences;

    public $initialState = [];

    public $data = [];

    public function mount($currentGym): void
    {
        $this->currentGym = $currentGym;
        $this->preferences = $this->currentGym->preferences;

        $preferences = $this->preferences->filter(function ($item) {
            return $item->value == true;
        })->pluck('key')->toArray();

        $this->form->fill([
            'preferences' => $preferences,
        ]);

        $this->initialState = $this->data;
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
        $selectedPreferences = $this->data['preferences'] ?? [];

        foreach (GymPreferences::cases() as $case) {
            $this->currentGym->setPreference(
                $case->value,
                in_array($case->value, $selectedPreferences),
            );
        }

        $this->preferences = $this->currentGym->preferences()->get();
        $this->initialState = $this->data;

        Notification::make()
            ->title('Cambios guardados')
            ->success()
            ->send();
    }

    #[Computed]
    public function isDirty()
    {
        return $this->initialState == $this->data;
    }

    public function render()
    {
        return view('livewire.gym.settings');
    }
}
