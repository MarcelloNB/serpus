<?php

namespace App\Enums;

enum UserRole: string
{
    case Peminjam = 'peminjam';

    case Admin = 'admin';
}
