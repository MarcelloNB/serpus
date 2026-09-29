<?php

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function queryCountDuring(callable $callback): int
{
    DB::enableQueryLog();
    DB::flushQueryLog();

    $callback();

    $count = count(DB::getQueryLog());
    DB::disableQueryLog();

    return $count;
}

test('the landing page stays within its query budget', function () {
    Book::factory()->count(3)->create();

    $count = queryCountDuring(fn () => $this->get('/')->assertOk());

    expect($count)->toBeLessThanOrEqual(8);
});

test('the catalog page stays within its query budget', function () {
    Book::factory()->count(3)->create();

    $count = queryCountDuring(fn () => $this->get(route('catalog.index'))->assertOk());

    expect($count)->toBeLessThanOrEqual(6);
});

test('the catalog data endpoint stays within its query budget', function () {
    Book::factory()->count(3)->create();

    $count = queryCountDuring(fn () => $this->getJson(route('catalog.data', ['draw' => 1]))->assertOk());

    expect($count)->toBeLessThanOrEqual(6);
});

test('the book detail page stays within its query budget', function () {
    $book = Book::factory()->create();

    $count = queryCountDuring(fn () => $this->get(route('catalog.show', $book))->assertOk());

    expect($count)->toBeLessThanOrEqual(5);
});

test('the admin dashboard stays within its query budget', function () {
    $admin = User::factory()->admin()->create();
    Book::factory()->count(3)->create();

    $count = queryCountDuring(fn () => $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk());

    expect($count)->toBeLessThanOrEqual(8);
});

test('the admin books page stays within its query budget', function () {
    $admin = User::factory()->admin()->create();

    $count = queryCountDuring(fn () => $this->actingAs($admin)->get(route('admin.books.index'))->assertOk());

    expect($count)->toBeLessThanOrEqual(4);
});

test('the admin books data endpoint stays within its query budget', function () {
    $admin = User::factory()->admin()->create();
    Book::factory()->count(3)->create();

    $count = queryCountDuring(fn () => $this->actingAs($admin)->getJson(route('admin.books.data', ['draw' => 1]))->assertOk());

    expect($count)->toBeLessThanOrEqual(6);
});

test('the history page stays within its query budget', function () {
    $peminjam = User::factory()->create();
    Loan::factory()->count(3)->for($peminjam, 'user')->create();

    $count = queryCountDuring(fn () => $this->actingAs($peminjam)->get(route('loans.index'))->assertOk());

    expect($count)->toBeLessThanOrEqual(4);
});

test('the history data endpoint stays within its query budget', function () {
    $peminjam = User::factory()->create();
    Loan::factory()->count(3)->for($peminjam, 'user')->create();

    $count = queryCountDuring(fn () => $this->actingAs($peminjam)->getJson(route('loans.data'))->assertOk());

    expect($count)->toBeLessThanOrEqual(6);
});
