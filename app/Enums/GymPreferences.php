<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasDescription;

enum GymPreferences: string implements HasDescription, HasLabel
{
    case RegisterKey = 'register_key';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::RegisterKey => 'Registro de llave / casillero',
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::RegisterKey => 'Al registrar un check-in, se debe ingresar el número de llave / casillero asignado al cliente.',
        };
    }
}
