<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SuperAdmin = 'super_admin';
    case Owner = 'owner';
    case Admin = 'admin';
    case Trainer = 'trainer';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Administrador',
            self::Owner => 'Propietario',
            self::Admin => 'Administrador',
            self::Trainer => 'Entrenador',
        };
    }
}
