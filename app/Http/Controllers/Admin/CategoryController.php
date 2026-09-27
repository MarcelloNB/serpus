<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Support\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index');
    }

    public function data(Request $request): JsonResponse
    {
        $result = DataTables::make(
            Category::query(),
            $request,
            ['id' => 'id', 'name' => 'name'],
            each: fn (array $row) => [
                ...$row,
                'aksi' => view('admin.categories.row-actions', $row)->render(),
            ],
        );

        return response()->json($result);
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        Category::create($request->validated());

        return to_route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return to_route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return to_route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
