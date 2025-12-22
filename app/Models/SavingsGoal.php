<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SavingsGoal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'target_amount',
        'current_amount',
        'deadline',
        'description',
        'monthly_contribution',
        'funding_source',
        'start_date',
    ];

    protected $casts = [
        'deadline' => 'date',
        'start_date' => 'date',
        'monthly_contribution' => 'decimal:2',
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function completionPercentage()
    {
        if ($this->target_amount <= 0) {
            return 0;
        }
        return min(round(($this->current_amount / $this->target_amount) * 100), 100);
    }

    public function remainingAmount()
    {
        return max($this->target_amount - $this->current_amount, 0);
    }

    public function monthsRemaining()
    {
        if (!$this->deadline || $this->deadline->isPast()) {
            return 0;
        }
        return now()->diffInMonths($this->deadline);
    }

    public function requiredMonthlyContribution()
    {
        $months = $this->monthsRemaining();
        if ($months <= 0) {
            return $this->remainingAmount();
        }
        return $this->remainingAmount() / $months;
    }

    public function feasibilityScore()
    {
        $required = $this->requiredMonthlyContribution();
        if ($required <= 0)
            return 100; // Already met or no deadline

        $currentPace = $this->monthly_contribution ?? 0;

        // Feasibility = (Current Pace / Required Pace) * 100
        // Cap at 100
        return min(round(($currentPace / $required) * 100), 100);
    }
}
