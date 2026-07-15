<?php

use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::resource('books', BookController::class)->except(['show']);

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
Route::get('/loans/pending', [LoanController::class, 'indexPending'])->name('loans.pending');
Route::get('/loans/{loan}/return', [LoanController::class, 'showReturn'])->name('loans.return.show');
Route::post('/loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
Route::post('/loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
Route::post('/loans/{loan}/return', [LoanController::class, 'returnBook'])->name('loans.return');
