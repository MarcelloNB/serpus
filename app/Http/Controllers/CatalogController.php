<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\LoanService;
use App\Support\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function __construct(private readonly LoanService $loans) {}

    public function index(): View
    {
        return view('catalog.index');
    }

    public function data(Request $request): JsonResponse
    {
        $query = Book::query()
            ->select('books.*', 'categories.name as category_name')
            ->leftJoin('categories', 'categories.id', '=', 'books.category_id');

        $result = DataTables::make(
            $query,
            $request,
            [
                'title' => 'books.title',
                'author' => 'books.author',
                'category_name' => 'categories.name',
                'stock' => 'books.stock',
            ],
            each: fn (array $row) => [
                ...$row,
                'title' => '<a href="'.route('catalog.show', $row['id']).'" class="font-semibold underline-offset-4 hover:underline">'.e($row['title']).'</a>',
                'stock' => view('components.stock-badge', ['stock' => $row['stock']])->render(),
                'aksi' => view('components.borrow-button', ['id' => $row['id'], 'stock' => $row['stock']])->render(),
            ],
        );

        return response()->json($result);
    }

    public function show(Book $book): View
    {
        return view('catalog.show', [
            'book' => $book->load('category'),
        ]);
    }

    public function borrow(Request $request, Book $book): RedirectResponse
    {
        $this->loans->borrow($request->user(), $book);

        return back()->with('success', 'Buku berhasil dipinjam.');
    }
}
