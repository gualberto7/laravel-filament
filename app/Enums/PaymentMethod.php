<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    case Cash = 'cash';
    case Qr = 'qr';
    case Card = 'card';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Cash => 'Efectivo',
            self::Qr => 'QR',
            self::Card => 'Tarjeta',
        };
    }
}
