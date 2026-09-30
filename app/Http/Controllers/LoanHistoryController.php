<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Models\Loan;
use App\Support\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class LoanHistoryController extends Controller
{
    public function index(): View
    {
        return view('loans.index');
    }

    public function data(Request $request): JsonResponse
    {
        $query = Loan::query()
            ->whereBelongsTo($request->user())
            ->leftJoin('books', 'books.id', '=', 'loans.book_id')
            ->select('loans.*', 'books.title as book_title');

        $result = DataTables::make(
            $query,
            $request,
            [
                'book_title' => 'books.title',
                'borrowed_at_label' => 'loans.borrowed_at',
                'due_at_label' => 'loans.due_at',
                'returned_at_label' => 'loans.returned_at',
                'status_label' => 'loans.status',
            ],
            each: fn (array $row) => [
                ...$row,
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
            ],
        );

        return response()->json($result);
    }

    private static function formatDate(?string $value): string
    {
        return $value === null
            ? '-'
            : Carbon::parse($value)->setTimezone(config('app.timezone'))->format('d/m/Y H:i');
    }
}
