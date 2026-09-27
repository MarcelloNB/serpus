<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\User;

test('guests see the catalog with the public header and no sidebar', function () {
    Book::factory()->create();

    $this->get(route('catalog.index'))
        ->assertOk()
        ->assertSee('Katalog Buku')
        ->assertSee('books/data')
        ->assertSee('Masuk')
        ->assertSee('Daftar')
        ->assertDontSee('Riwayat Peminjaman');
});

test('the catalog live search filters books without a page reload', function () {
    Book::factory()->create(['title' => 'Biologi Molekuler']);
    Book::factory()->create(['title' => 'Algoritma Struktur Data']);

    $response = $this->getJson(route('catalog.data', ['search' => ['value' => 'Biologi']]));

    $response->assertOk()
        ->assertJsonPath('recordsTotal', 2)
        ->assertJsonPath('recordsFiltered', 1);

    expect($response->json('data.0.title'))->toContain('Biologi Molekuler');
});

test('a guest is offered the login prompt instead of a borrow button', function () {
    Book::factory()->create(['stock' => 5]);

    $row = $this->getJson(route('catalog.data'))->json('data.0');

    expect($row['aksi'])->toContain('Masuk untuk meminjam');
});

test('a logged-in user is offered the borrow form', function () {
    $book = Book::factory()->create(['stock' => 5]);

    $row = $this->actingAs(User::factory()->create())
        ->getJson(route('catalog.data'))
        ->json('data.0');

    expect($row['aksi'])->toContain('Pinjam', route('catalog.borrow', $book->id));
});

test('an exhausted book shows a disabled stock badge and action', function () {
    Book::factory()->create(['stock' => 0]);

    $row = $this->getJson(route('catalog.data'))->json('data.0');

    expect($row['stock'])->toContain('Stok habis')
        ->and($row['aksi'])->toContain('Stok habis')
        ->and($row['aksi'])->toContain('aria-disabled');
});

test('a guest borrowing a book is redirected to the login page', function () {
    $book = Book::factory()->create(['stock' => 5]);

    $this->post(route('catalog.borrow', $book))->assertRedirect(route('login'));
});

test('a user can borrow an available book from the catalog', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create(['stock' => 2]);

    $this->actingAs($user)
        ->from(route('catalog.index'))
        ->post(route('catalog.borrow', $book))
        ->assertRedirect(route('catalog.index'))
        ->assertSessionHas('success');

    expect($book->fresh()->stock)->toBe(1)
        ->and(Loan::where('user_id', $user->getKey())->where('book_id', $book->getKey())->count())->toBe(1);
});

test('the success alert is shown on the catalog after borrowing', function () {
    $book = Book::factory()->create(['stock' => 2]);

    $this->actingAs(User::factory()->create())
        ->from(route('catalog.index'))
        ->post(route('catalog.borrow', $book))
        ->assertSessionHas('success');

    $this->get(route('catalog.index'))
        ->assertOk()
        ->assertSee('Buku berhasil dipinjam.');
});

test('borrowing a book whose stock is exhausted is rejected', function () {
    $book = Book::factory()->create(['stock' => 0]);

    $this->actingAs(User::factory()->create())
        ->post(route('catalog.borrow', $book))
        ->assertSessionHasErrors('book_id');

    expect($book->fresh()->stock)->toBe(0)
        ->and(Loan::count())->toBe(0);
});

test('the book detail page shows every piece of information for guests', function () {
    $category = Category::factory()->create(['name' => 'Sains']);
    $book = Book::factory()->create([
        'category_id' => $category->getKey(),
        'title' => 'Biologi Molekuler',
        'author' => 'Darno',
        'description' => 'Pengantar biologi modern.',
        'stock' => 5,
    ]);

    $this->get(route('catalog.show', $book))
        ->assertOk()
        ->assertSee('Biologi Molekuler')
        ->assertSee('Darno')
        ->assertSee('Sains')
        ->assertSee('Pengantar biologi modern.')
        ->assertSee('Tersedia')
        ->assertSee('Masuk untuk meminjam')
        ->assertSee('Kembali ke Katalog');
});

test('the book detail page offers the borrow form to logged-in users', function () {
    $book = Book::factory()->create(['stock' => 5]);

    $this->actingAs(User::factory()->create())
        ->get(route('catalog.show', $book))
        ->assertOk()
        ->assertSee('Pinjam')
        ->assertDontSee('Masuk untuk meminjam');
});

test('the detail page for a missing book returns 404', function () {
    $this->get('/books/999999')->assertNotFound();
});

test('a logged-in user sees the sidebar on the catalog', function () {
    Book::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('catalog.index'))
        ->assertOk()
        ->assertSee('Riwayat Peminjaman');
});
