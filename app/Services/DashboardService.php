<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Budget;

class DashboardService
{
    public function getDashboardData($userId)
    {
        return Cache::remember("dashboard_data_{$userId}", now()->addHours(6), function() use ($userId) {
            $now = now();
            $startOfMonth = $now->copy()->startOfMonth();
            $endOfMonth = $now->copy()->endOfMonth();

            return [
                'metrics' => $this->getMetrics($userId, $startOfMonth, $endOfMonth),
                'budgetStatus' => $this->getBudgetStatus($userId, $startOfMonth, $endOfMonth),
                'recentTransactions' => $this->getRecentTransactions($userId),
                'monthlyTrend' => $this->getMonthlyTrend($userId, $now),
                'expenseCategories' => $this->getExpenseCategories($userId, $startOfMonth, $endOfMonth)
            ];
        });
    }

    private function getMetrics($userId, $start, $end)
    {
        $income = Income::where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->sum('amount');
            
        $expense = Expense::where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->sum('amount');
            
        $totalIncome = Income::where('user_id', $userId)->sum('amount');
        $totalExpenses = Expense::where('user_id', $userId)->sum('amount');
        
        return [
            'monthlyIncome' => $income,
            'monthlyExpenses' => $expense,
            'monthlySavings' => $income - $expense,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'netWorth' => $totalIncome - $totalExpenses,
            'savingsRate' => $income > 0 ? (($income - $expense) / $income) * 100 : 0
        ];
    }

    private function getBudgetStatus($userId, $start, $end)
    {
        return Budget::with(['expenses' => function($q) use ($start, $end) {
                $q->whereBetween('date', [$start, $end]);
            }])
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->get()
            ->map(function($budget) {
                $spent = $budget->expenses->sum('amount');
                $percentage = $budget->limit > 0 ? min(100, ($spent / $budget->limit) * 100) : 0;
                
                return [
                    'name' => $budget->category,
                    'limit' => $budget->limit,
                    'spent' => $spent,
                    'remaining' => max(0, $budget->limit - $spent),
                    'percentage' => $percentage,
                    'is_over' => $spent > $budget->limit
                ];
            });
    }

    private function getRecentTransactions($userId, $limit = 5)
    {
        $incomes = Income::where('user_id', $userId)
            ->select('id', 'amount', 'description', 'date', DB::raw("'income' as type"))
            ->latest('date')
            ->take($limit)
            ->get();

        $expenses = Expense::where('user_id', $userId)
            ->select('id', 'amount', 'description', 'date', DB::raw("'expense' as type"))
            ->latest('date')
            ->take($limit)
            ->get();

        return $incomes->concat($expenses)
            ->sortByDesc('date')
            ->take($limit)
            ->values();
    }

    private function getMonthlyTrend($userId, $now, $months = 6)
    {
        return collect(range($months - 1, 0))
            ->map(function($monthsAgo) use ($userId, $now) {
                $month = $now->copy()->subMonths($monthsAgo);
                $start = $month->copy()->startOfMonth();
                $end = $month->copy()->endOfMonth();
                
                return [
                    'month' => $month->format('M Y'),
                    'income' => (float) Income::where('user_id', $userId)
                        ->whereBetween('date', [$start, $end])
                        ->sum('amount'),
                    'expense' => (float) Expense::where('user_id', $userId)
                        ->whereBetween('date', [$start, $end])
                        ->sum('amount')
                ];
            });
    }

    private function getExpenseCategories($userId, $start, $end)
    {
        $categories = Expense::where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->select('expense_categories.name', DB::raw('SUM(expenses.amount) as amount'))
            ->groupBy('expense_categories.name')
            ->orderByDesc('amount')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->name,
                    'amount' => (float) $item->amount
                ];
            });

        return $categories->isEmpty() ? collect([
            ['name' => 'Housing', 'amount' => 0],
            ['name' => 'Food', 'amount' => 0],
            ['name' => 'Transportation', 'amount' => 0],
            ['name' => 'Utilities', 'amount' => 0],
            ['name' => 'Others', 'amount' => 0]
        ]) : $categories;
    }
}