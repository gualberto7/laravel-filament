<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\ValidationException;

class CustomLogin extends Login
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->components([
                        TextInput::make('username')
                            ->label('Username')
                            ->required(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.username' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
