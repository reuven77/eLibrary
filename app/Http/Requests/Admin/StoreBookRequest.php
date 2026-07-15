<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Book::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:500'],
            'isbn' => ['nullable', 'string', 'max:20', 'unique:books,isbn'],
            'author_name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'uuid', 'exists:categories,id'],
            'format' => ['required', Rule::in(['fisik', 'digital', 'keduanya'])],
            'stock' => ['required', 'integer', 'min:0'],
            'synopsis' => ['nullable', 'string'],
            'published_year' => ['nullable', 'integer', 'min:1000', 'max:'.(date('Y') + 1)],
            'call_number' => ['required', 'string', 'max:50'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'ebook_file' => ['nullable', 'file', 'mimes:pdf,epub', 'max:51200'],
        ];
    }
}
