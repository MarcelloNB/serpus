<?php

namespace Database\Factories;

use App\Enums\LoanStatus;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Loan>
 */
class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'borrowed_at' => fake()->dateTimeBetween('-30 days'),
            'due_at' => fn (array $attributes) => Carbon::parse($attributes['borrowed_at'])->addDays((int) config('loans.days')),
            'returned_at' => null,
            'status' => LoanStatus::Dipinjam,
        ];
    }

    public function returned(): static
    {
        return $this->state(fn (array $attributes) => [
            'returned_at' => fake()->dateTimeBetween('-10 days'),
            'status' => LoanStatus::Dikembalikan,
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'borrowed_at' => now()->subDays(20),
            'due_at' => now()->subDays(6),
            'returned_at' => null,
            'status' => LoanStatus::Dipinjam,
        ]);
    }
}
