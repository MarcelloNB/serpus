<?php

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;

test('guests are redirected to login from the loan routes', function () {
    $loan = Loan::factory()->create();

    $this->get(route('admin.loans.index'))->assertRedirect(route('login'));
    $this->get(route('admin.loans.data'))->assertRedirect(route('login'));
    $this->get(route('admin.loans.create'))->assertRedirect(route('login'));
    $this->post(route('admin.loans.store'))->assertRedirect(route('login'));
    $this->patch(route('admin.loans.return', $loan))->assertRedirect(route('login'));
});

test('a peminjam is refused on the loan routes', function () {
    $loan = Loan::factory()->create();

    $this->actingAs(User::factory()->create());

    $this->get(route('admin.loans.index'))->assertForbidden();
    $this->get(route('admin.loans.data'))->assertForbidden();
    $this->get(route('admin.loans.create'))->assertForbidden();
    $this->post(route('admin.loans.store'))->assertForbidden();
    $this->patch(route('admin.loans.return', $loan))->assertForbidden();
});

test('an admin sees the loan list wired to its data endpoint', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.loans.index'))
        ->assertOk()
        ->assertSee('Tambah Peminjaman')
        ->assertSee('loans/data');
});

test('the data endpoint returns each loan with borrower, book and status', function () {
    $borrower = User::factory()->create(['name' => 'Budi Santoso']);
    $book = Book::factory()->create(['title' => 'Biologi Molekuler']);
    Loan::factory()->for($borrower, 'user')->for($book, 'book')->create();

    $response = $this->actingAs(User::factory()->admin()->create())
        ->getJson(route('admin.loans.data', ['draw' => 1]));

    $response->assertOk()
        ->assertJsonPath('recordsTotal', 1)
        ->assertJsonPath('data.0.user_name', 'Budi Santoso')
        ->assertJsonPath('data.0.book_title', 'Biologi Molekuler')
        ->assertJsonPath('data.0.returned_at_label', '-');

    expect($response->json('data.0.status_label'))->toContain('Dipinjam')->toContain('bg-amber-100')
        ->and($response->json('data.0.aksi'))->toContain('Kembalikan');
});

test('the data endpoint hides the return button for a returned loan', function () {
    Loan::factory()->returned()->create();

    $response = $this->actingAs(User::factory()->admin()->create())
        ->getJson(route('admin.loans.data'));

    expect($response->json('data.0.status_label'))->toContain('Dikembalikan')
        ->and($response->json('data.0.aksi'))->toContain('Selesai')->not->toContain('Kembalikan');
});

test('an admin can record a loan and the book stock drops', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create(['stock' => 3]);

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.loans.store'), ['user_id' => $user->id, 'book_id' => $book->id])
        ->assertRedirect(route('admin.loans.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('loans', [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'status' => 'dipinjam',
    ]);

    expect($book->fresh()->stock)->toBe(2)
        ->and(Loan::firstOrFail()->due_at->toDateString())->toBe(now()->addDays(14)->toDateString());
});

test('recording a loan beyond the five-book limit is rejected', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create(['stock' => 3]);
    Loan::factory()->count(5)->for($user, 'user')->create();

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.loans.store'), ['user_id' => $user->id, 'book_id' => $book->id])
        ->assertSessionHasErrors('book_id');

    expect(Loan::where('user_id', $user->getKey())->count())->toBe(5)
        ->and($book->fresh()->stock)->toBe(3);
});

test('recording a loan without a borrower or a book is rejected', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.loans.store'), ['user_id' => '', 'book_id' => ''])
        ->assertSessionHasErrors(['user_id', 'book_id']);

    $this->assertDatabaseCount('loans', 0);
});

test('recording a loan for an unknown book is rejected', function () {
    $user = User::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.loans.store'), ['user_id' => $user->id, 'book_id' => 999])
        ->assertSessionHasErrors('book_id');

    $this->assertDatabaseCount('loans', 0);
});

test('recording a loan for an out-of-stock book is rejected', function () {
    $user = User::factory()->create();
    $book = Book::factory()->outOfStock()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.loans.store'), ['user_id' => $user->id, 'book_id' => $book->id])
        ->assertSessionHasErrors('book_id');

    $this->assertDatabaseCount('loans', 0);
    expect($book->fresh()->stock)->toBe(0);
});

test('an admin can mark a book returned and the stock rises', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create(['stock' => 1]);

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.loans.store'), ['user_id' => $user->id, 'book_id' => $book->id]);

    $loan = Loan::firstOrFail();

    $this->actingAs(User::factory()->admin()->create())
        ->patch(route('admin.loans.return', $loan))
        ->assertRedirect(route('admin.loans.index'))
        ->assertSessionHas('success');

    $loan->refresh();
    expect($loan->status->value)->toBe('dikembalikan')
        ->and($loan->returned_at)->not->toBeNull()
        ->and($book->fresh()->stock)->toBe(1);
});

test('returning the same loan twice is rejected and the stock is not restored again', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create(['stock' => 3]);
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.loans.store'), ['user_id' => $user->id, 'book_id' => $book->id]);

    $loan = Loan::firstOrFail();

    $this->actingAs($admin)->patch(route('admin.loans.return', $loan));
    expect($book->fresh()->stock)->toBe(3);

    $this->actingAs($admin)
        ->patch(route('admin.loans.return', $loan))
        ->assertSessionHasErrors('status');

    expect($book->fresh()->stock)->toBe(3)
        ->and($loan->fresh()->status->value)->toBe('dikembalikan');
});

test('the loan form renders for an admin', function () {
    User::factory()->create(['name' => 'Budi Santoso']);
    Book::factory()->create(['title' => 'Biologi Molekuler', 'stock' => 3]);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.loans.create'))
        ->assertOk()
        ->assertSee('Tambah Peminjaman')
        ->assertSee('Budi Santoso')
        ->assertSee('Biologi Molekuler');
});

test('the admin loans data endpoint shows the due date and the overdue badge', function () {
    $borrower = User::factory()->create();
    $book = Book::factory()->create();
    $active = Loan::factory()->for($borrower, 'user')->for($book, 'book')->create([
        'borrowed_at' => now()->subDay(),
        'due_at' => now()->addDays(13),
    ]);
    $late = Loan::factory()->overdue()->for($borrower, 'user')->for($book, 'book')->create();

    $rows = collect($this->actingAs(User::factory()->admin()->create())
        ->getJson(route('admin.loans.data', ['draw' => 1]))
        ->assertOk()
        ->json('data'));

    expect($rows->firstWhere('id', $active->id)['due_at_label'])->not->toContain('Terlambat')
        ->and($rows->firstWhere('id', $active->id)['due_at_label'])->not->toBe('-')
        ->and($rows->firstWhere('id', $active->id)['borrowed_at_label'])
        ->toBe($active->borrowed_at->format('d/m/Y H:i'))
        ->and($rows->firstWhere('id', $late->id)['due_at_label'])->toContain('Terlambat');
});
