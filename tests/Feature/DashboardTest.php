<?php

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;

test('the dashboard shows the three statistics', function () {
    $books = Book::factory()->count(3)->create();
    $peminjam = User::factory()->create();
    User::factory()->count(4)->create();
    Loan::factory()->count(2)->for($peminjam, 'user')->for($books->first(), 'book')->create();
    Loan::factory()->returned()->for($peminjam, 'user')->for($books->first(), 'book')->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertViewHas('totalBooks', 3)
        ->assertViewHas('activeLoans', 2)
        ->assertViewHas('totalUsers', 6)
        ->assertSee('Total Buku')
        ->assertSee('Sedang Dipinjam')
        ->assertSee('Total Pengguna');
});

test('the dashboard lists the five latest loans', function () {
    $peminjam = User::factory()->create(['name' => 'Siti Aminah']);

    foreach (range(1, 6) as $i) {
        $book = Book::factory()->create(['title' => 'Buku '.$i]);
        Loan::factory()->for($peminjam, 'user')->for($book, 'book')->create([
            'borrowed_at' => now()->subDays($i),
        ]);
    }

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertViewHas('latestLoans')
        ->assertSee(['Peminjam', 'Siti Aminah', 'Buku 1', 'Buku 5'])
        ->assertDontSee('Buku 6');
});

test('the dashboard shows an empty message without loans', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Belum ada peminjaman.');
});
