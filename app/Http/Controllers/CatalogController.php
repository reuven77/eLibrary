<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogSearchRequest;
use App\Models\Book;
use App\Services\CatalogService;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function __construct(private readonly CatalogService $catalog)
    {
    }

    public function index(CatalogSearchRequest $request): View
    {
        $filters = $request->validated();

        return view('pages.catalog.index', [
            'books' => $this->catalog->search($filters),
            'categories' => $this->catalog->categories(),
            'filters' => $filters,
        ]);
    }

    public function show(Book $book): View
    {
        $this->authorize('view', $book);

        return view('pages.catalog.show', [
            'book' => $this->catalog->findForDetail($book->id),
        ]);
    }
}
