<?php

namespace Tests\Unit;

use App\Exceptions\LoanException;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LoanServiceTest extends TestCase
{
    use RefreshDatabase;

    private LoanService $loans;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loans = app(LoanService::class);
    }

    public function test_meminjam_buku_membuat_pengajuan_tanpa_mengurangi_stok(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $book = Book::factory()->physical(3)->create();

        $loan = $this->loans->meminjamBuku($member, $book);

        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'user_id' => $member->id,
            'book_id' => $book->id,
            'status' => Loan::STATUS_PENDING,
        ]);
        $this->assertNull($loan->due_at);
        $this->assertSame(3, $book->fresh()->stock);
    }

    public function test_approve_loan_mengurangi_stok_dan_set_due_at(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create(['role' => 'member']);
        $book = Book::factory()->physical(2)->create();
        $loan = $this->loans->meminjamBuku($member, $book);

        $approved = $this->loans->approveLoan($loan->id, $admin->id);

        $this->assertSame(Loan::STATUS_APPROVED, $approved->status);
        $this->assertNotNull($approved->due_at);
        $this->assertSame($admin->id, $approved->reviewed_by);
        $this->assertSame(1, $book->fresh()->stock);
    }

    public function test_approve_gagal_saat_stok_habis_tanpa_ubah_status(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create(['role' => 'member']);
        $book = Book::factory()->physical(1)->create();
        $loan = $this->loans->meminjamBuku($member, $book);
        $book->update(['stock' => 0]);

        try {
            $this->loans->approveLoan($loan->id, $admin->id);
            $threw = false;
        } catch (LoanException $e) {
            $threw = true;
            $this->assertStringContainsString('Stok buku habis', $e->getMessage());
        }

        $this->assertTrue($threw);
        $this->assertSame(Loan::STATUS_PENDING, $loan->fresh()->status);
        $this->assertSame(0, $book->fresh()->stock);
    }

    public function test_reject_loan_mengubah_status_tanpa_menyentuh_stok(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create(['role' => 'member']);
        $book = Book::factory()->physical(2)->create();
        $loan = $this->loans->meminjamBuku($member, $book);

        $rejected = $this->loans->rejectLoan($loan->id, 'Stok dialihkan ke anggota lain.', $admin->id);

        $this->assertSame(Loan::STATUS_REJECTED, $rejected->status);
        $this->assertSame('Stok dialihkan ke anggota lain.', $rejected->rejection_reason);
        $this->assertSame(2, $book->fresh()->stock);
    }

    public function test_meminjam_gagal_saat_stok_habis(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $book = Book::factory()->physical(0)->create();

        $this->expectException(LoanException::class);
        $this->expectExceptionMessage('Stok buku habis');

        $this->loans->meminjamBuku($member, $book);
    }

    public function test_stok_satu_hanya_satu_approve_yang_berhasil(): void
    {
        $admin = User::factory()->admin()->create();
        $memberA = User::factory()->create(['role' => 'member']);
        $memberB = User::factory()->create(['role' => 'member']);
        $book = Book::factory()->physical(1)->create();

        $loanA = $this->loans->meminjamBuku($memberA, $book);
        $loanB = $this->loans->meminjamBuku($memberB, $book);

        $this->assertSame(1, $book->fresh()->stock);

        $this->loans->approveLoan($loanA->id, $admin->id);

        try {
            $this->loans->approveLoan($loanB->id, $admin->id);
            $secondSucceeded = true;
        } catch (LoanException $e) {
            $secondSucceeded = false;
            $this->assertStringContainsString('Stok buku habis', $e->getMessage());
        }

        $this->assertFalse($secondSucceeded);
        $this->assertSame(0, $book->fresh()->stock);
        $this->assertSame(1, Loan::query()->where('book_id', $book->id)->where('status', Loan::STATUS_APPROVED)->count());
        $this->assertSame(Loan::STATUS_PENDING, $loanB->fresh()->status);
    }

    public function test_hitung_denda_tiga_hari_terlambat(): void
    {
        $loan = Loan::factory()->create([
            'due_at' => Carbon::parse('2026-07-01'),
            'status' => Loan::STATUS_APPROVED,
        ]);

        $fine = $this->loans->hitungDenda($loan, Carbon::parse('2026-07-04'));

        $this->assertSame('6000.00', $fine);
    }

    public function test_kembalikan_buku_mengembalikan_stok_dan_mencatat_denda(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create(['role' => 'member']);
        $book = Book::factory()->physical(1)->create();
        $loan = $this->loans->meminjamBuku($member, $book);
        $loan = $this->loans->approveLoan($loan->id, $admin->id);

        $loan->due_at = now()->subDays(2);
        $loan->save();

        $returned = $this->loans->kembalikanBuku($loan);

        $this->assertSame(Loan::STATUS_RETURNED, $returned->status);
        $this->assertSame('4000.00', (string) $returned->fine_amount);
        $this->assertSame(1, $book->fresh()->stock);
    }
}
