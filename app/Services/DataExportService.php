<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DataExportService
{
    /**
     * Export all user data as JSON in a ZIP file
     */
    public function export(User $user): string
    {
        $data = [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'created_at' => $user->created_at->toDateTimeString(),
            ],
            'preferences' => $user->preference ? $user->preference->toArray() : null,
            'incomes' => $user->hasMany(\App\Models\Income::class, 'user_id')->get()->toArray(),
            'expenses' => $user->hasMany(\App\Models\Expense::class, 'user_id')->get()->toArray(),
            'budgets' => $user->hasMany(\App\Models\Budget::class, 'user_id')->get()->toArray(),
            'savings' => $user->hasMany(\App\Models\Saving::class, 'user_id')->get()->toArray(),
            'savings_goals' => $user->hasMany(\App\Models\SavingsGoal::class, 'user_id')->get()->toArray(),
            'investments' => $user->hasMany(\App\Models\Investment::class, 'user_id')->get()->toArray(),
            'reports' => $user->reports()->get()->toArray(),
        ];

        $fileName = "user_data_{$user->id}_" . now()->format('Y-m-d_His') . ".json";
        $filePath = "exports/{$fileName}";

        Storage::disk('local')->put($filePath, json_encode($data, JSON_PRETTY_PRINT));

        return storage_path("app/{$filePath}");
    }

    /**
     * Soft delete all user financial data
     */
    public function softDeleteUserData(User $user): void
    {
        // Soft delete financial records
        \App\Models\Income::where('user_id', $user->id)->delete();
        \App\Models\Expense::where('user_id', $user->id)->delete();
        \App\Models\Budget::where('user_id', $user->id)->delete();
        \App\Models\Saving::where('user_id', $user->id)->delete();
        \App\Models\SavingsGoal::where('user_id', $user->id)->delete();
        \App\Models\Investment::where('user_id', $user->id)->delete();
        \App\Models\Report::where('user_id', $user->id)->delete();
        \App\Models\RecurringIncome::where('user_id', $user->id)->delete();
        \App\Models\RecurringExpense::where('user_id', $user->id)->delete();
        \App\Models\IncomeSource::where('user_id', $user->id)->delete();
        \App\Models\ExpenseCategory::where('user_id', $user->id)->delete();

        // Soft delete user preference
        if ($user->preference) {
            $user->preference->delete();
        }

        // Soft delete user
        $user->delete();
    }
}
