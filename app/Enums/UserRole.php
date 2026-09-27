<?php

namespace App\Enums;

enum UserRole: string
{
    case Peminjam = 'peminjam';

    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Peminjam => 'Peminjam',
        };
    }
}
