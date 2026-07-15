<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CatalogService
{
    /**
     * Katalog berpaginasi: search judul/penulis + filter kategori.
     *
     * @param  array{q?: string|null, category?: string|null, per_page?: int|null}  $filters
     */
    public function search(array $filters = []): LengthAwarePaginator
    {
        $query = Book::query()
            ->selectCatalogColumns()
            ->with([
                'author:id,name',
                'category:id,name,slug',
            ]);

        $this->applyFilters($query, $filters);

        return $query
            ->orderBy('title')
            ->paginate((int) ($filters['per_page'] ?? 12))
            ->withQueryString();
    }

    /**
     * Buku pilihan untuk beranda (paginasi kecil / limit via paginate).
     */
    public function featured(int $perPage = 8): LengthAwarePaginator
    {
        return Book::query()
            ->selectCatalogColumns()
            ->with([
                'author:id,name',
                'category:id,name,slug',
            ])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function categories(): Collection
    {
        return Category::query()
            ->select(['id', 'name', 'slug'])
            ->orderBy('name')
            ->get();
    }

    public function findForDetail(string $bookId): Book
    {
        return Book::query()
            ->select([
                'id',
                'title',
                'isbn',
                'author_id',
                'category_id',
                'cover_image_path',
                'file_path',
                'format',
                'stock',
                'synopsis',
                'published_year',
                'call_number',
                'created_at',
                'updated_at',
            ])
            ->with([
                'author:id,name,bio',
                'category:id,name,slug',
                'reviews' => fn ($q) => $q
                    ->select(['id', 'user_id', 'book_id', 'rating', 'comment', 'created_at'])
                    ->with('user:id,name')
                    ->latest('created_at'),
            ])
            ->whereKey($bookId)
            ->firstOrFail();
    }

    /**
     * @param  Builder<Book>  $query
     * @param  array{q?: string|null, category?: string|null}  $filters
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        $term = trim((string) ($filters['q'] ?? ''));

        if ($term !== '') {
            $like = '%'.$term.'%';

            $query->where(function (Builder $inner) use ($like) {
                $inner->where('title', 'ilike', $like)
                    ->orWhere('call_number', 'ilike', $like)
                    ->orWhere('isbn', 'ilike', $like)
                    ->orWhereHas('author', function (Builder $author) use ($like) {
                        $author->where('name', 'ilike', $like);
                    });
            });
        }

        $categorySlug = trim((string) ($filters['category'] ?? ''));

        if ($categorySlug !== '') {
            $query->whereHas('category', function (Builder $category) use ($categorySlug) {
                $category->where('slug', $categorySlug);
            });
        }
    }
}
