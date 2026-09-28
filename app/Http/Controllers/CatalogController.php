<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Services\LoanService;
use App\Support\DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function __construct(private readonly LoanService $loans) {}

    public function index(Request $request): View
    {
        $query = Book::query()->with('category')->orderBy('title');

        self::applyKeyword($query, $request);
        self::applyFilters($query, $request);

        return view('catalog.index', [
            'books' => $query->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = Book::query()
            ->select('books.*', 'categories.name as category_name')
            ->leftJoin('categories', 'categories.id', '=', 'books.category_id')
            ->orderBy('books.title');

        self::applyFilters($query, $request);

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
                'title_text' => $row['title'],
                'cover_url' => $row['cover_image'] ? asset('storage/'.$row['cover_image']) : null,
                'spine_color' => Book::spineColorFor($row['category_id'] ?? null),
                'title' => '<a href="'.route('catalog.show', $row['id']).'" class="hover:text-ink/70">'.e($row['title']).'</a>',
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
        if ($request->user()->isAdmin()) {
            return back()->with('error', 'Akun admin hanya dapat melihat katalog, bukan meminjam buku.');
        }

        $this->loans->borrow($request->user(), $book);

        return back()->with('success', 'Buku berhasil dipinjam.');
    }

    private static function applyKeyword(Builder $query, Request $request): void
    {
        $keyword = trim((string) $request->input('search.value'));

        if ($keyword === '') {
            return;
        }

        $query->where(function (Builder $group) use ($keyword) {
            $group->where('books.title', 'like', '%'.$keyword.'%')
                ->orWhere('books.author', 'like', '%'.$keyword.'%');
        });
    }

    private static function applyFilters(Builder $query, Request $request): void
    {
        if ($request->filled('kategori')) {
            $query->where('books.category_id', $request->integer('kategori'));
        }

        $stock = $request->string('stok')->toString();

        if ($stock === 'tersedia') {
            $query->where('books.stock', '>', 0);
        } elseif ($stock === 'habis') {
            $query->where('books.stock', 0);
        }
    }
}
