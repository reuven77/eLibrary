<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBookCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_bisa_melihat_index_buku(): void
    {
        $admin = User::factory()->admin()->create();
        Book::factory()->count(2)->create();

        $response = $this->actingAs($admin)->get(route('admin.books.index'));

        $response->assertOk();
    }

    public function test_member_ditolak_dari_crud_buku(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $book = Book::factory()->create();

        $this->actingAs($member)->get(route('admin.books.index'))->assertForbidden();
        $this->actingAs($member)->get(route('admin.books.create'))->assertForbidden();
        $this->actingAs($member)->post(route('admin.books.store'), $this->validPayload())->assertForbidden();
        $this->actingAs($member)->get(route('admin.books.edit', $book))->assertForbidden();
        $this->actingAs($member)->put(route('admin.books.update', $book), $this->validPayload($book))->assertForbidden();
        $this->actingAs($member)->delete(route('admin.books.destroy', $book))->assertForbidden();
    }

    public function test_admin_bisa_menyimpan_buku_baru(): void
    {
        $admin = User::factory()->admin()->create();
        $payload = $this->validPayload();

        $response = $this->actingAs($admin)->post(route('admin.books.store'), $payload);

        $response->assertRedirect(route('admin.books.index'));
        $this->assertDatabaseHas('books', [
            'title' => $payload['title'],
            'call_number' => $payload['call_number'],
            'isbn' => $payload['isbn'],
        ]);
    }

    public function test_admin_bisa_memperbarui_buku(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->physical(2)->create();
        $payload = $this->validPayload($book);
        $payload['title'] = 'Judul Diperbarui';
        $payload['stock'] = 5;

        $response = $this->actingAs($admin)->put(route('admin.books.update', $book), $payload);

        $response->assertRedirect(route('admin.books.index'));
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Judul Diperbarui',
            'stock' => 5,
        ]);
    }

    public function test_admin_bisa_menyimpan_buku_dengan_penulis_baru(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.books.store'), [
            'title' => 'Buku Penulis Baru',
            'isbn' => '978-1234567890',
            'author_name' => 'Nama Penulis Belum Ada',
            'category_id' => $category->id,
            'format' => 'fisik',
            'stock' => 1,
            'call_number' => '813.1 · NEW',
        ]);

        $response->assertRedirect(route('admin.books.index'));
        $this->assertDatabaseHas('authors', ['name' => 'Nama Penulis Belum Ada']);
        $this->assertDatabaseHas('books', ['title' => 'Buku Penulis Baru']);
    }

    public function test_admin_bisa_menghapus_buku(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.books.destroy', $book));

        $response->assertRedirect(route('admin.books.index'));
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_tamu_dilarang_akses_admin(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.books.index'))->assertRedirect(route('login'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(?Book $book = null): array
    {
        $author = $book?->author ?? Author::factory()->create();
        $category = $book?->category ?? Category::factory()->create();

        return [
            'title' => 'Buku Uji RuangBaca',
            'isbn' => '978-'.fake()->unique()->numerify('##########'),
            'author_name' => $author->name,
            'category_id' => $category->id,
            'format' => 'fisik',
            'stock' => 3,
            'synopsis' => 'Sinopsis untuk pengujian.',
            'published_year' => 2024,
            'call_number' => '813.1 · TST',
        ];
    }
}
