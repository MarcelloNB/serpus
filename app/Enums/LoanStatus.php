<?php

namespace App\Enums;

enum LoanStatus: string
{
    case Dipinjam = 'dipinjam';

    case Dikembalikan = 'dikembalikan';
}
