<?php

namespace App\Observers;

use App\Models\Income;
use App\Models\SavingsAllocationRule;
use App\Models\SavingsLegacy;
use Carbon\Carbon;

class IncomeObserver
{
    /**
     * Handle the Income "created" event.
     */
    public function created(Income $income): void
    {
        // Fetch active allocation rules for the user
        $rules = SavingsAllocationRule::where('user_id', $income->user_id)
            ->where('is_active', true)
            ->get();

        if ($rules->isEmpty()) {
            return;
        }

        $date = Carbon::parse($income->date);
        $month = $date->format('Y-m');
        $year = $date->year;
        $quarter = 'Q' . $date->quarter;

        foreach ($rules as $rule) {
            $amount = $income->amount * ($rule->percentage / 100);

            SavingsLegacy::create([
                'user_id' => $income->user_id,
                'income_id' => $income->id,
                'category' => $rule->category,
                'amount' => $amount,
                'percentage_applied' => $rule->percentage,
                'date' => $income->date,
                'month' => $month,
                'quarter' => $quarter,
                'year' => $year,
            ]);
        }
    }
}
