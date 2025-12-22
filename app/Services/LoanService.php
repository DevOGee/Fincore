<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Account;
use App\Models\LoanRepayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoanService
{
    /**
     * Create a new loan from a budget breach
     */
    public function createFromBudgetBreach(Expense $expense, float $overBudgetAmount): ?Loan
    {
        // Don't create loans for zero or negative amounts
        if ($overBudgetAmount <= 0) {
            return null;
        }

        return DB::transaction(function () use ($expense, $overBudgetAmount) {
            $loan = Loan::create([
                'user_id' => $expense->user_id,
                'name' => 'Budget Overrun: ' . $expense->expenseCategory->name,
                'source' => Loan::SOURCE_BUDGET_BREACH,
                'expense_category_id' => $expense->expense_category_id,
                'expense_id' => $expense->id,
                'original_amount' => $overBudgetAmount,
                'outstanding_balance' => $overBudgetAmount,
                'interest_rate' => 0, // Could be configurable
                'date' => $expense->date,
                'due_date' => now()->addMonth(),
                'status' => Loan::STATUS_ACTIVE,
                'is_auto_created' => true,
                'description' => 'Automatically created due to budget overrun',
            ]);

            // Create a system note on the expense
            $expense->notes = ($expense->notes ? $expense->notes . "\n\n" : '') . 
                            "[System] KES " . number_format($overBudgetAmount, 2) . 
                            " recorded as loan #{$loan->id} due to budget overrun";
            $expense->save();

            return $loan;
        });
    }

    /**
     * Record a loan repayment
     */
    public function recordRepayment(
        Loan $loan, 
        float $amount, 
        ?int $accountId = null, 
        ?string $paymentDate = null, 
        ?string $notes = null,
        ?string $referenceNumber = null
    ): LoanRepayment {
        return DB::transaction(function () use ($loan, $amount, $accountId, $paymentDate, $notes, $referenceNumber) {
            // Create the repayment record
            $repayment = $loan->repayments()->create([
                'amount' => $amount,
                'account_id' => $accountId,
                'payment_date' => $paymentDate ?: now(),
                'notes' => $notes,
                'reference_number' => $referenceNumber,
            ]);

            // Update the loan balance
            $loan->decrement('outstanding_balance', $amount);

            // Update account balance if account is provided
            if ($accountId) {
                $account = Account::find($accountId);
                if ($account) {
                    $account->decrement('balance', $amount);
                }
            }

            // Update loan status if fully paid
            if ($loan->outstanding_balance <= 0.01) { // Account for floating point precision
                $loan->update([
                    'status' => Loan::STATUS_CLEARED,
                    'outstanding_balance' => 0,
                    'cleared_at' => now(),
                ]);
            }
            // Check if overdue
            elseif ($loan->due_date && $loan->due_date->isPast()) {
                $loan->update(['status' => Loan::STATUS_OVERDUE]);
            }

            return $repayment;
        });
    }

    /**
     * Get dashboard summary for loans
     */
    public function getDashboardSummary(int $userId, ?int $month = null, ?int $year = null): array
    {
        $month = $month ?: now()->month;
        $year = $year ?: now()->year;
        
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        
        $activeLoans = Loan::forUser($userId)
            ->active()
            ->with('expenseCategory')
            ->get();
            
        $clearedLoans = Loan::forUser($userId)
            ->where('status', Loan::STATUS_CLEARED)
            ->whereBetween('cleared_at', [$startOfMonth, $endOfMonth])
            ->count();
            
        // Calculate total interest
        $totalInterest = $activeLoans->sum(function($loan) {
            return $loan->outstanding_balance * ($loan->interest_rate / 100);
        });
        
        // Get debt composition by category
        $debtComposition = $activeLoans->groupBy('expense_category_id')
            ->map(function($loans, $categoryId) {
                $first = $loans->first();
                return [
                    'category_id' => $categoryId,
                    'category_name' => $first->expenseCategory->name ?? 'Uncategorized',
                    'amount' => $loans->sum('outstanding_balance'),
                    'count' => $loans->count(),
                ];
            })
            ->sortByDesc('amount')
            ->values();
            
        // Get debt trend for the last 6 months
        $debtTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();
            
            // Get opening balance (loans created before this month)
            $opening = Loan::forUser($userId)
                ->where('date', '<', $start)
                ->where(function($q) use ($start) {
                    $q->where('status', '!=', Loan::STATUS_CLEARED)
                      ->orWhere('cleared_at', '>=', $start);
                })
                ->sum('outstanding_balance');
                
            // Get new loans this month
            $newLoans = Loan::forUser($userId)
                ->whereBetween('date', [$start, $end])
                ->sum('original_amount');
                
            // Get repayments this month
            $repayments = LoanRepayment::whereHas('loan', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->whereBetween('payment_date', [$start, $end])
                ->sum('amount');
                
            $debtTrend[] = [
                'month' => $date->format('M Y'),
                'opening' => $opening,
                'new_loans' => $newLoans,
                'repayments' => $repayments,
                'closing' => $opening + $newLoans - $repayments,
            ];
        }
        
        return [
            'summary' => [
                'total_active_loans' => $activeLoans->count(),
                'total_outstanding' => $activeLoans->sum('outstanding_balance'),
                'new_this_month' => Loan::forUser($userId)
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->count(),
                'cleared_this_month' => $clearedLoans,
                'total_interest_payable' => $totalInterest,
            ],
            'composition' => $debtComposition,
            'trend' => $debtTrend,
            'recent_activity' => $this->getRecentActivity($userId),
        ];
    }
    
    /**
     * Get recent loan activity
     */
    protected function getRecentActivity(int $userId, int $limit = 5): array
    {
        $loans = Loan::forUser($userId)
            ->with('expenseCategory')
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($loan) {
                return [
                    'id' => $loan->id,
                    'name' => $loan->name,
                    'category' => $loan->expenseCategory->name ?? 'Uncategorized',
                    'original_amount' => $loan->original_amount,
                    'outstanding_balance' => $loan->outstanding_balance,
                    'status' => $loan->status,
                    'due_date' => $loan->due_date?->format('Y-m-d'),
                    'updated_at' => $loan->updated_at,
                    'progress' => $loan->progress_percentage,
                ];
            });
            
        $repayments = LoanRepayment::whereHas('loan', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->with(['loan', 'account'])
            ->orderBy('payment_date', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($repayment) {
                return [
                    'id' => $repayment->id,
                    'loan_id' => $repayment->loan_id,
                    'loan_name' => $repayment->loan->name,
                    'amount' => $repayment->amount,
                    'payment_date' => $repayment->payment_date->format('Y-m-d'),
                    'account' => $repayment->account->name ?? 'N/A',
                ];
            });
            
        return [
            'loans' => $loans,
            'repayments' => $repayments,
        ];
    }
    
    /**
     * Get loan statistics
     */
    public function getLoanStatistics(int $userId): array
    {
        $totalBorrowed = Loan::forUser($userId)->sum('original_amount');
        $totalRepaid = LoanRepayment::whereHas('loan', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->sum('amount');
        
        $activeLoans = Loan::forUser($userId)->active()->count();
        $overdueLoans = Loan::forUser($userId)->overdue()->count();
        
        // Average interest rate (weighted by loan amount)
        $avgInterest = Loan::forUser($userId)
            ->active()
            ->selectRaw('SUM(original_amount * interest_rate) / NULLIF(SUM(original_amount), 0) as weighted_avg')
            ->value('weighted_avg') ?? 0;
            
        return [
            'total_borrowed' => $totalBorrowed,
            'total_repaid' => $totalRepaid,
            'active_loans' => $activeLoans,
            'overdue_loans' => $overdueLoans,
            'avg_interest_rate' => round($avgInterest, 2),
        ];
    }
}
