<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;

class AdminDashboardService
{
    /**
     * Ringkasan angka untuk dashboard admin.
     *
     * @return array{total_books: int, borrowed: int, overdue: int, outstanding_fines: string}
     */
    public function summary(): array
    {
        $outstanding = Loan::query()
            ->whereIn('status', Loan::ON_HAND_STATUSES)
            ->where('fine_amount', '>', 0)
            ->sum('fine_amount');

        return [
            'total_books' => Book::query()->count(),
            'borrowed' => Loan::query()->where('status', Loan::STATUS_APPROVED)->count(),
            'pending' => Loan::query()->where('status', Loan::STATUS_PENDING)->count(),
            'overdue' => Loan::query()->where('status', Loan::STATUS_OVERDUE)->count(),
            'outstanding_fines' => number_format((float) $outstanding, 2, '.', ''),
            'total_members' => User::query()->where('role', 'member')->count(),
        ];
    }

    /**
     * Peminjaman aktif/terlambat untuk tabel ringkas (paginated).
     */
    public function recentLoans(int $perPage = 15)
    {
        return Loan::query()
            ->select([
                'id',
                'user_id',
                'book_id',
                'borrowed_at',
                'due_at',
                'returned_at',
                'status',
                'fine_amount',
                'created_at',
            ])
            ->with([
                'user:id,name,email',
                'book:id,title,call_number',
            ])
            ->orderByDesc('borrowed_at')
            ->paginate($perPage);
    }
}
