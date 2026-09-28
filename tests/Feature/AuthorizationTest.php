<?php

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;

test('guests are redirected to login before reaching the admin area', function () {
    $this->get('/admin')->assertRedirect(route('login'));
});

test('a peminjam opening an admin url directly is refused', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertForbidden();
});

test('an admin can open the admin dashboard', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin')
        ->assertOk()
        ->assertSee('Dashboard Admin');
});

test('the dashboard route sends each role to its own home', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('dashboard'))
        ->assertRedirect(route('admin.dashboard'));

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertRedirect(route('catalog.index'));
});

test('the admin menu is only rendered for admins', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertSee('Dashboard')
        ->assertSee('Administrator')
        ->assertSee('Katalog Buku')
        ->assertDontSee('Riwayat Peminjaman');

    $this->actingAs(User::factory()->create())
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertDontSee('Dashboard')
        ->assertDontSee('Administrator')
        ->assertSee('Katalog Buku');
});

test('an admin can browse the catalog but never borrow', function () {
    $admin = User::factory()->admin()->create();
    $book = Book::factory()->create(['stock' => 5]);

    $this->actingAs($admin)->get(route('catalog.index'))->assertOk();
    $this->actingAs($admin)->get(route('catalog.data'))->assertOk();
    $this->actingAs($admin)->get(route('catalog.show', $book))->assertOk();

    $row = $this->actingAs($admin)->getJson(route('catalog.data'))->json('data.0');
    expect($row['aksi'])->toContain('Khusus peminjam');

    $this->actingAs($admin)
        ->from(route('catalog.index'))
        ->post(route('catalog.borrow', $book))
        ->assertRedirect(route('catalog.index'))
        ->assertSessionHas('error');

    expect($book->fresh()->stock)->toBe(5)
        ->and(Loan::count())->toBe(0);
});

test('an admin is refused on the peminjam loan history routes', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('loans.index'))->assertForbidden();
    $this->actingAs($admin)->get(route('loans.data'))->assertForbidden();
});

test('a guest and a peminjam can open the peminjam routes', function () {
    $book = Book::factory()->create();

    $this->get(route('catalog.index'))->assertOk();
    $this->get(route('catalog.show', $book))->assertOk();
    $this->get(route('loans.index'))->assertRedirect(route('login'));

    $this->actingAs(User::factory()->create())->get(route('loans.index'))->assertOk();
});
