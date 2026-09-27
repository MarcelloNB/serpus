<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LoanStatus;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'totalBooks' => Book::count(),
            'activeLoans' => Loan::where('status', LoanStatus::Dipinjam)->count(),
            'totalUsers' => User::count(),
            'latestLoans' => Loan::query()
                ->with(['user', 'book'])
                ->latest('borrowed_at')
                ->take(5)
                ->get(),
        ]);
    }
}
