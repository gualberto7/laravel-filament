<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasDescription;

enum GymPreferences: string implements HasLabel, HasDescription
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
            self::RegisterKey => 'Al registrar una entrada del cliente, se debe ingresar el numero de llave / casillero del cliente.',
        };
    }
}
