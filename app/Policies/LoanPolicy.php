<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;

class LoanPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Loan $loan): bool
    {
        return $user->isAdmin() || $loan->user_id === $user->id;
    }

    public function printReceipt(User $user, Loan $loan): bool
    {
        return $loan->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'member' || $user->isAdmin();
    }

    public function returnBook(User $user, Loan $loan): bool
    {
        return $user->isAdmin();
    }

    public function approve(User $user, Loan $loan): bool
    {
        return $user->isAdmin();
    }

    public function reject(User $user, Loan $loan): bool
    {
        return $user->isAdmin();
    }

    public function manage(User $user): bool
    {
        return $user->isAdmin();
    }
}
