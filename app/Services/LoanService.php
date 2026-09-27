<?php

namespace App\Services;

use App\Enums\LoanStatus;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanService
{
    /**
     * Catat peminjaman baru: kunci baris buku agar stok tidak bisa diambil dua kali.
     */
    public function borrow(User $user, Book $book): Loan
    {
        return DB::transaction(function () use ($user, $book): Loan {
            $lockedBook = Book::query()->whereKey($book->getKey())->lockForUpdate()->firstOrFail();

            if ($lockedBook->stock < 1) {
                throw ValidationException::withMessages([
                    'book_id' => 'Stok buku "'.$book->title.'" sedang habis.',
                ]);
            }

            $lockedBook->decrement('stock');

            return Loan::create([
                'user_id' => $user->getKey(),
                'book_id' => $lockedBook->getKey(),
                'borrowed_at' => now(),
                'status' => LoanStatus::Dipinjam,
            ]);
        });
    }

    /**
     * Catat pengembalian: kunci baris peminjaman agar tidak bisa dikembalikan dua kali.
     */
    public function processReturn(Loan $loan): Loan
    {
        return DB::transaction(function () use ($loan): Loan {
            $lockedLoan = Loan::query()->whereKey($loan->getKey())->lockForUpdate()->firstOrFail();

            if ($lockedLoan->status !== LoanStatus::Dipinjam) {
                throw ValidationException::withMessages([
                    'status' => 'Peminjaman ini sudah pernah dikembalikan.',
                ]);
            }

            Book::query()->whereKey($lockedLoan->book_id)->lockForUpdate()->increment('stock');

            $lockedLoan->update([
                'status' => LoanStatus::Dikembalikan,
                'returned_at' => now(),
            ]);

            return $lockedLoan;
        });
    }
}
