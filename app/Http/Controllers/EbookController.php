<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EbookController extends Controller
{
    public function show(Book $book): View
    {
        abort_unless($book->isDigital() && $book->file_path, 404);

        return view('pages.ebooks.show', [
            'book' => $book->load(['author:id,name', 'category:id,name,slug']),
        ]);
    }

    public function file(Book $book): StreamedResponse|Response
    {
        abort_unless($book->isDigital() && $book->file_path, 404);
        abort_unless(Storage::disk('public')->exists($book->file_path), 404);

        return Storage::disk('public')->response($book->file_path);
    }
}
