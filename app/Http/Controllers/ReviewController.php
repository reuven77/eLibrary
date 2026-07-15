<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Book $book): RedirectResponse
    {
        $user = $request->user();

        $eligible = Loan::query()
            ->where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', 'dikembalikan')
            ->exists();

        $isDigitalReadable = $book->isDigital();

        if (! $eligible && ! $isDigitalReadable) {
            return back()->with('error', 'Ulasan hanya setelah mengembalikan buku fisik, atau untuk koleksi digital.');
        }

        Review::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'book_id' => $book->id,
            ],
            [
                'rating' => $request->validated('rating'),
                'comment' => $request->validated('comment'),
            ]
        );

        return back()->with('success', 'Ulasan tersimpan.');
    }
}
