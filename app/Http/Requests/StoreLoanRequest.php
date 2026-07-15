<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        $book = $this->route('book');

        return $this->user()?->can('borrow', $book) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'borrower_phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{8,30}$/'],
            'borrower_address' => ['required', 'string', 'min:10', 'max:1000'],
            'id_card' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'borrower_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'borrower_phone.required' => 'Nomor HP wajib diisi.',
            'borrower_phone.regex' => 'Format nomor HP tidak valid.',
            'borrower_address.required' => 'Alamat wajib diisi.',
            'borrower_address.min' => 'Alamat minimal 10 karakter.',
            'id_card.required' => 'Foto kartu identitas wajib diunggah.',
            'id_card.image' => 'Kartu identitas harus berupa gambar.',
            'id_card.max' => 'Ukuran foto kartu identitas maksimal 2 MB.',
        ];
    }
}
