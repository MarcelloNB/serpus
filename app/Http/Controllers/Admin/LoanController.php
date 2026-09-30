<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LoanStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanRequest;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use App\Services\LoanService;
use App\Support\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function __construct(private readonly LoanService $loans) {}

    public function index(): View
    {
        return view('admin.loans.index');
    }

    public function data(Request $request): JsonResponse
    {
        $query = Loan::query()
            ->leftJoin('users', 'users.id', '=', 'loans.user_id')
            ->leftJoin('books', 'books.id', '=', 'loans.book_id')
            ->select('loans.*', 'users.name as user_name', 'books.title as book_title');

        $result = DataTables::make(
            $query,
            $request,
            [
                'id' => 'loans.id',
                'user_name' => 'users.name',
                'book_title' => 'books.title',
                'borrowed_at_label' => 'loans.borrowed_at',
                'due_at_label' => 'loans.due_at',
                'returned_at_label' => 'loans.returned_at',
                'status_label' => 'loans.status',
            ],
            each: fn (array $row) => [
                ...$row,
                'user_name' => e($row['user_name']),
                'book_title' => e($row['book_title']),
                'borrowed_at_label' => self::formatDate($row['borrowed_at']),
                'due_at_label' => view('components.due-date-badge', [
                    'label' => self::formatDate($row['due_at']),
                    'overdue' => $row['status'] === LoanStatus::Dipinjam->value
                        && $row['due_at'] !== null
                        && Carbon::parse($row['due_at'])->isPast(),
                ])->render(),
                'returned_at_label' => self::formatDate($row['returned_at']),
                'status_label' => view('components.loan-status-badge', [
                    'status' => LoanStatus::from($row['status']),
                ])->render(),
                'aksi' => view('admin.loans.row-actions', $row)->render(),
            ],
        );

        return response()->json($result);
    }

    public function create(): View
    {
        return view('admin.loans.create', [
            'users' => User::orderBy('name')->get(),
            'books' => Book::orderBy('title')->get(),
        ]);
    }

    public function store(StoreLoanRequest $request): RedirectResponse
    {
        $attributes = $request->validated();

        $this->loans->borrow(
            User::findOrFail($attributes['user_id']),
            Book::findOrFail($attributes['book_id']),
        );

        return to_route('admin.loans.index')->with('success', 'Peminjaman buku berhasil dicatat.');
    }

    public function returnLoan(Loan $loan): RedirectResponse
    {
        $this->loans->processReturn($loan);

        return to_route('admin.loans.index')->with('success', 'Pengembalian buku berhasil dicatat.');
    }

    private static function formatDate(?string $value): string
    {
        return $value === null
            ? '-'
            : Carbon::parse($value)->setTimezone(config('app.timezone'))->format('d/m/Y H:i');
    }
}
