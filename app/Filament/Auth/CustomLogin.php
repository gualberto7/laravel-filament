<?php

namespace App\Filament\Auth;

use Filament\Schemas\Schema;
use Filament\Auth\Pages\Login;
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
                    ->required(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent()
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
            'data.username' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
