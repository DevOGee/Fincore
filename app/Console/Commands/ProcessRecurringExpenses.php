<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessRecurringExpenses extends Command
{
    protected $signature = 'expenses:process-recurring';

    protected $description = 'Process recurring expense profiles and create expense entries';

    public function handle()
    {
        $profiles = \App\Models\RecurringExpense::where('active', true)
            ->where('next_run_date', '<=', now())
            ->get();

        foreach ($profiles as $profile) {
            // Check if end_date has passed
            if ($profile->end_date && $profile->end_date < now()) {
                $profile->update(['active' => false]);
                continue;
            }

            // Create the expense entry (note: budget enforcement is bypassed for auto-generated entries)
            \App\Models\Expense::create([
                'user_id' => $profile->user_id,
                'expense_category_id' => $profile->expense_category_id,
                'category' => $profile->expenseCategory->name,
                'amount' => $profile->amount,
                'date' => now(),
                'recipient' => $profile->recipient,
                'payment_method' => $profile->payment_method,
                'description' => $profile->description . ' (Auto-generated)',
                'is_recurring' => true,
            ]);

            // Update next_run_date
            $nextDate = match ($profile->frequency) {
                'weekly' => $profile->next_run_date->addWeek(),
                'monthly' => $profile->next_run_date->addMonth(),
                'quarterly' => $profile->next_run_date->addMonths(3),
                'annually' => $profile->next_run_date->addYear(),
                default => $profile->next_run_date->addMonth(),
            };

            $profile->update(['next_run_date' => $nextDate]);

            $this->info("Processed profile ID: {$profile->id}");
        }

        $this->info('Recurring expenses processed successfully.');
    }
}
