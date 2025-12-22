<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LoanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Loan $loan): bool
    {
        return $user->id === $loan->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Loan $loan): bool
    {
        return $user->id === $loan->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Loan $loan): bool
    {
        // Only allow deletion of manual loans
        return $user->id === $loan->user_id && !$loan->is_auto_created;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Loan $loan): bool
    {
        return $user->id === $loan->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Loan $loan): bool
    {
        return $user->id === $loan->user_id && !$loan->is_auto_created;
    }

    /**
     * Determine whether the user can record a repayment for the loan.
     */
    public function recordRepayment(User $user, Loan $loan): bool
    {
        return $user->id === $loan->user_id && 
               $loan->status !== Loan::STATUS_CLEARED && 
               $loan->outstanding_balance > 0;
    }
}
