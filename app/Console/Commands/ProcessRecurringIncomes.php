<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RecurringIncome;
use App\Models\Income;
use Carbon\Carbon;

class ProcessRecurringIncomes extends Command
{
    protected $signature = 'incomes:process-recurring';
    protected $description = 'Process recurring income profiles and generate income entries';

    public function handle()
    {
        $today = Carbon::today();
        $profiles = RecurringIncome::where('active', true)
            ->where('start_date', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $today);
            })
            ->where(function ($query) use ($today) {
                $query->whereNull('next_run_date')
                    ->orWhere('next_run_date', '<=', $today);
            })
            ->get();

        foreach ($profiles as $profile) {
            // Create Income
            Income::create([
                'user_id' => $profile->user_id,
                'income_source_id' => $profile->income_source_id,
                'source' => $profile->incomeSource->name,
                'amount' => $profile->amount,
                'date' => $today,
                'category' => 'Recurring',
                'description' => 'Auto-generated from recurring profile',
            ]);

            // Update Next Run Date
            $nextRun = $profile->next_run_date ? Carbon::parse($profile->next_run_date) : Carbon::parse($profile->start_date);

            switch ($profile->frequency) {
                case 'monthly':
                    $nextRun->addMonth();
                    break;
                case 'weekly':
                    $nextRun->addWeek();
                    break;
                case 'quarterly':
                    $nextRun->addQuarter();
                    break;
                case 'annually':
                    $nextRun->addYear();
                    break;
            }

            $profile->update(['next_run_date' => $nextRun]);
            $this->info("Processed profile ID: {$profile->id}");
        }

        $this->info('Recurring incomes processed successfully.');
    }
}
