<?php

namespace App\Services;

use App\Exceptions\LoanException;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LoanService
{
    /**
     * Ajukan peminjaman buku fisik (belum mengurangi stok).
     * Record dibuat dengan status menunggu_persetujuan.
     *
     * @param  array{borrower_phone?: string, borrower_address?: string, borrower_notes?: ?string}  $identity
     *
     * @throws LoanException
     */
    public function meminjamBuku(
        User $user,
        Book|string $book,
        array $identity = [],
        ?UploadedFile $idCard = null,
    ): Loan {
        return DB::transaction(function () use ($user, $book, $identity, $idCard) {
            /** @var User $lockedUser */
            $lockedUser = User::query()
                ->select(['id', 'is_active'])
                ->whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedUser->isBlocked()) {
                throw LoanException::userTidakAktif();
            }

            $bookId = $book instanceof Book ? $book->id : $book;

            /** @var Book|null $lockedBook */
            $lockedBook = Book::query()
                ->select([
                    'id',
                    'title',
                    'format',
                    'stock',
                ])
                ->whereKey($bookId)
                ->lockForUpdate()
                ->first();

            if ($lockedBook === null) {
                throw LoanException::bukuTidakDitemukan();
            }

            if (! $lockedBook->isPhysical()) {
                throw LoanException::bukuTidakDapatDipinjam();
            }

            // Soft-check stok untuk UX; pengurangan stok hanya di approveLoan().
            if ($lockedBook->stock < 1) {
                throw LoanException::stokHabis();
            }

            $maxActive = (int) config('ruangbaca.max_active_loans');

            $activeCount = Loan::query()
                ->where('user_id', $lockedUser->id)
                ->whereIn('status', Loan::QUOTA_STATUSES)
                ->count();

            if ($activeCount >= $maxActive) {
                throw LoanException::batasPinjamanTercapai($maxActive);
            }

            $alreadyBorrowing = Loan::query()
                ->where('user_id', $lockedUser->id)
                ->where('book_id', $lockedBook->id)
                ->whereIn('status', Loan::QUOTA_STATUSES)
                ->exists();

            if ($alreadyBorrowing) {
                throw LoanException::sudahMeminjamBukuIni();
            }

            $idCardPath = null;
            if ($idCard !== null) {
                $filename = time().'_'.$idCard->getClientOriginalName();
                $idCardPath = $idCard->storeAs('id-cards', $filename, 'public');
            }

            return Loan::query()->create([
                'user_id' => $lockedUser->id,
                'book_id' => $lockedBook->id,
                'borrowed_at' => now(),
                'due_at' => null,
                'returned_at' => null,
                'status' => Loan::STATUS_PENDING,
                'fine_amount' => 0,
                'rejection_reason' => null,
                'reviewed_by' => null,
                'borrower_phone' => $identity['borrower_phone'] ?? null,
                'borrower_address' => $identity['borrower_address'] ?? null,
                'id_card_path' => $idCardPath,
                'borrower_notes' => $identity['borrower_notes'] ?? null,
            ]);
        });
    }

    /**
     * Admin menyetujui pengajuan: kurangi stok atomik + set due_at.
     *
     * @throws LoanException
     */
    public function approveLoan(string $loanId, string $adminId): Loan
    {
        return DB::transaction(function () use ($loanId, $adminId) {
            /** @var Loan|null $lockedLoan */
            $lockedLoan = Loan::query()
                ->select([
                    'id',
                    'user_id',
                    'book_id',
                    'borrowed_at',
                    'due_at',
                    'returned_at',
                    'status',
                    'fine_amount',
                    'rejection_reason',
                    'reviewed_by',
                ])
                ->whereKey($loanId)
                ->lockForUpdate()
                ->first();

            if ($lockedLoan === null) {
                throw LoanException::pinjamanTidakDitemukan();
            }

            if ($lockedLoan->status !== Loan::STATUS_PENDING) {
                throw LoanException::pinjamanBukanMenunggu();
            }

            /** @var Book|null $lockedBook */
            $lockedBook = Book::query()
                ->select(['id', 'format', 'stock'])
                ->whereKey($lockedLoan->book_id)
                ->lockForUpdate()
                ->first();

            if ($lockedBook === null) {
                throw LoanException::bukuTidakDitemukan();
            }

            if ($lockedBook->stock < 1) {
                throw LoanException::stokHabisSaatPersetujuan();
            }

            $lockedBook->stock = $lockedBook->stock - 1;
            $lockedBook->save();

            $approvedAt = now();
            $lockedLoan->borrowed_at = $approvedAt;
            $lockedLoan->due_at = $approvedAt->copy()->addDays((int) config('ruangbaca.loan_period_days'));
            $lockedLoan->status = Loan::STATUS_APPROVED;
            $lockedLoan->reviewed_by = $adminId;
            $lockedLoan->rejection_reason = null;
            $lockedLoan->save();

            return $lockedLoan->refresh();
        });
    }

    /**
     * Admin menolak pengajuan pinjaman (stok tidak berubah).
     *
     * @throws LoanException
     */
    public function rejectLoan(string $loanId, string $reason, ?string $adminId = null): Loan
    {
        return DB::transaction(function () use ($loanId, $reason, $adminId) {
            /** @var Loan|null $lockedLoan */
            $lockedLoan = Loan::query()
                ->select([
                    'id',
                    'status',
                    'rejection_reason',
                    'reviewed_by',
                    'due_at',
                ])
                ->whereKey($loanId)
                ->lockForUpdate()
                ->first();

            if ($lockedLoan === null) {
                throw LoanException::pinjamanTidakDitemukan();
            }

            if ($lockedLoan->status !== Loan::STATUS_PENDING) {
                throw LoanException::pinjamanBukanMenunggu();
            }

            $lockedLoan->status = Loan::STATUS_REJECTED;
            $lockedLoan->rejection_reason = $reason;
            $lockedLoan->reviewed_by = $adminId;
            $lockedLoan->due_at = null;
            $lockedLoan->save();

            return $lockedLoan->refresh();
        });
    }

    /**
     * Konfirmasi pengembalian buku fisik; hitung denda bila terlambat.
     *
     * @throws LoanException
     */
    public function kembalikanBuku(Loan|string $loan, ?CarbonInterface $returnedAt = null): Loan
    {
        return DB::transaction(function () use ($loan, $returnedAt) {
            $loanId = $loan instanceof Loan ? $loan->id : $loan;

            /** @var Loan|null $lockedLoan */
            $lockedLoan = Loan::query()
                ->select([
                    'id',
                    'user_id',
                    'book_id',
                    'borrowed_at',
                    'due_at',
                    'returned_at',
                    'status',
                    'fine_amount',
                ])
                ->whereKey($loanId)
                ->lockForUpdate()
                ->first();

            if ($lockedLoan === null) {
                throw LoanException::pinjamanTidakDitemukan();
            }

            if (! in_array($lockedLoan->status, Loan::ON_HAND_STATUSES, true)) {
                throw LoanException::pinjamanTidakAktif();
            }

            /** @var Book|null $lockedBook */
            $lockedBook = Book::query()
                ->select(['id', 'format', 'stock'])
                ->whereKey($lockedLoan->book_id)
                ->lockForUpdate()
                ->first();

            if ($lockedBook === null) {
                throw LoanException::bukuTidakDitemukan();
            }

            $returnedAt = Carbon::parse($returnedAt ?? now());
            $fine = $this->hitungDenda($lockedLoan, $returnedAt);

            $lockedLoan->returned_at = $returnedAt;
            $lockedLoan->status = Loan::STATUS_RETURNED;
            $lockedLoan->fine_amount = $fine;
            $lockedLoan->save();

            if ($lockedBook->isPhysical()) {
                $lockedBook->stock = $lockedBook->stock + 1;
                $lockedBook->save();
            }

            return $lockedLoan->refresh();
        });
    }

    /**
     * Hitung denda keterlambatan (NUMERIC-safe, dibulatkan 2 desimal).
     */
    public function hitungDenda(Loan $loan, ?CarbonInterface $asOf = null): string
    {
        if ($loan->due_at === null) {
            return '0.00';
        }

        $asOf = Carbon::parse($asOf ?? now())->startOfDay();
        $dueAt = Carbon::parse($loan->due_at)->startOfDay();

        if ($asOf->lessThanOrEqualTo($dueAt)) {
            return '0.00';
        }

        $daysLate = (int) $dueAt->diffInDays($asOf);
        $perDay = (string) config('ruangbaca.fine_per_day');
        $maxFine = (string) config('ruangbaca.max_fine');

        $raw = bcmul((string) $daysLate, $perDay, 2);
        $cap = bcadd($maxFine, '0', 2);

        return bccomp($raw, $cap, 2) === 1 ? $cap : $raw;
    }

    /**
     * Tandai loan disetujui yang melewati due_at sebagai terlambat.
     */
    public function tandaiTerlambat(Loan|string $loan): Loan
    {
        return DB::transaction(function () use ($loan) {
            $loanId = $loan instanceof Loan ? $loan->id : $loan;

            /** @var Loan|null $lockedLoan */
            $lockedLoan = Loan::query()
                ->select([
                    'id',
                    'due_at',
                    'returned_at',
                    'status',
                    'fine_amount',
                ])
                ->whereKey($loanId)
                ->lockForUpdate()
                ->first();

            if ($lockedLoan === null) {
                throw LoanException::pinjamanTidakDitemukan();
            }

            if ($lockedLoan->status !== Loan::STATUS_APPROVED || $lockedLoan->returned_at !== null) {
                return $lockedLoan;
            }

            if ($lockedLoan->due_at === null || $lockedLoan->due_at->isFuture()) {
                return $lockedLoan;
            }

            $lockedLoan->status = Loan::STATUS_OVERDUE;
            $lockedLoan->fine_amount = $this->hitungDenda($lockedLoan);
            $lockedLoan->save();

            return $lockedLoan;
        });
    }
}
