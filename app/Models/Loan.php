<?php

namespace App\Models;

use App\Enums\LoanStatus;
use Database\Factories\LoanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    /** @use HasFactory<LoanFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'book_id', 'borrowed_at', 'returned_at', 'status'];

    protected function casts(): array
    {
        return [
            'status' => LoanStatus::class,
            'borrowed_at' => 'datetime',
            'returned_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function isActive(): bool
    {
        return $this->status === LoanStatus::Dipinjam;
    }
}
