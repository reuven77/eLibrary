<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\EbookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/katalog', [CatalogController::class, 'index'])
    ->middleware('throttle:60,1')
    ->name('catalog.index');

Route::get('/katalog/{book}', [CatalogController::class, 'show'])->name('catalog.show');

Route::middleware(['auth'])->group(function () {
    Route::get('/pinjaman', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/pinjaman/{loan}/cetak', [LoanController::class, 'printReceipt'])->name('loans.print');
    Route::get('/katalog/{book}/pinjam', [LoanController::class, 'create'])
        ->name('loans.create');
    Route::post('/katalog/{book}/pinjam', [LoanController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('loans.store');

    Route::post('/katalog/{book}/ulasan', [ReviewController::class, 'store'])
        ->middleware('throttle:20,1')
        ->name('reviews.store');

    Route::get('/baca/{book}', [EbookController::class, 'show'])->name('ebooks.show');
    Route::get('/baca/{book}/file', [EbookController::class, 'file'])->name('ebooks.file');

    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user?->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('home');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
