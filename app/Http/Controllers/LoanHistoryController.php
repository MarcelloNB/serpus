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
                'returned_at_label' => 'loans.returned_at',
                'status_label' => 'loans.status',
            ],
            each: fn (array $row) => [
                ...$row,
                'borrowed_at_label' => self::formatDate($row['borrowed_at']),
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
        return $value === null ? '-' : Carbon::parse($value)->format('d/m/Y H:i');
    }
}
