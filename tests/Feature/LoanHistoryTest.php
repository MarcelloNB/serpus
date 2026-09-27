<?php

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;

test('guests are redirected to login from the history routes', function () {
    $this->get(route('loans.index'))->assertRedirect(route('login'));
    $this->get(route('loans.data'))->assertRedirect(route('login'));
});

test('a peminjam sees only their own loans', function () {
    $peminjam = User::factory()->create();
    $book = Book::factory()->create(['title' => 'Biologi Molekuler']);
    Loan::factory()->for($peminjam, 'user')->for($book, 'book')->create();
    Loan::factory()->for($book, 'book')->create();

    $response = $this->actingAs($peminjam)->getJson(route('loans.data', ['draw' => 1]));

    $response->assertOk()
        ->assertJsonPath('recordsTotal', 1)
        ->assertJsonPath('data.0.book_title', 'Biologi Molekuler');
});

test('the data endpoint renders the dates and the status badge', function () {
    $peminjam = User::factory()->create();
    $book = Book::factory()->create();
    $active = Loan::factory()->for($peminjam, 'user')->for($book, 'book')->create();
    $finished = Loan::factory()->returned()->for($peminjam, 'user')->for($book, 'book')->create();

    $response = $this->actingAs($peminjam)->getJson(route('loans.data'));

    $rows = collect($response->json('data'));
    expect($rows->firstWhere('id', $active->id)['status_label'])->toContain('Dipinjam')
        ->and($rows->firstWhere('id', $active->id)['returned_at_label'])->toBe('-')
        ->and($rows->firstWhere('id', $finished->id)['status_label'])->toContain('Dikembalikan')
        ->and($rows->firstWhere('id', $finished->id)['returned_at_label'])->not->toBe('-');
});

test('an admin is refused on the history endpoint', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->getJson(route('loans.data'))
        ->assertForbidden();
});

test('the history page is read-only and offers no return action', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('loans.index'))
        ->assertOk()
        ->assertSee('Riwayat Peminjaman')
        ->assertSee('loans/data')
        ->assertDontSee('Kembalikan');
});

test('the sidebar shows the history menu only to peminjam', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertSee('Riwayat Peminjaman');

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertDontSee('Riwayat Peminjaman');
});
