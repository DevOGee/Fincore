<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanRepayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'account_id',
        'amount',
        'payment_date',
        'notes',
        'reference_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    // Relationships
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    // Scopes
    public function scopeForLoan($query, $loanId)
    {
        return $query->where('loan_id', $loanId);
    }

    public function scopeForAccount($query, $accountId)
    {
        return $query->where('account_id', $accountId);
    }

    public function scopeDateBetween($query, $startDate, $endDate = null)
    {
        $endDate = $endDate ?: $startDate;
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    // Methods
    public static function getTotalRepaid($loanId)
    {
        return self::where('loan_id', $loanId)->sum('amount');
    }

    public static function getRepaymentSummary($userId, $startDate = null, $endDate = null)
    {
        $query = self::whereHas('loan', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });

        if ($startDate) {
            $query->where('payment_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('payment_date', '<=', $endDate);
        }

        return $query->selectRaw('COUNT(*) as count, SUM(amount) as total')
            ->first()
            ->toArray();
    }
}
