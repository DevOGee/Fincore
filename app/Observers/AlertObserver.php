<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\Budget;
use App\Services\AlertService;

class AlertObserver
{
    protected $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        // Check for expense-related alerts
        $this->alertService->checkExpenseAlerts($expense);
        
        // Check budget alerts for this expense's category
        $this->alertService->checkBudgetAlerts(
            $expense->user_id,
            $expense->date->month,
            $expense->date->year
        );
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        // Only check if amount, date, or category changed
        if ($expense->wasChanged(['amount', 'date', 'expense_category_id'])) {
            $this->alertService->checkExpenseAlerts($expense);
            
            $this->alertService->checkBudgetAlerts(
                $expense->user_id,
                $expense->date->month,
                $expense->date->year
            );
            
            // If date or category changed, check old values too
            if ($expense->wasChanged(['date', 'expense_category_id'])) {
                $this->alertService->checkBudgetAlerts(
                    $expense->user_id,
                    $expense->getOriginal('date')->month,
                    $expense->getOriginal('date')->year
                );
            }
        }
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        // Check budget alerts when an expense is deleted
        $this->alertService->checkBudgetAlerts(
            $expense->user_id,
            $expense->date->month,
            $expense->date->year
        );
    }

    /**
     * Handle the Budget "created" event.
     */
    public function budgetCreated(Budget $budget): void
    {
        // Initial budget check
        $this->alertService->checkSingleBudgetAlerts(
            $budget,
            $budget->starts_at,
            $budget->ends_at
        );
    }

    /**
     * Handle the Budget "updated" event.
     */
    public function budgetUpdated(Budget $budget): void
    {
        if ($budget->wasChanged(['amount', 'starts_at', 'ends_at', 'expense_category_id'])) {
            $this->alertService->checkSingleBudgetAlerts(
                $budget,
                $budget->starts_at,
                $budget->ends_at
            );
            
            // If date range changed, check old range too
            if ($budget->wasChanged(['starts_at', 'ends_at'])) {
                $this->alertService->checkSingleBudgetAlerts(
                    $budget,
                    $budget->getOriginal('starts_at'),
                    $budget->getOriginal('ends_at')
                );
            }
        }
    }

    /**
     * Handle the Budget "deleted" event.
     */
    public function budgetDeleted(Budget $budget): void
    {
        // No action needed when budget is deleted
    }
}
