<?php

namespace App\Filament\Auth;

use Filament\Schemas\Schema;
use Filament\Auth\Pages\Login;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\ValidationException;

class CustomLogin extends Login
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('username')
                    ->label('Usuario')
                    ->required()
                    ->extraInputAttributes([
                        'data-test' => 'username-input',
                    ]),

                TextInput::make('password')
                    ->label('Contraseña')
                    ->required()
                    ->password()
                    // ->suffixIcon(Heroicon::Eye)
                    ->extraInputAttributes([
                        'data-test' => 'password-input',
                    ]),

                // $this->getRememberFormComponent(),
            ]);
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
            'data.username' => __('Credenciales incorrectas'),
        ]);
    }
}
