<?php

namespace App\Http\Controllers;

use App\Exceptions\LoanException;
use App\Http\Requests\StoreLoanRequest;
use App\Models\Book;
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
        $this->authorize('viewAny', Loan::class);

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
                'reviewed_by',
                'created_at',
            ])
            ->with(['book:id,title,call_number,format'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('borrowed_at')
            ->paginate(15);

        return view('pages.loans.index', compact('loans'));
    }

    public function create(Book $book): View|RedirectResponse
    {
        $this->authorize('borrow', $book);

        if (! $book->isPhysical()) {
            return redirect()
                ->route('catalog.show', $book)
                ->with('error', 'Hanya buku fisik yang bisa dipinjam.');
        }

        $book->load(['author:id,name', 'category:id,name']);

        return view('pages.loans.create', compact('book'));
    }

    public function store(StoreLoanRequest $request, Book $book): RedirectResponse
    {
        try {
            $this->loans->meminjamBuku(
                $request->user(),
                $book,
                $request->safe()->only([
                    'borrower_phone',
                    'borrower_address',
                    'borrower_notes',
                ]),
                $request->file('id_card'),
            );
        } catch (LoanException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('loans.index')
            ->with('success', 'Pengajuan pinjaman dikirim. Menunggu persetujuan pustakawan.');
    }

    public function printReceipt(Loan $loan): View
    {
        $this->authorize('printReceipt', $loan);

        abort_unless(
            $loan->canPrintReceipt(),
            403,
            'Kartu bukti hanya tersedia untuk peminjaman yang sudah disetujui.'
        );

        $loan->load([
            'user:id,name,email',
            'book:id,title,call_number,author_id',
            'book.author:id,name',
            'reviewer:id,name',
        ]);

        return view('pages.member.loan-receipt', compact('loan'));
    }
}
