<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Services\LoanService;
use Illuminate\Console\Command;

class MarkOverdueLoansCommand extends Command
{
    protected $signature = 'ruangbaca:mark-overdue';

    protected $description = 'Tandai pinjaman disetujui yang melewati jatuh tempo sebagai terlambat';

    public function handle(LoanService $loans): int
    {
        $query = Loan::query()
            ->select(['id'])
            ->where('status', Loan::STATUS_APPROVED)
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->whereNull('returned_at');

        $count = 0;

        $query->orderBy('id')->chunkById(100, function ($chunk) use ($loans, &$count) {
            foreach ($chunk as $loan) {
                $loans->tandaiTerlambat($loan->id);
                $count++;
            }
        });

        $this->info("Ditandai terlambat: {$count} pinjaman.");

        return self::SUCCESS;
    }
}
