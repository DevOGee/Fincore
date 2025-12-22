<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Users table
        if (!Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Financial tables
        $tables = [
            'incomes',
            'expenses',
            'savings',
            'investments',
            'budgets',
            'savings_goals',
            'income_sources',
            'expense_categories',
            'recurring_incomes',
            'recurring_expenses',
            'reports',
            'user_preferences',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'incomes',
            'expenses',
            'savings',
            'investments',
            'budgets',
            'savings_goals',
            'income_sources',
            'expense_categories',
            'recurring_incomes',
            'recurring_expenses',
            'reports',
            'user_preferences',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
