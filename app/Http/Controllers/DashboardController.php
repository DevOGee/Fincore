<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Saving;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Basic financial metrics
        $totalIncome = Income::where('user_id', $userId)->sum('amount');
        $totalExpenses = Expense::where('user_id', $userId)->sum('amount');
        $monthlyIncome = Income::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        $monthlyExpenses = Expense::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // Budget tracking
        $budgetStatus = Budget::where('user_id', $userId)
            ->where('status', 'approved')
            ->with([
                'expenses' => function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->whereBetween('date', [$startOfMonth, $endOfMonth]);
                }
            ])
            ->get()
            ->map(function ($budget) {
                $spent = $budget->expenses->sum('amount');
                $remaining = max(0, $budget->limit - $spent);
                $percentage = $budget->limit > 0 ? min(100, ($spent / $budget->limit) * 100) : 0;

                return [
                    'name' => $budget->category,
                    'limit' => $budget->limit,
                    'spent' => $spent,
                    'remaining' => $remaining,
                    'percentage' => $percentage,
                    'is_over' => $spent > $budget->limit
                ];
            });

        // Recent transactions
        $recentTransactions = collect()
            ->merge(
                Income::where('user_id', $userId)
                    ->select('id', 'amount', 'description', 'date', DB::raw("'income' as type"))
                    ->latest('date')
                    ->take(5)
                    ->get()
            )
            ->merge(
                Expense::where('user_id', $userId)
                    ->select('id', 'amount', 'description', 'date', DB::raw("'expense' as type"))
                    ->latest('date')
                    ->take(5)
                    ->get()
            )
            ->sortByDesc('date')
            ->take(5);

        // Monthly trend data
        $monthlyTrend = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $income = Income::where('user_id', $userId)
                ->whereBetween('date', [$start, $end])
                ->sum('amount');

            $expense = Expense::where('user_id', $userId)
                ->whereBetween('date', [$start, $end])
                ->sum('amount');

            $monthlyTrend->push([
                'month' => $month->format('M Y'),
                'income' => $income,
                'expense' => $expense,
                'savings' => $income - $expense
            ]);
        }

        // Expense categories data for the current month
        // Expense categories data for the current month
        $expenseCategories = Expense::where('expenses.user_id', $userId)
            ->whereBetween('expenses.date', [$startOfMonth, $endOfMonth])
            ->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->select('expense_categories.name as category', DB::raw('SUM(expenses.amount) as amount'))
            ->groupBy('expense_categories.name')
            ->orderByDesc('amount')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->category,
                    'amount' => (float) $item->amount
                ];
            });

        // If no categories found, provide some default categories with zero amounts
        if ($expenseCategories->isEmpty()) {
            $expenseCategories = collect([
                ['name' => 'Housing', 'amount' => 0],
                ['name' => 'Food', 'amount' => 0],
                ['name' => 'Transportation', 'amount' => 0],
                ['name' => 'Utilities', 'amount' => 0],
                ['name' => 'Others', 'amount' => 0]
            ]);
        }

        // New KPIs
        $legacyBalance = \App\Models\SavingsLegacy::where('user_id', $userId)->sum('amount');
        $totalSavings = Saving::where('user_id', $userId)->sum('balance'); // Assuming 'balance' exists in savings table
        $totalInvestments = \App\Models\Investment::where('user_id', $userId)->sum('current_value'); // Assuming investments table
        $totalDebts = \App\Models\Debt::where('user_id', $userId)->sum('amount'); // Assuming debts table

        $netWorth = ($totalIncome - $totalExpenses) + $totalInvestments - $totalDebts; // Simplified Net Worth
        // Better Net Worth: (Cash + Savings + Investments) - Debts
        // For now, let's use: (Total Income - Total Expenses) + Legacy + Investments - Debts
        // Actually, (Income - Expenses) is basically cash on hand + savings.
        // Let's refine: Net Worth = (Wallet Balance) + Legacy + Investments - Debts
        // Wallet Balance = Total Income - Total Expenses
        $walletBalance = $totalIncome - $totalExpenses;
        $netWorth = $walletBalance + $legacyBalance + $totalInvestments - $totalDebts;

        $savingsRate = $monthlyIncome > 0
            ? (($monthlyIncome - $monthlyExpenses) / $monthlyIncome) * 100
            : 0;

        // Wealth Composition Data
        $walletBalance = max(0, $totalIncome - $totalExpenses); // Cash on hand
        $totalSavingsCombined = $legacyBalance + $totalSavings;

        $wealthComposition = [
            'Cash' => $walletBalance,
            'Savings' => $totalSavingsCombined,
            'Investments' => $totalInvestments,
            'Liabilities' => $totalDebts
        ];

        // Governance Data
        $user = auth()->user();
        $auditCount = \App\Models\AuditLog::where('user_id', $userId)->count();
        $lastLogin = $user->last_login_at;
        $ips = \App\Models\InvestmentPolicyStatement::where('user_id', $userId)->first();
        $ipsCompliance = $ips ? $ips->checkCompliance() : null;

        return view('dashboard', [
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'monthlyIncome' => $monthlyIncome,
            'monthlyExpenses' => $monthlyExpenses,
            'monthlySavings' => $monthlyIncome - $monthlyExpenses,
            'budgetStatus' => $budgetStatus,
            'recentTransactions' => $recentTransactions,
            'monthlyTrend' => $monthlyTrend,
            'expenseCategories' => $expenseCategories,
            'legacyBalance' => $legacyBalance,
            'netWorth' => $netWorth,
            'savingsRate' => $savingsRate,
            'wealthComposition' => $wealthComposition,
            'totalInvestments' => $totalInvestments,
            'totalSavings' => $totalSavingsCombined,
            // Governance
            'auditCount' => $auditCount,
            'lastLogin' => $lastLogin,
            'ipsCompliance' => $ipsCompliance,
        ]);
    }
}
