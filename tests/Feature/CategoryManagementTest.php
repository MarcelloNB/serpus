<?php

use App\Models\Category;
use App\Models\User;

test('guests are redirected to login from the category routes', function () {
    $category = Category::factory()->create();

    $this->get(route('admin.categories.index'))->assertRedirect(route('login'));
    $this->get(route('admin.categories.data'))->assertRedirect(route('login'));
    $this->get(route('admin.categories.create'))->assertRedirect(route('login'));
    $this->get(route('admin.categories.edit', $category))->assertRedirect(route('login'));
    $this->post(route('admin.categories.store'))->assertRedirect(route('login'));
    $this->put(route('admin.categories.update', $category))->assertRedirect(route('login'));
    $this->delete(route('admin.categories.destroy', $category))->assertRedirect(route('login'));
});

test('a peminjam is refused on the category routes', function () {
    $category = Category::factory()->create();

    $this->actingAs(User::factory()->create());

    $this->get(route('admin.categories.index'))->assertForbidden();
    $this->get(route('admin.categories.data'))->assertForbidden();
    $this->get(route('admin.categories.create'))->assertForbidden();
    $this->get(route('admin.categories.edit', $category))->assertForbidden();
    $this->post(route('admin.categories.store'))->assertForbidden();
    $this->put(route('admin.categories.update', $category))->assertForbidden();
    $this->delete(route('admin.categories.destroy', $category))->assertForbidden();
});

test('an admin sees the category list wired to its data endpoint', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.categories.index'))
        ->assertOk()
        ->assertSee('Tambah Kategori')
        ->assertSee('admin/categories/data');
});

test('the data endpoint returns the rows behind the table', function () {
    Category::factory()->create(['name' => 'Fiksi']);
    Category::factory()->create(['name' => 'Sejarah']);

    $response = $this->actingAs(User::factory()->admin()->create())
        ->getJson(route('admin.categories.data', ['draw' => 3, 'start' => 0, 'length' => 10]));

    $response->assertOk()
        ->assertJsonPath('draw', 3)
        ->assertJsonPath('recordsTotal', 2)
        ->assertJsonPath('recordsFiltered', 2);

    expect(array_column($response->json('data'), 'name'))->toEqualCanonicalizing(['Fiksi', 'Sejarah'])
        ->and($response->json('data.0.aksi'))->toContain('Ubah')->toContain('Hapus');
});

test('an admin can create a category', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.categories.store'), ['name' => 'Filsafat'])
        ->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('categories', ['name' => 'Filsafat']);
});

test('creating a category without a name is rejected', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.categories.store'), ['name' => ''])
        ->assertSessionHasErrors('name');

    $this->assertDatabaseCount('categories', 0);
});

test('a field with a validation error gets the red border', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->withSession(['errors' => [
            'default' => [
                'format' => ':message',
                'messages' => ['name' => ['Nama kategori wajib diisi.']],
            ],
        ]])
        ->get(route('admin.categories.create'))
        ->assertOk()
        ->assertSee('border-red-300');
});

test('creating a category with a name that already exists is rejected', function () {
    Category::factory()->create(['name' => 'Fiksi']);

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.categories.store'), ['name' => 'Fiksi'])
        ->assertSessionHasErrors('name');

    $this->assertDatabaseCount('categories', 1);
});

test('an admin can rename a category', function () {
    $category = Category::factory()->create(['name' => 'Fiksi']);

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.categories.update', $category), ['name' => 'Fiksi Revisi'])
        ->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Fiksi Revisi']);
});

test('renaming a category to another category name is rejected', function () {
    Category::factory()->create(['name' => 'Fiksi']);
    $other = Category::factory()->create(['name' => 'Sejarah']);

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.categories.update', $other), ['name' => 'Fiksi'])
        ->assertSessionHasErrors('name');

    $this->assertDatabaseHas('categories', ['id' => $other->id, 'name' => 'Sejarah']);
});

test('saving a category without changing its name is accepted', function () {
    $category = Category::factory()->create(['name' => 'Fiksi']);

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.categories.update', $category), ['name' => 'Fiksi'])
        ->assertRedirect(route('admin.categories.index'))
        ->assertSessionDoesntHaveErrors();
});

test('an admin can delete a category', function () {
    $category = Category::factory()->create(['name' => 'Fiksi']);

    $this->actingAs(User::factory()->admin()->create())
        ->delete(route('admin.categories.destroy', $category))
        ->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

test('the category forms render for an admin', function () {
    $category = Category::factory()->create(['name' => 'Fiksi']);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.categories.create'))
        ->assertOk()
        ->assertSee('Tambah Kategori');

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.categories.edit', $category))
        ->assertOk()
        ->assertSee('Ubah Kategori')
        ->assertSee('Fiksi');
});
