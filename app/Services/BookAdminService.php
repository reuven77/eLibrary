<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookAdminService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, ?UploadedFile $cover = null, ?UploadedFile $ebook = null): Book
    {
        $payload = $this->mapPayload($data);
        $payload['cover_image_path'] = $this->storeCover($cover);
        $payload['file_path'] = $this->storeEbook($ebook);

        return Book::query()->create($payload);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Book $book, array $data, ?UploadedFile $cover = null, ?UploadedFile $ebook = null): Book
    {
        $payload = $this->mapPayload($data);

        if ($cover !== null) {
            $this->deleteIfExists($book->cover_image_path);
            $payload['cover_image_path'] = $this->storeCover($cover);
        }

        if ($ebook !== null) {
            $this->deleteIfExists($book->file_path);
            $payload['file_path'] = $this->storeEbook($ebook);
        }

        $book->update($payload);

        return $book->refresh();
    }

    public function delete(Book $book): void
    {
        $this->deleteIfExists($book->cover_image_path);
        $this->deleteIfExists($book->file_path);
        $book->delete();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function mapPayload(array $data): array
    {
        return [
            'title' => $data['title'],
            'isbn' => $data['isbn'] ?? null,
            'author_id' => $this->resolveAuthorId($data['author_name']),
            'category_id' => $data['category_id'],
            'format' => $data['format'],
            'stock' => (int) $data['stock'],
            'synopsis' => $data['synopsis'] ?? null,
            'published_year' => $data['published_year'] ?? null,
            'call_number' => $data['call_number'],
        ];
    }

    private function resolveAuthorId(string $name): string
    {
        $name = Str::of($name)->squish()->toString();

        $existing = Author::query()
            ->whereRaw('LOWER(name) = ?', [Str::lower($name)])
            ->first();

        if ($existing !== null) {
            return $existing->id;
        }

        return Author::query()->create(['name' => $name])->id;
    }

    private function storeCover(?UploadedFile $file): ?string
    {
        if ($file === null) {
            return null;
        }

        $filename = time().'_'.$file->getClientOriginalName();

        return $file->storeAs('covers', $filename, 'public');
    }

    private function storeEbook(?UploadedFile $file): ?string
    {
        if ($file === null) {
            return null;
        }

        return $file->store('ebooks', 'public');
    }

    private function deleteIfExists(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
