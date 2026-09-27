<?php

use App\Models\Category;
use App\Models\User;
use App\Support\DataTables;
use Illuminate\Http\Request;

test('defaults to the first page of ten rows when the request carries no parameters', function () {
    Category::factory()->count(15)->create();

    $result = DataTables::make(Category::query(), Request::create('/data'), ['id' => 'id', 'name' => 'name']);

    expect($result['draw'])->toBe(1)
        ->and($result['recordsTotal'])->toBe(15)
        ->and($result['recordsFiltered'])->toBe(15)
        ->and($result['data'])->toHaveCount(10);
});

test('returns only the requested slice of rows', function () {
    Category::factory()->count(15)->create();

    $result = DataTables::make(
        Category::query(),
        Request::create('/data', 'GET', ['start' => 10, 'length' => 10]),
        ['id' => 'id', 'name' => 'name'],
    );

    expect($result['data'])->toHaveCount(5);
});

test('caps the page length at one hundred rows', function () {
    User::factory()->count(105)->create();

    $result = DataTables::make(
        User::query(),
        Request::create('/data', 'GET', ['length' => 1000]),
        ['name' => 'name'],
    );

    expect($result['recordsTotal'])->toBe(105)
        ->and($result['data'])->toHaveCount(DataTables::MAX_LENGTH);
});

test('echoes the draw value that identifies the request', function () {
    $result = DataTables::make(Category::query(), Request::create('/data', 'GET', ['draw' => 42]), []);

    expect($result['draw'])->toBe(42);
});

test('filters rows with the global search value', function () {
    Category::factory()->create(['name' => 'Fiksi']);
    Category::factory()->create(['name' => 'Fiksi Sastra']);
    Category::factory()->create(['name' => 'Sejarah']);

    $result = DataTables::make(
        Category::query(),
        Request::create('/data', 'GET', ['search' => ['value' => 'Fiksi']]),
        ['name' => 'name'],
    );

    expect($result['recordsTotal'])->toBe(3)
        ->and($result['recordsFiltered'])->toBe(2)
        ->and(array_column($result['data'], 'name'))->each->toContain('Fiksi');
});

test('ignores a column filter whose column is not in the allowed list', function () {
    Category::factory()->create(['name' => 'Fiksi']);
    Category::factory()->create(['name' => 'Sejarah']);

    $result = DataTables::make(
        Category::query(),
        Request::create('/data', 'GET', [
            'columns' => [
                ['data' => 'title', 'search' => ['value' => 'Fiksi']],
            ],
        ]),
        ['name' => 'name'],
    );

    expect($result['recordsFiltered'])->toBe(2);
});

test('appends a derived key to every row when a callback is given', function () {
    Category::factory()->create(['name' => 'Fiksi']);

    $result = DataTables::make(
        Category::query(),
        Request::create('/data'),
        ['name' => 'name'],
        each: fn (array $row) => [...$row, 'aksi' => '<button>Ubah</button>'],
    );

    expect($result['data'][0]['aksi'])->toBe('<button>Ubah</button>');
});

test('orders rows by the requested column and direction', function () {
    Category::factory()->create(['name' => 'Cakra']);
    Category::factory()->create(['name' => 'Anggrek']);
    Category::factory()->create(['name' => 'Bambu']);

    $parameters = [
        'columns' => [['data' => 'id'], ['data' => 'name']],
        'order' => [['column' => 1, 'dir' => 'asc']],
    ];

    $ascending = DataTables::make(
        Category::query(),
        Request::create('/data', 'GET', $parameters),
        ['id' => 'id', 'name' => 'name'],
    );

    expect(array_column($ascending['data'], 'name'))->toBe(['Anggrek', 'Bambu', 'Cakra']);

    $parameters['order'] = [['column' => 1, 'dir' => 'desc']];

    $descending = DataTables::make(
        Category::query(),
        Request::create('/data', 'GET', $parameters),
        ['id' => 'id', 'name' => 'name'],
    );

    expect(array_column($descending['data'], 'name'))->toBe(['Cakra', 'Bambu', 'Anggrek']);
});

test('ignores an order whose column is not in the allowed list', function () {
    Category::factory()->count(3)->create();

    $result = DataTables::make(
        Category::query(),
        Request::create('/data', 'GET', [
            'columns' => [['data' => 'password']],
            'order' => [['column' => 0, 'dir' => 'asc']],
        ]),
        ['name' => 'name'],
    );

    expect($result['recordsFiltered'])->toBe(3);
});
