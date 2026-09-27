<?php

use App\Models\Book;
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
        ->assertDontSee('Katalog Buku');

    $this->actingAs(User::factory()->create())
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertDontSee('Dashboard')
        ->assertDontSee('Administrator')
        ->assertSee('Katalog Buku');
});

test('an admin is refused on the peminjam routes', function () {
    $admin = User::factory()->admin()->create();
    $book = Book::factory()->create();

    $this->actingAs($admin)->get(route('catalog.index'))->assertForbidden();
    $this->actingAs($admin)->get(route('catalog.data'))->assertForbidden();
    $this->actingAs($admin)->get(route('catalog.show', $book))->assertForbidden();
    $this->actingAs($admin)->post(route('catalog.borrow', $book))->assertForbidden();
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
