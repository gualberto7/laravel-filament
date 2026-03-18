<?php

namespace App\Livewire\Account;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Settings extends Component implements HasForms
{
    use InteractsWithForms;

    public $passwordData = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('current_password')
                    ->label('Contraseña actual')
                    ->password()
                    ->revealable()
                    ->required()
                    ->currentPassword(),
                TextInput::make('password')
                    ->label('Nueva contraseña')
                    ->password()
                    ->revealable()
                    ->required()
                    ->rule(Password::default())
                    ->same('password_confirmation'),
                TextInput::make('password_confirmation')
                    ->label('Confirmar nueva contraseña')
                    ->password()
                    ->revealable()
                    ->required(),
            ])
            ->statePath('passwordData');
    }

    public function updatePassword(): void
    {
        $this->form->validate();

        auth()->user()->update([
            'password' => Hash::make($this->passwordData['password']),
        ]);

        $this->form->fill();

        Notification::make()
            ->title('Contraseña actualizada')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('livewire.account.settings');
    }
}
