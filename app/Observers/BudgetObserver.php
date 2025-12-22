<?php

namespace App\Observers;

use App\Models\Budget;
use App\Models\Expense;

class BudgetObserver
{
    /**
     * Handle the Budget "updated" event.
     */
    public function updated(Budget $budget): void
    {
        if ($budget->isDirty('status') && $budget->status === 'approved') {
            Expense::create([
                'user_id' => $budget->user_id,
                'expense_category_id' => $budget->expense_category_id,
                'category' => $budget->category,
                'amount' => $budget->limit,
                'date' => now(),
                'description' => "Approved Budget: {$budget->category}",
                'recipient' => 'Internal', // Default recipient
                'payment_method' => 'Other', // Default payment method
            ]);
        }
    }
}
