<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SubscriptionStatus: string implements HasColor, HasLabel
{
    case Active = 'active';
    case ExpiresSoon = 'expires_soon';
    case ExpiresToday = 'expires_today';
    case Expired = 'expired';
    case Inactive = 'inactive';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Active => 'Activo',
            self::ExpiresSoon => 'Vence pronto',
            self::ExpiresToday => 'Vence hoy',
            self::Expired => 'Vencido',
            self::Inactive => 'Inactivo',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Active => 'success',
            self::ExpiresSoon => 'warning',
            self::ExpiresToday => 'danger',
            self::Expired, self::Inactive => 'gray',
        };
    }
}
