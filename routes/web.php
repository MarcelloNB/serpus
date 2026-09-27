<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\LoanHistoryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('non-admin')->group(function () {
    Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
    Route::get('/books/data', [CatalogController::class, 'data'])->name('catalog.data');
    Route::get('/books/{book}', [CatalogController::class, 'show'])->name('catalog.show');
});

Route::get('/dashboard', function (Request $request) {
    return redirect()->to(
        $request->user()->isAdmin() ? route('admin.dashboard') : route('catalog.index'),
    );
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'non-admin'])->group(function () {
    Route::get('/loans', [LoanHistoryController::class, 'index'])->name('loans.index');
    Route::get('/loans/data', [LoanHistoryController::class, 'data'])->name('loans.data');

    Route::post('/books/{book}/borrow', [CatalogController::class, 'borrow'])->name('catalog.borrow');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
