<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Category;
use App\Support\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(): View
    {
        return view('admin.books.index');
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
                'id' => 'books.id',
                'title' => 'books.title',
                'author' => 'books.author',
                'category_name' => 'categories.name',
                'stock' => 'books.stock',
            ],
            each: fn (array $row) => [
                ...$row,
                'aksi' => view('admin.books.row-actions', $row)->render(),
            ],
        );

        return response()->json($result);
    }

    public function create(): View
    {
        return view('admin.books.create', [
            'book' => null,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(StoreBookRequest $request): RedirectResponse
    {
        Book::create($request->validated());

        return to_route('admin.books.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit(Book $book): View
    {
        return view('admin.books.edit', [
            'book' => $book,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $book->update($request->validated());

        return to_route('admin.books.index')->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $book->delete();

        return to_route('admin.books.index')->with('success', 'Buku berhasil dihapus.');
    }
}
