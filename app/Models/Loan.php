<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Loan extends Model
{
    use HasFactory;

    // Loan sources
    const SOURCE_BUDGET_BREACH = 'budget_breach';
    const SOURCE_CREDIT_CARD = 'credit_card';
    const SOURCE_MANUAL = 'manual';
    const SOURCE_OVERDRAFT = 'overdraft';
    const SOURCE_OTHER = 'other';

    // Statuses
    const STATUS_ACTIVE = 'active';
    const STATUS_CLEARED = 'cleared';
    const STATUS_OVERDUE = 'overdue';

    protected $fillable = [
        'user_id',
        'name',
        'source',
        'expense_category_id',
        'expense_id',
        'original_amount',
        'outstanding_balance',
        'interest_rate',
        'date',
        'due_date',
        'reference_number',
        'description',
        'status',
        'is_auto_created',
        'cleared_at',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'date' => 'date',
        'due_date' => 'date',
        'is_auto_created' => 'boolean',
        'cleared_at' => 'datetime',
    ];

    protected $appends = [
        'is_overdue',
        'progress_percentage',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(LoanRepayment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeCleared($query)
    {
        return $query->where('status', self::STATUS_CLEARED);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_OVERDUE);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getIsOverdueAttribute(): bool
    {
        return $this->status === self::STATUS_OVERDUE || 
               ($this->due_date && $this->due_date->isPast() && $this->outstanding_balance > 0);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->original_amount <= 0) {
            return 100;
        }
        
        $paid = $this->original_amount - $this->outstanding_balance;
        return min(100, max(0, ($paid / $this->original_amount) * 100));
    }

    // Methods
    public function recordPayment($amount, $paymentDate = null, $accountId = null, $notes = null): LoanRepayment
    {
        return DB::transaction(function () use ($amount, $paymentDate, $accountId, $notes) {
            // Create repayment record
            $repayment = $this->repayments()->create([
                'amount' => $amount,
                'payment_date' => $paymentDate ?? now(),
                'account_id' => $accountId,
                'notes' => $notes,
            ]);

            // Update loan balance
            $this->decrement('outstanding_balance', $amount);

            // Update status if fully paid
            if ($this->outstanding_balance <= 0) {
                $this->update([
                    'status' => self::STATUS_CLEARED,
                    'cleared_at' => now(),
                ]);
            }
            // Check if overdue
            elseif ($this->due_date && $this->due_date->isPast()) {
                $this->update(['status' => self::STATUS_OVERDUE]);
            }

            return $repayment;
        });
    }

    public static function createFromBudgetBreach($userId, $expense, $overBudgetAmount)
    {
        return DB::transaction(function () use ($userId, $expense, $overBudgetAmount) {
            $loan = self::create([
                'user_id' => $userId,
                'name' => 'Budget Overrun: ' . $expense->expenseCategory->name,
                'source' => self::SOURCE_BUDGET_BREACH,
                'expense_category_id' => $expense->expense_category_id,
                'expense_id' => $expense->id,
                'original_amount' => $overBudgetAmount,
                'outstanding_balance' => $overBudgetAmount,
                'interest_rate' => 0, // Can be configured
                'date' => $expense->date,
                'due_date' => now()->addMonth(),
                'status' => self::STATUS_ACTIVE,
                'is_auto_created' => true,
                'description' => 'Automatically created due to budget overrun',
            ]);

            // Trigger any events or notifications
            event(new \App\Events\LoanCreated($loan));

            return $loan;
        });
    }

    public static function getDashboardSummary($userId, $month = null, $year = null)
    {
        $month = $month ?: now()->month;
        $year = $year ?: now()->year;
        
        $startOfMonth = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        
        return [
            'total_active_loans' => self::where('user_id', $userId)
                ->where('status', self::STATUS_ACTIVE)
                ->count(),
                
            'total_outstanding' => self::where('user_id', $userId)
                ->where('status', self::STATUS_ACTIVE)
                ->sum('outstanding_balance'),
                
            'new_this_month' => self::where('user_id', $userId)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count(),
                
            'cleared_this_month' => self::where('user_id', $userId)
                ->where('status', self::STATUS_CLEARED)
                ->whereBetween('cleared_at', [$startOfMonth, $endOfMonth])
                ->count(),
                
            'total_interest_payable' => self::where('user_id', $userId)
                ->where('status', self::STATUS_ACTIVE)
                ->sum(DB::raw('outstanding_balance * (interest_rate / 100)')),
        ];
    }
}
