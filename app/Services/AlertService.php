<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\Loan;
use Carbon\Carbon;

class AlertService
{
    // Budget utilization thresholds
    const BUDGET_WARNING_THRESHOLD = 75; // 75%
    const BUDGET_CRITICAL_THRESHOLD = 90; // 90%
    const BUDGET_BREACH_THRESHOLD = 100; // 100%

    // Daily spending threshold (percentage above average)
    const DAILY_SPENDING_THRESHOLD = 150; // 150% of average

    // Days to consider for average calculation
    const AVERAGE_DAYS = 30;

    /**
     * Check and create budget alerts for a user
     */
    public function checkBudgetAlerts($userId, $month = null, $year = null)
    {
        $month = $month ?: now()->month;
        $year = $year ?: now()->year;
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $budgets = Budget::where('user_id', $userId)
            ->where('starts_at', '<=', $endDate)
            ->where('ends_at', '>=', $startDate)
            ->with(['expenseCategory'])
            ->get();

        foreach ($budgets as $budget) {
            $this->checkSingleBudgetAlerts($budget, $startDate, $endDate);
        }
    }

    protected function checkSingleBudgetAlerts(Budget $budget, $startDate, $endDate)
    {
        $totalSpent = $budget->expenses()
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $utilization = ($totalSpent / $budget->amount) * 100;

        // Check for budget warnings
        if ($utilization >= self::BUDGET_BREACH_THRESHOLD) {
            $this->createBudgetBreachAlert($budget, $totalSpent, $utilization);
            
            // Check if we need to create a loan
            if ($budget->auto_loan_on_breach) {
                $this->createLoanForBudgetBreach($budget, $totalSpent);
            }
        } elseif ($utilization >= self::BUDGET_CRITICAL_THRESHOLD) {
            $this->createBudgetCriticalAlert($budget, $totalSpent, $utilization);
        } elseif ($utilization >= self::BUDGET_WARNING_THRESHOLD) {
            $this->createBudgetWarningAlert($budget, $totalSpent, $utilization);
        }

        // Check for rapid budget burn (more than 50% spent in first week)
        if (now()->day <= 7 && $utilization > 50) {
            $this->createRapidBudgetBurnAlert($budget, $totalSpent, $utilization);
        }
    }

    /**
     * Check for unusual spending patterns
     */
    public function checkExpenseAlerts(Expense $expense)
    {
        $userId = $expense->user_id;
        $categoryId = $expense->expense_category_id;
        $amount = $expense->amount;
        $date = $expense->date;

        // Check for high daily spend
        $this->checkHighDailySpend($userId, $categoryId, $amount, $date);
        
        // Check for unusual category spend
        $this->checkUnusualCategorySpend($userId, $categoryId, $amount, $date);
    }

    protected function checkHighDailySpend($userId, $categoryId, $amount, $date)
    {
        // Get average daily spend for this category
        $average = Expense::where('user_id', $userId)
            ->where('expense_category_id', $categoryId)
            ->where('date', '>=', now()->subDays(self::AVERAGE_DAYS))
            ->where('date', '<', $date)
            ->avg('amount');

        if ($average && $amount > ($average * (self::DAILY_SPENDING_THRESHOLD / 100))) {
            $category = $expense->expenseCategory->name;
            $title = "High Daily Spend in {$category}";
            $message = "You've spent KES " . number_format($amount) . " on {$category} today, " . 
                      "which is " . round(($amount / $average) * 100) . "% above your recent average.";
            
            Alert::createExpenseAlert(
                $userId,
                Alert::SEVERITY_WARNING,
                $title,
                $message,
                $expense,
                [
                    'amount' => $amount,
                    'average' => $average,
                    'percentage_above_average' => round(($amount / $average) * 100)
                ]
            );
        }
    }

    protected function checkUnusualCategorySpend($userId, $categoryId, $amount, $date)
    {
        // Get monthly average for this category
        $monthlyAverage = Expense::where('user_id', $userId)
            ->where('expense_category_id', $categoryId)
            ->where('date', '>=', now()->subMonths(3)->startOfMonth())
            ->where('date', '<', $date->copy()->startOfMonth())
            ->selectRaw('AVG(amount) as avg_amount, MONTH(date) as month')
            ->groupBy('month')
            ->avg('avg_amount');

        if ($monthlyAverage && $amount > ($monthlyAverage * 1.5)) {
            $category = $expense->expenseCategory->name;
            $title = "Unusual Spending in {$category}";
            $message = "You've spent KES " . number_format($amount) . " on {$category}, " . 
                      "which is significantly higher than your 3-month average of KES " . 
                      number_format($monthlyAverage) . ".";
            
            Alert::createExpenseAlert(
                $userId,
                Alert::SEVERITY_WARNING,
                $title,
                $message,
                $expense,
                [
                    'amount' => $amount,
                    'monthly_average' => $monthlyAverage,
                    'percentage_above_average' => round(($amount / $monthlyAverage) * 100)
                ]
            );
        }
    }

    protected function createBudgetWarningAlert(Budget $budget, $spent, $utilization)
    {
        $remaining = $budget->amount - $spent;
        $daysRemaining = now()->diffInDays(Carbon::parse($budget->ends_at));
        
        $title = "Budget Warning: {$budget->expenseCategory->name}";
        $message = "You've used {$utilization}% of your {$budget->expenseCategory->name} budget. " .
                  "KES " . number_format($remaining) . " remaining for {$daysRemaining} days.";

        return Alert::createBudgetAlert(
            $budget->user_id,
            Alert::SEVERITY_WARNING,
            $title,
            $message,
            $budget,
            [
                'budget_id' => $budget->id,
                'category' => $budget->expenseCategory->name,
                'budget_amount' => $budget->amount,
                'spent' => $spent,
                'remaining' => $remaining,
                'utilization' => $utilization,
                'threshold' => self::BUDGET_WARNING_THRESHOLD
            ]
        );
    }

    protected function createBudgetCriticalAlert(Budget $budget, $spent, $utilization)
    {
        $remaining = $budget->amount - $spent;
        $daysRemaining = now()->diffInDays(Carbon::parse($budget->ends_at));
        
        $title = "Budget Critical: {$budget->expenseCategory->name}";
        $message = "⚠️ You've used {$utilization}% of your {$budget->expenseCategory->name} budget. " .
                  "Only KES " . number_format($remaining) . " left for {$daysRemaining} days. " .
                  "Consider adjusting your spending.";

        return Alert::createBudgetAlert(
            $budget->user_id,
            Alert::SEVERITY_CRITICAL,
            $title,
            $message,
            $budget,
            [
                'budget_id' => $budget->id,
                'category' => $budget->expenseCategory->name,
                'budget_amount' => $budget->amount,
                'spent' => $spent,
                'remaining' => $remaining,
                'utilization' => $utilization,
                'threshold' => self::BUDGET_CRITICAL_THRESHOLD
            ]
        );
    }

    protected function createBudgetBreachAlert(Budget $budget, $spent, $utilization)
    {
        $overBudget = $spent - $budget->amount;
        
        $title = "🚨 Budget Breach: {$budget->expenseCategory->name}";
        $message = "You've exceeded your {$budget->expenseCategory->name} budget by KES " . 
                  number_format($overBudget) . ". ";
        
        if ($budget->auto_loan_on_breach) {
            $message .= "This amount will be recorded as a loan.";
        } else {
            $message .= "Consider reviewing your expenses or adjusting your budget.";
        }

        return Alert::createBudgetAlert(
            $budget->user_id,
            Alert::SEVERITY_CRITICAL,
            $title,
            $message,
            $budget,
            [
                'budget_id' => $budget->id,
                'category' => $budget->expenseCategory->name,
                'budget_amount' => $budget->amount,
                'spent' => $spent,
                'over_budget' => $overBudget,
                'utilization' => $utilization,
                'threshold' => self::BUDGET_BREACH_THRESHOLD,
                'auto_loan' => $budget->auto_loan_on_breach
            ]
        );
    }

    protected function createRapidBudgetBurnAlert(Budget $budget, $spent, $utilization)
    {
        $title = "⚠️ Rapid Budget Burn: {$budget->expenseCategory->name}";
        $message = "You've already used {$utilization}% of your {$budget->expenseCategory->name} budget " .
                  "in the first week. Current spending rate may lead to budget shortfall.";

        return Alert::createBudgetAlert(
            $budget->user_id,
            Alert::SEVERITY_WARNING,
            $title,
            $message,
            $budget,
            [
                'budget_id' => $budget->id,
                'category' => $budget->expenseCategory->name,
                'budget_amount' => $budget->amount,
                'spent' => $spent,
                'utilization' => $utilization,
                'days_elapsed' => now()->day,
                'is_rapid_burn' => true
            ]
        );
    }

    protected function createLoanForBudgetBreach(Budget $budget, $spent)
    {
        $overBudget = $spent - $budget->amount;
        
        // Create a loan record
        $loan = Loan::create([
            'user_id' => $budget->user_id,
            'amount' => $overBudget,
            'description' => "Auto-created loan for {$budget->expenseCategory->name} budget overage",
            'due_date' => now()->addMonth(),
            'interest_rate' => 0, // or your default interest rate
            'status' => 'active',
            'auto_created' => true,
            'related_budget_id' => $budget->id
        ]);

        // Create loan alert
        $title = "💳 Loan Created: {$budget->expenseCategory->name} Budget";
        $message = "A loan of KES " . number_format($overBudget) . " has been created " .
                  "to cover your {$budget->expenseCategory->name} budget overage. " .
                  "Due date: " . $loan->due_date->format('M d, Y');

        return Alert::createLoanAlert(
            $budget->user_id,
            $title,
            $message,
            $loan,
            [
                'loan_id' => $loan->id,
                'amount' => $loan->amount,
                'due_date' => $loan->due_date,
                'budget_id' => $budget->id,
                'category' => $budget->expenseCategory->name,
                'auto_created' => true
            ]
        );
    }

    /**
     * Mark all unread alerts as read for a user
     */
    public function markAllAsRead($userId)
    {
        return Alert::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    /**
     * Get unread alerts count for a user
     */
    public function getUnreadCount($userId)
    {
        return Alert::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get recent alerts for a user
     */
    public function getRecentAlerts($userId, $limit = 10)
    {
        return Alert::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
