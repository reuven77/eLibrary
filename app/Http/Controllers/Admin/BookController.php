<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBookRequest;
use App\Http\Requests\Admin\UpdateBookRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Services\BookAdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookController extends Controller
{
    public function __construct(private readonly BookAdminService $books)
    {
    }

    public function index(): View
    {
        $this->authorize('viewAny', Book::class);

        $books = Book::query()
            ->selectCatalogColumns()
            ->with(['author:id,name', 'category:id,name,slug'])
            ->orderBy('title')
            ->paginate(15);

        return view('pages.admin.books.index', compact('books'));
    }

    public function create(): View
    {
        $this->authorize('create', Book::class);

        return view('pages.admin.books.create', [
            'authors' => Author::query()->select(['id', 'name'])->orderBy('name')->get(),
            'categories' => Category::query()->select(['id', 'name'])->orderBy('name')->get(),
        ]);
    }

    public function store(StoreBookRequest $request): RedirectResponse
    {
        $this->books->create(
            $request->validated(),
            $request->file('cover_image'),
            $request->file('ebook_file'),
        );

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit(Book $book): View
    {
        $this->authorize('update', $book);

        $book->loadMissing('author:id,name');

        return view('pages.admin.books.edit', [
            'book' => $book,
            'authors' => Author::query()->select(['id', 'name'])->orderBy('name')->get(),
            'categories' => Category::query()->select(['id', 'name'])->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $this->books->update(
            $book,
            $request->validated(),
            $request->file('cover_image'),
            $request->file('ebook_file'),
        );

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $this->authorize('delete', $book);

        $this->books->delete($book);

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Buku berhasil dihapus.');
    }
}
