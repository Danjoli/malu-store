<?php

namespace App\Enums;

enum AdminRole: string
{
    case Support = 'suporte';
    case Admin = 'admin';
    case SuperAdmin = 'superadmin';

    public function label(): string
    {
        return match ($this) {
            self::Support => 'Suporte',
            self::Admin => 'Administrador',
            self::SuperAdmin => 'Super Administrador',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Support => 'bg-yellow-200 text-yellow-800',
            self::Admin => 'bg-blue-200 text-blue-800',
            self::SuperAdmin => 'bg-purple-200 text-purple-800',
        };
    }
}
