<?php

namespace App\Http\Controllers;

use App\Services\CatalogService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private readonly CatalogService $catalog)
    {
    }

    public function __invoke(): View
    {
        return view('pages.home', [
            'books' => $this->catalog->featured(8),
            'categories' => $this->catalog->categories(),
        ]);
    }
}
