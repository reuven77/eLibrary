<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\LoanException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectLoanRequest;
use App\Models\Loan;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function __construct(private readonly LoanService $loans)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('manage', Loan::class);

        $status = $request->string('status')->toString();

        $loans = Loan::query()
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
                'borrower_phone',
                'created_at',
            ])
            ->with([
                'user:id,name,email',
                'book:id,title,call_number',
            ])
            ->when(
                $status !== '' && in_array($status, [
                    'menunggu_persetujuan',
                    'disetujui',
                    'ditolak',
                    'dikembalikan',
                    'terlambat',
                ], true),
                fn ($q) => $q->where('status', $status)
            )
            ->orderByRaw("CASE WHEN status = 'menunggu_persetujuan' THEN 0 ELSE 1 END")
            ->orderByDesc('borrowed_at')
            ->paginate(20)
            ->withQueryString();

        return view('pages.admin.loans.index', [
            'loans' => $loans,
            'status' => $status,
        ]);
    }

    public function indexPending(): View
    {
        $this->authorize('manage', Loan::class);

        $loans = Loan::query()
            ->select([
                'id',
                'user_id',
                'book_id',
                'borrowed_at',
                'due_at',
                'status',
                'borrower_phone',
                'borrower_address',
                'id_card_path',
                'borrower_notes',
                'created_at',
            ])
            ->with([
                'user:id,name,email',
                'book:id,title,call_number,stock,format',
            ])
            ->where('status', Loan::STATUS_PENDING)
            ->orderBy('borrowed_at')
            ->paginate(20);

        return view('pages.admin.loans.pending', compact('loans'));
    }

    public function showReturn(Loan $loan): View
    {
        $this->authorize('returnBook', $loan);

        abort_unless(
            in_array($loan->status, Loan::ON_HAND_STATUSES, true),
            403,
            'Pinjaman ini tidak bisa dikembalikan.'
        );

        $loan->load([
            'user:id,name,email',
            'book:id,title,call_number,author_id,stock',
            'book.author:id,name',
        ]);

        $estimatedFine = $this->loans->hitungDenda($loan);
        $daysLate = 0;

        if ($loan->due_at !== null && $loan->due_at->copy()->startOfDay()->lt(now()->startOfDay())) {
            $daysLate = (int) $loan->due_at->copy()->startOfDay()->diffInDays(now()->startOfDay());
        }

        return view('pages.admin.loans.return', [
            'loan' => $loan,
            'estimatedFine' => $estimatedFine,
            'daysLate' => $daysLate,
        ]);
    }

    public function approve(Loan $loan): RedirectResponse
    {
        $this->authorize('approve', $loan);

        try {
            $this->loans->approveLoan($loan->id, (string) auth()->id());
        } catch (LoanException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pengajuan pinjaman disetujui.');
    }

    public function reject(RejectLoanRequest $request, Loan $loan): RedirectResponse
    {
        try {
            $this->loans->rejectLoan(
                $loan->id,
                $request->validated('reason'),
                (string) $request->user()->id,
            );
        } catch (LoanException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pengajuan pinjaman ditolak.');
    }

    public function returnBook(Loan $loan): RedirectResponse
    {
        $this->authorize('returnBook', $loan);

        try {
            $returned = $this->loans->kembalikanBuku($loan);
        } catch (LoanException $e) {
            return back()->with('error', $e->getMessage());
        }

        $fine = (string) $returned->fine_amount;
        $message = bccomp($fine, '0.00', 2) === 1
            ? "Pengembalian dicatat. Denda keterlambatan: Rp {$fine}."
            : 'Pengembalian dicatat. Tanpa denda.';

        return redirect()
            ->route('admin.loans.index')
            ->with('success', $message);
    }
}
