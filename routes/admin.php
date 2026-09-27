<?php

use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('categories/data', [CategoryController::class, 'data'])->name('categories.data');
    Route::resource('categories', CategoryController::class)->except(['show']);

    Route::get('books/data', [BookController::class, 'data'])->name('books.data');
    Route::resource('books', BookController::class)->except(['show']);

    Route::get('users/data', [UserController::class, 'data'])->name('users.data');
    Route::resource('users', UserController::class)->except(['show']);

    Route::get('loans/data', [LoanController::class, 'data'])->name('loans.data');
    Route::patch('loans/{loan}/return', [LoanController::class, 'returnLoan'])->name('loans.return');
    Route::resource('loans', LoanController::class)->only(['index', 'create', 'store']);
});
