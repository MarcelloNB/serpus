<?php

namespace Database\Factories;

use App\Enums\LoanStatus;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

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
}
