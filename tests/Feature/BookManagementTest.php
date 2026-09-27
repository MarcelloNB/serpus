<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\User;

test('guests are redirected to login from the book routes', function () {
    $book = Book::factory()->create();

    $this->get(route('admin.books.index'))->assertRedirect(route('login'));
    $this->get(route('admin.books.data'))->assertRedirect(route('login'));
    $this->get(route('admin.books.create'))->assertRedirect(route('login'));
    $this->get(route('admin.books.edit', $book))->assertRedirect(route('login'));
    $this->post(route('admin.books.store'))->assertRedirect(route('login'));
    $this->put(route('admin.books.update', $book))->assertRedirect(route('login'));
    $this->delete(route('admin.books.destroy', $book))->assertRedirect(route('login'));
});

test('a peminjam is refused on the book routes', function () {
    $book = Book::factory()->create();

    $this->actingAs(User::factory()->create());

    $this->get(route('admin.books.index'))->assertForbidden();
    $this->get(route('admin.books.data'))->assertForbidden();
    $this->get(route('admin.books.create'))->assertForbidden();
    $this->get(route('admin.books.edit', $book))->assertForbidden();
    $this->post(route('admin.books.store'))->assertForbidden();
    $this->put(route('admin.books.update', $book))->assertForbidden();
    $this->delete(route('admin.books.destroy', $book))->assertForbidden();
});

test('an admin sees the book list wired to its data endpoint', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.books.index'))
        ->assertOk()
        ->assertSee('Tambah Buku')
        ->assertSee('books/data');
});

test('the data endpoint returns each book with its category name', function () {
    $category = Category::factory()->create(['name' => 'Sains']);
    Book::factory()->for($category, 'category')->create([
        'title' => 'Biologi Molekuler',
        'author' => 'Siti Rahayu',
        'stock' => 4,
    ]);

    $response = $this->actingAs(User::factory()->admin()->create())
        ->getJson(route('admin.books.data', ['draw' => 1]));

    $response->assertOk()
        ->assertJsonPath('recordsTotal', 1)
        ->assertJsonPath('data.0.title', 'Biologi Molekuler')
        ->assertJsonPath('data.0.author', 'Siti Rahayu')
        ->assertJsonPath('data.0.category_name', 'Sains')
        ->assertJsonPath('data.0.stock', 4);

    expect($response->json('data.0.aksi'))->toContain('Ubah')->toContain('Hapus');
});

test('the data endpoint finds a book through its category name', function () {
    $sains = Category::factory()->create(['name' => 'Sains']);
    $sejarah = Category::factory()->create(['name' => 'Sejarah']);
    Book::factory()->for($sains, 'category')->create(['title' => 'Biologi Molekuler']);
    Book::factory()->for($sejarah, 'category')->create(['title' => 'Majapahit']);

    $response = $this->actingAs(User::factory()->admin()->create())
        ->getJson(route('admin.books.data', [
            'columns' => [['data' => 'category_name', 'search' => ['value' => 'Sejarah']]],
        ]));

    $response->assertOk()
        ->assertJsonPath('recordsTotal', 2)
        ->assertJsonPath('recordsFiltered', 1)
        ->assertJsonPath('data.0.title', 'Majapahit');
});

test('an admin can create a book', function () {
    $category = Category::factory()->create(['name' => 'Sains']);

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.books.store'), [
            'title' => 'Fisika Kelas X',
            'author' => 'Ahmad Hidayat',
            'description' => 'Buku teks fisika untuk SMA.',
            'stock' => 5,
            'category_id' => $category->id,
        ])
        ->assertRedirect(route('admin.books.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('books', [
        'title' => 'Fisika Kelas X',
        'stock' => 5,
        'category_id' => $category->id,
    ]);
});

test('creating a book without the required fields is rejected', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.books.store'), [
            'title' => '',
            'author' => '',
            'stock' => '',
            'category_id' => '',
        ])
        ->assertSessionHasErrors(['title', 'author', 'stock', 'category_id']);

    $this->assertDatabaseCount('books', 0);
});

test('creating a book with an unknown category is rejected', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.books.store'), [
            'title' => 'Fisika Kelas X',
            'author' => 'Ahmad Hidayat',
            'stock' => 5,
            'category_id' => 999,
        ])
        ->assertSessionHasErrors('category_id');

    $this->assertDatabaseCount('books', 0);
});

test('creating a book with a negative stock is rejected', function () {
    $category = Category::factory()->create(['name' => 'Sains']);

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.books.store'), [
            'title' => 'Fisika Kelas X',
            'author' => 'Ahmad Hidayat',
            'stock' => -1,
            'category_id' => $category->id,
        ])
        ->assertSessionHasErrors('stock');

    $this->assertDatabaseCount('books', 0);
});

test('an admin can update a book', function () {
    $oldCategory = Category::factory()->create(['name' => 'Sains']);
    $newCategory = Category::factory()->create(['name' => 'Sejarah']);
    $book = Book::factory()->for($oldCategory, 'category')->create(['title' => 'Judul Lama']);

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.books.update', $book), [
            'title' => 'Judul Baru',
            'author' => 'Penulis Baru',
            'description' => 'Deskripsi baru.',
            'stock' => 8,
            'category_id' => $newCategory->id,
        ])
        ->assertRedirect(route('admin.books.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('books', [
        'id' => $book->id,
        'title' => 'Judul Baru',
        'category_id' => $newCategory->id,
        'stock' => 8,
    ]);
});

test('an admin can delete a book', function () {
    $book = Book::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->delete(route('admin.books.destroy', $book))
        ->assertRedirect(route('admin.books.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('books', ['id' => $book->id]);
});

test('the book forms render for an admin', function () {
    $category = Category::factory()->create(['name' => 'Sains']);
    $book = Book::factory()->for($category, 'category')->create(['title' => 'Biologi Molekuler']);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.books.create'))
        ->assertOk()
        ->assertSee('Tambah Buku')
        ->assertSee('Sains');

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.books.edit', $book))
        ->assertOk()
        ->assertSee('Ubah Buku')
        ->assertSee('Biologi Molekuler');
});
