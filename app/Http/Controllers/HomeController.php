<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $heroSlides = Book::whereNotNull('cover_image')->latest()->take(5)->get()
            ->map(fn (Book $book): array => [
                'image' => asset('storage/'.$book->cover_image),
                'style' => null,
            ])
            ->values();

        if ($heroSlides->isEmpty()) {
            $heroSlides = collect([
                ['image' => null, 'style' => 'linear-gradient(135deg, #1F4D3A 0%, #14352A 100%)'],
                ['image' => null, 'style' => 'linear-gradient(135deg, #2E6E6B 0%, #1F4D3A 100%)'],
                ['image' => null, 'style' => 'linear-gradient(135deg, #6B4E7A 0%, #22302A 100%)'],
            ]);
        }

        return view('home', [
            'heroSlides' => $heroSlides,
            'popularBooks' => Book::with('category')
                ->whereHas('loans')
                ->withCount('loans')
                ->orderByDesc('loans_count')
                ->take(10)
                ->get(),
            'latestBooks' => Book::with('category')->latest()->take(4)->get(),
        ]);
    }
}
