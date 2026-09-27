<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('guests are redirected to login from the user routes', function () {
    $user = User::factory()->create();

    $this->get(route('admin.users.index'))->assertRedirect(route('login'));
    $this->get(route('admin.users.data'))->assertRedirect(route('login'));
    $this->get(route('admin.users.create'))->assertRedirect(route('login'));
    $this->get(route('admin.users.edit', $user))->assertRedirect(route('login'));
    $this->post(route('admin.users.store'))->assertRedirect(route('login'));
    $this->put(route('admin.users.update', $user))->assertRedirect(route('login'));
    $this->delete(route('admin.users.destroy', $user))->assertRedirect(route('login'));
});

test('a peminjam is refused on the user routes', function () {
    $user = User::factory()->create();

    $this->actingAs(User::factory()->create());

    $this->get(route('admin.users.index'))->assertForbidden();
    $this->get(route('admin.users.data'))->assertForbidden();
    $this->get(route('admin.users.create'))->assertForbidden();
    $this->get(route('admin.users.edit', $user))->assertForbidden();
    $this->post(route('admin.users.store'))->assertForbidden();
    $this->put(route('admin.users.update', $user))->assertForbidden();
    $this->delete(route('admin.users.destroy', $user))->assertForbidden();
});

test('an admin sees the user list wired to its data endpoint', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('Tambah Pengguna')
        ->assertSee('users/data');
});

test('the user forms offer password helpers', function () {
    $user = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.users.create'))
        ->assertOk()
        ->assertSee('Buatkan')
        ->assertSee('Tampilkan kata sandi')
        ->assertSee('Minimal 8 karakter.');

    $this->actingAs($admin)
        ->get(route('admin.users.edit', $user))
        ->assertOk()
        ->assertSee('Buatkan')
        ->assertSee('Kosongkan kolom ini jika tidak ingin mengganti password.');
});

test('the data endpoint returns each user with their role label', function () {
    $admin = User::factory()->admin()->create();
    $peminjam = User::factory()->create();

    $response = $this->actingAs($admin)
        ->getJson(route('admin.users.data', ['draw' => 1]));

    $response->assertOk()->assertJsonPath('recordsTotal', 2);

    $labels = array_column($response->json('data'), 'role_label', 'email');
    expect($labels[$admin->email])->toContain('Administrator')
        ->and($labels[$peminjam->email])->toContain('Peminjam');

    $actions = array_column($response->json('data'), 'aksi', 'email');
    expect($actions[$admin->email])->not->toContain('Hapus')
        ->and($actions[$peminjam->email])->toContain('Hapus');
});

test('an admin can create a user', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.users.store'), [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.test',
            'password' => 'rahasia123',
            'role' => 'peminjam',
        ])
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', ['email' => 'budi@example.test', 'role' => 'peminjam']);

    expect(Hash::check('rahasia123', User::where('email', 'budi@example.test')->first()->password))->toBeTrue();
});

test('creating a user without the required fields is rejected', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.users.store'), ['name' => '', 'email' => '', 'password' => '', 'role' => ''])
        ->assertSessionHasErrors(['name', 'email', 'password', 'role']);

    $this->assertDatabaseCount('users', 1);
});

test('creating a user with a malformed email is rejected', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.users.store'), [
            'name' => 'Budi',
            'email' => 'bukan-email',
            'password' => 'rahasia123',
            'role' => 'peminjam',
        ])
        ->assertSessionHasErrors('email');
});

test('creating a user with an email that already exists is rejected', function () {
    User::factory()->create(['email' => 'budi@example.test']);

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.users.store'), [
            'name' => 'Budi Lain',
            'email' => 'budi@example.test',
            'password' => 'rahasia123',
            'role' => 'peminjam',
        ])
        ->assertSessionHasErrors('email');
});

test('creating a user with an unknown role is rejected', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.users.store'), [
            'name' => 'Budi',
            'email' => 'budi@example.test',
            'password' => 'rahasia123',
            'role' => 'superuser',
        ])
        ->assertSessionHasErrors(['role' => 'Peran tidak valid.']);
});

test('an admin can update a user without touching the password', function () {
    $user = User::factory()->create(['password' => 'password']);

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.users.update', $user), [
            'name' => 'Nama Baru',
            'email' => 'baru@example.test',
            'password' => '',
            'role' => 'admin',
        ])
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Nama Baru',
        'email' => 'baru@example.test',
        'role' => 'admin',
    ]);

    expect(Hash::check('password', $user->fresh()->password))->toBeTrue();
});

test('an admin can change a user password', function () {
    $user = User::factory()->create(['password' => 'password']);

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'barusaja99',
            'role' => 'peminjam',
        ])
        ->assertRedirect(route('admin.users.index'));

    expect(Hash::check('barusaja99', $user->fresh()->password))->toBeTrue()
        ->and(Hash::check('password', $user->fresh()->password))->toBeFalse();
});

test('updating a user to an email that already exists is rejected', function () {
    User::factory()->create(['email' => 'budi@example.test']);
    $user = User::factory()->create(['email' => 'andi@example.test']);

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => 'budi@example.test',
            'role' => 'peminjam',
        ])
        ->assertSessionHasErrors('email');

    $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => 'andi@example.test']);
});

test('an admin cannot delete their own account', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $admin))
        ->assertRedirect()
        ->assertSessionHas('error');

    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});

test('an admin can delete another user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $user))
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

test('the user forms render for an admin', function () {
    $user = User::factory()->create(['name' => 'Budi Santoso']);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.users.create'))
        ->assertOk()
        ->assertSee('Tambah Pengguna')
        ->assertSee('Administrator');

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.users.edit', $user))
        ->assertOk()
        ->assertSee('Ubah Pengguna')
        ->assertSee('Budi Santoso');
});
