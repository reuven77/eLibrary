<?php

namespace App\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
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
])]
class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory, HasUuids;

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'published_year' => 'integer',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')
            ->using(Favorite::class)
            ->withPivot('created_at');
    }

    public function isPhysical(): bool
    {
        return in_array($this->format, ['fisik', 'keduanya'], true);
    }

    public function isDigital(): bool
    {
        return in_array($this->format, ['digital', 'keduanya'], true);
    }

    /**
     * Status ketersediaan untuk stempel UI (bukan kolom DB).
     * Brass=tersedia, Forest=dipinjam (stok habis), Rust dipakai di loan terlambat.
     */
    public function availabilityStatus(): string
    {
        if ($this->isDigital() && ! $this->isPhysical()) {
            return 'tersedia';
        }

        return $this->stock > 0 ? 'tersedia' : 'dipinjam';
    }

    public function scopeSelectCatalogColumns(Builder $query): Builder
    {
        return $query->select([
            'id',
            'title',
            'isbn',
            'author_id',
            'category_id',
            'cover_image_path',
            'format',
            'stock',
            'synopsis',
            'published_year',
            'call_number',
            'created_at',
            'updated_at',
        ]);
    }
}
