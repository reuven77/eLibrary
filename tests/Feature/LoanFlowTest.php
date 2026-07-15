<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LoanFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_dapat_mengajukan_pinjaman_via_http(): void
    {
        Storage::fake('public');

        $member = User::factory()->create(['role' => 'member']);
        $book = Book::factory()->physical(2)->create();

        $response = $this->actingAs($member)->post(route('loans.store', $book), $this->loanPayload());

        $response->assertRedirect(route('loans.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('loans', [
            'user_id' => $member->id,
            'book_id' => $book->id,
            'status' => Loan::STATUS_PENDING,
            'borrower_phone' => '081234567890',
        ]);
        $this->assertSame(2, $book->fresh()->stock);
        $this->assertNotNull(Loan::query()->first()->id_card_path);
    }

    public function test_form_pengajuan_menampilkan_halaman_identitas(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $book = Book::factory()->physical(1)->create();

        $response = $this->actingAs($member)->get(route('loans.create', $book));

        $response->assertOk();
        $response->assertSee('Ajukan peminjaman');
        $response->assertSee('borrower_phone', false);
    }

    public function test_pinjam_gagal_saat_stok_habis_via_http(): void
    {
        Storage::fake('public');

        $member = User::factory()->create(['role' => 'member']);
        $book = Book::factory()->physical(0)->create();

        $response = $this->actingAs($member)
            ->from(route('loans.create', $book))
            ->post(route('loans.store', $book), $this->loanPayload());

        $response->assertRedirect(route('loans.create', $book));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('Stok buku habis', session('error'));
        $this->assertSame(0, Loan::query()->count());
        $this->assertSame(0, $book->fresh()->stock);
    }

    public function test_tamu_tidak_bisa_meminjam(): void
    {
        $book = Book::factory()->physical(1)->create();

        $response = $this->post(route('loans.store', $book), $this->loanPayload());

        $response->assertRedirect(route('login'));
        $this->assertSame(0, Loan::query()->count());
    }

    public function test_admin_dapat_melihat_halaman_konfirmasi_pengembalian(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create(['role' => 'member']);
        $book = Book::factory()->physical(1)->create();

        $loan = Loan::factory()->create([
            'user_id' => $member->id,
            'book_id' => $book->id,
            'status' => Loan::STATUS_APPROVED,
            'borrowed_at' => now()->subDays(3),
            'due_at' => now()->addDays(4),
            'returned_at' => null,
            'fine_amount' => 0,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.loans.return.show', $loan));

        $response->assertOk();
        $response->assertSee('Konfirmasi pengembalian');
        $response->assertSee($book->title);
    }

    public function test_admin_dapat_mengembalikan_buku(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create(['role' => 'member']);
        $book = Book::factory()->physical(1)->create();

        $loan = Loan::factory()->create([
            'user_id' => $member->id,
            'book_id' => $book->id,
            'status' => Loan::STATUS_APPROVED,
            'borrowed_at' => now()->subDays(3),
            'due_at' => now()->addDays(4),
            'returned_at' => null,
            'fine_amount' => 0,
        ]);
        $book->update(['stock' => 0]);

        $response = $this->actingAs($admin)->post(route('admin.loans.return', $loan));

        $response->assertRedirect(route('admin.loans.index'));
        $response->assertSessionHas('success');
        $this->assertSame(Loan::STATUS_RETURNED, $loan->fresh()->status);
        $this->assertSame(1, $book->fresh()->stock);
    }

    public function test_member_tidak_bisa_mengembalikan_via_admin_route(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $book = Book::factory()->physical(1)->create();
        $loan = Loan::factory()->create([
            'user_id' => $member->id,
            'book_id' => $book->id,
            'status' => Loan::STATUS_APPROVED,
        ]);

        $response = $this->actingAs($member)->post(route('admin.loans.return', $loan));

        $response->assertForbidden();
    }

    /**
     * @return array<string, mixed>
     */
    private function loanPayload(): array
    {
        return [
            'borrower_phone' => '081234567890',
            'borrower_address' => 'Jl. Melati No. 10, Jakarta Selatan',
            'id_card' => UploadedFile::fake()->image('ktp.jpg'),
            'borrower_notes' => 'Akan diambil sore',
        ];
    }
}
