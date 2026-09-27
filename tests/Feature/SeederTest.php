<?php

use App\Enums\UserRole;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;

test('seeders create the demo accounts, categories and books', function () {
    $this->seed();

    $admin = User::where('email', 'admin@serpus.test')->first();
    $peminjam = User::where('email', 'peminjam@serpus.test')->first();

    expect($admin)->not->toBeNull()
        ->and($admin->isAdmin())->toBeTrue()
        ->and($admin->password)->not->toBe('password')
        ->and($peminjam)->not->toBeNull()
        ->and($peminjam->role)->toBe(UserRole::Peminjam)
        ->and(Category::count())->toBe(8)
        ->and(Book::count())->toBe(12)
        ->and(Book::where('stock', 0)->count())->toBe(2);
});

test('seeders can be run repeatedly without duplicating data', function () {
    $this->seed();

    $counts = [
        'users' => User::count(),
        'categories' => Category::count(),
        'books' => Book::count(),
    ];

    $this->seed();

    expect(User::count())->toBe($counts['users'])
        ->and(Category::count())->toBe($counts['categories'])
        ->and(Book::count())->toBe($counts['books']);
});

test('every seeded book belongs to a seeded category', function () {
    $this->seed();

    expect(Book::query()->whereDoesntHave('category')->count())->toBe(0);
});
