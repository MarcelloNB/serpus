<?php

use App\Models\Book;
use App\Models\User;

test('the landing page shows the hero carousel, sections and the latest books', function () {
    Book::factory()->create(['title' => 'Biologi Molekuler']);

    $this->get('/')
        ->assertOk()
        ->assertSee('SerPus — Sistem Elektronik Perpustakaan')
        ->assertSee('data-carousel', false)
        ->assertSee('Cukup dari browser, kapan saja.')
        ->assertSee('Buku Terpopuler')
        ->assertSee('Berita Kegiatan Perpustakaan')
        ->assertSee('Buku Terbaru')
        ->assertSee('Biologi Molekuler')
        ->assertSee('Daftar Sekarang');
});

test('the landing page greets a logged-in peminjam with the catalog link', function () {
    $this->actingAs(User::factory()->create())
        ->get('/')
        ->assertOk()
        ->assertSee('Buka Katalog')
        ->assertDontSee('Daftar Sekarang');
});

test('an admin can open the landing page', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/')
        ->assertOk()
        ->assertSee('Buka Dashboard')
        ->assertDontSee('Daftar Sekarang');
});

test('the landing page shows an empty message without books', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Belum ada buku di katalog.');
});

test('the public navbar links the landing page and the catalog', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('href="'.route('catalog.index').'"', false);

    $this->get(route('catalog.index'))
        ->assertOk()
        ->assertSee('href="'.route('home').'"', false);
});
