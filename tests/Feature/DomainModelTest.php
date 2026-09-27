<?php

use App\Enums\LoanStatus;
use App\Enums\UserRole;
use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\QueryException;

test('user factory defaults to peminjam and supports the admin state', function () {
    $peminjam = User::factory()->create();
    $admin = User::factory()->admin()->create();

    expect($peminjam->role)->toBe(UserRole::Peminjam)
        ->and($peminjam->isAdmin())->toBeFalse()
        ->and($admin->role)->toBe(UserRole::Admin)
        ->and($admin->isAdmin())->toBeTrue();
});

test('a new user without an explicit role gets peminjam from the database default', function () {
    $id = DB::table('users')->insertGetId([
        'name' => 'Peminjam Baru',
        'email' => 'baru@example.test',
        'password' => Hash::make('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect(User::find($id)->role)->toBe(UserRole::Peminjam);
});

test('category owns books', function () {
    $category = Category::factory()->has(Book::factory()->count(2), 'books')->create();

    expect($category->books)->toHaveCount(2)
        ->and($category->books->first()->category->is($category))->toBeTrue();
});

test('book knows when it can be borrowed', function () {
    $book = Book::factory()->create(['stock' => 3]);
    $empty = Book::factory()->outOfStock()->create();

    expect($book->isAvailable())->toBeTrue()
        ->and($empty->stock)->toBe(0)
        ->and($empty->isAvailable())->toBeFalse();
});

test('loan factory creates an active loan and a returned state', function () {
    $active = Loan::factory()->create();
    $returned = Loan::factory()->returned()->create();

    expect($active->status)->toBe(LoanStatus::Dipinjam)
        ->and($active->returned_at)->toBeNull()
        ->and($active->isActive())->toBeTrue()
        ->and($returned->status)->toBe(LoanStatus::Dikembalikan)
        ->and($returned->returned_at)->not->toBeNull()
        ->and($returned->isActive())->toBeFalse();
});

test('loan relationships resolve to their owners', function () {
    $loan = Loan::factory()->create();

    expect($loan->user)->toBeInstanceOf(User::class)
        ->and($loan->book)->toBeInstanceOf(Book::class)
        ->and($loan->user->loans->first()->is($loan))->toBeTrue()
        ->and($loan->book->loans->first()->is($loan))->toBeTrue();
});

test('a user with loan history cannot be deleted', function () {
    $loan = Loan::factory()->create();

    expect(fn () => $loan->user->delete())->toThrow(QueryException::class);
});

test('a book with loan history cannot be deleted', function () {
    $loan = Loan::factory()->create();

    expect(fn () => $loan->book->delete())->toThrow(QueryException::class);
});

test('a category with books cannot be deleted', function () {
    $category = Category::factory()->has(Book::factory(), 'books')->create();

    expect(fn () => $category->delete())->toThrow(QueryException::class);
});
