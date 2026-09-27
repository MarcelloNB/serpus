<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'totalBooks' => Book::count(),
            'totalCategories' => Category::count(),
            'totalUsers' => User::count(),
            'latestBooks' => Book::with('category')->latest()->take(4)->get(),
        ]);
    }
}
