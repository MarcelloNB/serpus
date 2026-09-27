<?php

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
        ->assertSee('Administrator');

    $this->actingAs(User::factory()->create())
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertDontSee('Dashboard')
        ->assertDontSee('Administrator');
});
