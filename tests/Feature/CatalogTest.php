<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\User;

test('guests see the catalog with the public header and no sidebar', function () {
    Book::factory()->create(['title' => 'Biologi Molekuler']);

    $this->get(route('catalog.index'))
        ->assertOk()
        ->assertSee('Katalog Buku')
        ->assertSee('Biologi Molekuler')
        ->assertSee('books/data')
        ->assertSee('Masuk')
        ->assertSee('Daftar')
        ->assertDontSee('Riwayat Peminjaman');
});

test('the catalog is served under the katalog path', function () {
    $this->get('/katalog')->assertOk()->assertSee('Katalog Buku');
});

test('the catalog filters books by category', function () {
    $sains = Category::factory()->create();
    $komik = Category::factory()->create();
    Book::factory()->for($sains, 'category')->create(['title' => 'Biologi Molekuler']);
    Book::factory()->for($komik, 'category')->create(['title' => 'Naruto']);

    $this->get(route('catalog.index', ['kategori' => $sains->getKey()]))
        ->assertOk()
        ->assertSee('Biologi Molekuler')
        ->assertDontSee('Naruto');
});

test('the catalog filters books by stock', function () {
    Book::factory()->create(['title' => 'Matematika', 'stock' => 2]);
    Book::factory()->create(['title' => 'Ensiklopedia', 'stock' => 0]);

    $this->get(route('catalog.index', ['stok' => 'tersedia']))
        ->assertOk()
        ->assertSee('Matematika')
        ->assertDontSee('Ensiklopedia');

    $this->get(route('catalog.index', ['stok' => 'habis']))
        ->assertOk()
        ->assertSee('Ensiklopedia')
        ->assertDontSee('Matematika');
});

test('the search form filters the server-rendered catalog without javascript', function () {
    Book::factory()->create(['title' => 'Biologi Molekuler', 'author' => 'Darno']);
    Book::factory()->create(['title' => 'Algoritma Struktur Data', 'author' => 'Andra']);

    $this->get(route('catalog.index', ['search' => ['value' => 'Biologi']]))
        ->assertOk()
        ->assertSee('Biologi Molekuler')
        ->assertDontSee('Algoritma Struktur Data');
});

test('the data endpoint applies the category and stock filters', function () {
    $sains = Category::factory()->create();
    $komik = Category::factory()->create();
    Book::factory()->for($sains, 'category')->create(['stock' => 3]);
    Book::factory()->for($sains, 'category')->create(['stock' => 0]);
    Book::factory()->for($komik, 'category')->create(['stock' => 3]);

    $this->getJson(route('catalog.data', ['kategori' => $sains->getKey()]))
        ->assertOk()
        ->assertJsonPath('recordsTotal', 2);

    $this->getJson(route('catalog.data', ['stok' => 'habis']))
        ->assertOk()
        ->assertJsonPath('recordsTotal', 1);

    $this->getJson(route('catalog.data', ['kategori' => $sains->getKey(), 'stok' => 'tersedia']))
        ->assertOk()
        ->assertJsonPath('recordsTotal', 1);
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

test('borrowing is rejected when the borrower already holds the maximum books', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create(['stock' => 2]);
    Loan::factory()->count(5)->for($user, 'user')->create();

    $this->actingAs($user)
        ->post(route('catalog.borrow', $book))
        ->assertSessionHasErrors('book_id');

    expect($book->fresh()->stock)->toBe(2)
        ->and(Loan::where('user_id', $user->getKey())->count())->toBe(5);
});

test('a successful borrow is due fourteen days later', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create(['stock' => 1]);

    $this->actingAs($user)
        ->post(route('catalog.borrow', $book))
        ->assertSessionHas('success');

    $loan = Loan::where('user_id', $user->getKey())->firstOrFail();

    expect($loan->due_at->toDateString())->toBe(now()->addDays(14)->toDateString());
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
        ->assertSee('Batas peminjaman')
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

test('a book cover is shown on the catalog, detail page and data endpoint', function () {
    $book = Book::factory()->create([
        'title' => 'Bergambar',
        'cover_image' => 'covers/bergambar.jpg',
    ]);

    $this->get(route('catalog.index'))
        ->assertOk()
        ->assertSee('storage/covers/bergambar.jpg');

    $this->get(route('catalog.show', $book))
        ->assertOk()
        ->assertSee('storage/covers/bergambar.jpg');

    $response = $this->getJson(route('catalog.data', ['draw' => 1]));

    $response->assertOk();
    expect($response->json('data.0.cover_url'))->toContain('storage/covers/bergambar.jpg')
        ->and($response->json('data.0.title_text'))->toBe('Bergambar');
});

test('a book without a cover shows a placeholder instead of an image', function () {
    Book::factory()->create(['title' => 'Tanpa Sampul', 'cover_image' => null]);

    $this->get(route('catalog.index'))
        ->assertOk()
        ->assertDontSee('storage/covers/', false)
        ->assertSee('Tanpa Sampul');
});

test('html in a book title is escaped on the server-rendered catalog and detail pages', function () {
    $book = Book::factory()->create([
        'title' => '<img src=x onerror=alert(1)>',
        'stock' => 3,
    ]);

    $this->get(route('catalog.index'))
        ->assertOk()
        ->assertDontSee('<img src=x', false)
        ->assertSee('&lt;img src=x', false);

    $this->get(route('catalog.show', $book))
        ->assertOk()
        ->assertDontSee('<img src=x', false)
        ->assertSee('&lt;img src=x', false);
});

test('the catalog data endpoint escapes html titles while title_text stays plain', function () {
    Book::factory()->create(['title' => '<img src=x onerror=alert(1)>']);

    $row = $this->getJson(route('catalog.data', ['draw' => 1]))
        ->assertOk()
        ->json('data.0');

    expect($row['title'])->toContain('&lt;img src=x')
        ->and($row['title'])->not->toContain('<img src=x')
        ->and($row['title_text'])->toBe('<img src=x onerror=alert(1)>');
});
