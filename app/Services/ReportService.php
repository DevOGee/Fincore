<?php

namespace App\Services;

use App\Models\User;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Budget;
use App\Models\Loan;
use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ReportService
{
    protected $user;
    protected $startDate;
    protected $endDate;

    public function __construct(User $user, $startDate = null, $endDate = null)
    {
        $this->user = $user;
        $this->startDate = $startDate ? Carbon::parse($startDate) : now()->startOfMonth();
        $this->endDate = $endDate ? Carbon::parse($endDate) : now()->endOfMonth();
    }

    public function generateMonthlyReport($format = 'pdf')
    {
        $data = $this->getMonthlyReportData();
        return $this->export('monthly', $data, $format);
    }

    public function generateAnnualReport($year = null, $format = 'pdf')
    {
        $year = $year ?? now()->year;
        $this->startDate = Carbon::create($year)->startOfYear();
        $this->endDate = Carbon::create($year)->endOfYear();
        
        $data = $this->getAnnualReportData();
        return $this->export('annual', $data, $format);
    }

    public function generateExpenseReport($format = 'pdf')
    {
        $data = $this->getExpenseReportData();
        return $this->export('expense', $data, $format);
    }

    public function generateBudgetPerformanceReport($format = 'pdf')
    {
        $data = $this->getBudgetPerformanceData();
        return $this->export('budget-performance', $data, $format);
    }

    public function generateLoanReport($format = 'pdf')
    {
        $data = $this->getLoanReportData();
        return $this->export('loan', $data, $format);
    }

    public function generateNetWorthReport($format = 'pdf')
    {
        $data = $this->getNetWorthData();
        return $this->export('net-worth', $data, $format);
    }

    protected function getMonthlyReportData()
    {
        return [
            'period' => $this->getPeriodString(),
            'income' => $this->getIncomeSummary(),
            'expenses' => $this->getExpenseSummary(),
            'budget' => $this->getBudgetSummary(),
            'cash_flow' => $this->getCashFlow(),
            'loans' => $this->getLoanSummary(),
            'net_worth' => $this->getNetWorthChange(),
            'user' => $this->user
        ];
    }

    protected function getAnnualReportData()
    {
        $monthlyData = [];
        $current = $this->startDate->copy();
        
        while ($current->lte($this->endDate)) {
            $endOfMonth = $current->copy()->endOfMonth();
            $this->startDate = $current->copy();
            $this->endDate = $endOfMonth;
            
            $monthlyData[$current->format('M Y')] = $this->getMonthlyReportData();
            
            $current->addMonth()->startOfMonth();
        }
        
        return [
            'year' => $this->startDate->year,
            'monthly_data' => $monthlyData,
            'annual_summary' => [
                'total_income' => collect($monthlyData)->sum('income.total'),
                'total_expenses' => collect($monthlyData)->sum('expenses.total'),
                'net_savings' => collect($monthlyData)->sum('cash_flow.net_savings'),
                'net_worth_change' => $this->getNetWorthChange(true),
            ],
            'user' => $this->user
        ];
    }

    protected function getExpenseReportData()
    {
        $expenses = Expense::where('user_id', $this->user->id)
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->with('category')
            ->orderBy('date', 'desc')
            ->get();
            
        $byCategory = $expenses->groupBy('expense_category_id')
            ->map(function ($items) {
                return [
                    'category' => $items->first()->category->name,
                    'total' => $items->sum('amount'),
                    'count' => $items->count(),
                    'percentage' => 0, // Will be calculated after total is known
                ];
            });
            
        $totalExpenses = $byCategory->sum('total');
        
        if ($totalExpenses > 0) {
            $byCategory = $byCategory->map(function ($item) use ($totalExpenses) {
                $item['percentage'] = ($item['total'] / $totalExpenses) * 100;
                return $item;
            });
        }
        
        return [
            'period' => $this->getPeriodString(),
            'total_expenses' => $totalExpenses,
            'expense_count' => $expenses->count(),
            'expenses' => $expenses,
            'by_category' => $byCategory->sortByDesc('total')->values(),
            'user' => $this->user
        ];
    }

    protected function getBudgetPerformanceData()
    {
        $budgets = Budget::where('user_id', $this->user->id)
            ->with('category')
            ->get();
            
        $expenses = Expense::where('user_id', $this->user->id)
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->select('expense_category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('expense_category_id')
            ->pluck('total', 'expense_category_id');
        
        $performance = [];
        $totalBudget = 0;
        $totalSpent = 0;
        
        foreach ($budgets as $budget) {
            $spent = $expenses->get($budget->expense_category_id, 0);
            $remaining = max(0, $budget->amount - $spent);
            $utilization = $budget->amount > 0 ? ($spent / $budget->amount) * 100 : 0;
            
            $performance[] = [
                'category' => $budget->category->name,
                'budgeted' => $budget->amount,
                'spent' => $spent,
                'remaining' => $remaining,
                'utilization' => $utilization,
                'is_over_budget' => $spent > $budget->amount,
            ];
            
            $totalBudget += $budget->amount;
            $totalSpent += $spent;
        }
        
        $totalRemaining = max(0, $totalBudget - $totalSpent);
        $totalUtilization = $totalBudget > 0 ? ($totalSpent / $totalBudget) * 100 : 0;
        
        return [
            'period' => $this->getPeriodString(),
            'performance' => collect($performance)->sortByDesc('utilization')->values(),
            'summary' => [
                'total_budget' => $totalBudget,
                'total_spent' => $totalSpent,
                'total_remaining' => $totalRemaining,
                'total_utilization' => $totalUtilization,
                'is_overall_over_budget' => $totalSpent > $totalBudget,
            ],
            'user' => $this->user
        ];
    }

    protected function getLoanReportData()
    {
        $loans = Loan::where('user_id', $this->user->id)
            ->with(['repayments' => function($query) {
                $query->whereBetween('payment_date', [$this->startDate, $this->endDate]);
            }])
            ->get();
            
        $activeLoans = $loans->where('status', 'active');
        $repaidLoans = $loans->where('status', 'repaid');
        
        $totalBorrowed = $loans->sum('original_amount');
        $totalRepaid = $loans->sum('total_repaid');
        $totalOutstanding = $loans->sum('outstanding_balance');
        
        $recentRepayments = $loans->flatMap->repayments
            ->sortByDesc('payment_date')
            ->take(10);
        
        $bySource = $loans->groupBy('source')
            ->map(function ($loans, $source) {
                return [
                    'source' => $source,
                    'count' => $loans->count(),
                    'total_borrowed' => $loans->sum('original_amount'),
                    'total_repaid' => $loans->sum('total_repaid'),
                    'outstanding' => $loans->sum('outstanding_balance'),
                ];
            });
        
        return [
            'period' => $this->getPeriodString(),
            'active_loans' => $activeLoans->values(),
            'repaid_loans' => $repaidLoans->values(),
            'total_borrowed' => $totalBorrowed,
            'total_repaid' => $totalRepaid,
            'total_outstanding' => $totalOutstanding,
            'by_source' => $bySource->sortByDesc('outstanding')->values(),
            'recent_repayments' => $recentRepayments->values(),
            'user' => $this->user
        ];
    }

    protected function getNetWorthData()
    {
        $accounts = Account::where('user_id', $this->user->id)
            ->with('transactions')
            ->get();
            
        $assets = $accounts->where('type', 'asset');
        $liabilities = $accounts->where('type', 'liability');
        
        $totalAssets = $assets->sum('current_balance');
        $totalLiabilities = $liabilities->sum('current_balance');
        $netWorth = $totalAssets - $totalLiabilities;
        
        // Get historical data for the past 12 months
        $history = [];
        $current = now()->copy()->subMonths(11)->startOfMonth();
        
        while ($current <= now()) {
            $endOfMonth = $current->copy()->endOfMonth();
            
            $assetsAtDate = $accounts->where('type', 'asset')
                ->sum(function($account) use ($endOfMonth) {
                    $openingBalance = $account->opening_balance;
                    $transactions = $account->transactions
                        ->where('date', '<=', $endOfMonth)
                        ->sum('amount');
                    return $openingBalance + $transactions;
                });
                
            $liabilitiesAtDate = $accounts->where('type', 'liability')
                ->sum(function($account) use ($endOfMonth) {
                    $openingBalance = $account->opening_balance;
                    $transactions = $account->transactions
                        ->where('date', '<=', $endOfMonth)
                        ->sum('amount');
                    return $openingBalance + $transactions;
                });
                
            $history[] = [
                'month' => $current->format('M Y'),
                'assets' => $assetsAtDate,
                'liabilities' => $liabilitiesAtDate,
                'net_worth' => $assetsAtDate - $liabilitiesAtDate
            ];
            
            $current->addMonth();
        }
        
        return [
            'as_of_date' => now()->format('Y-m-d'),
            'assets' => [
                'total' => $totalAssets,
                'breakdown' => $assets->map(function($account) {
                    return [
                        'name' => $account->name,
                        'balance' => $account->current_balance,
                        'percentage' => $totalAssets > 0 ? ($account->current_balance / $totalAssets) * 100 : 0
                    ];
                })->sortByDesc('balance')->values()
            ],
            'liabilities' => [
                'total' => $totalLiabilities,
                'breakdown' => $liabilities->map(function($account) use ($totalLiabilities) {
                    return [
                        'name' => $account->name,
                        'balance' => $account->current_balance,
                        'percentage' => $totalLiabilities > 0 ? ($account->current_balance / $totalLiabilities) * 100 : 0
                    ];
                })->sortByDesc('balance')->values()
            ],
            'net_worth' => $netWorth,
            'history' => $history,
            'user' => $this->user
        ];
    }

    protected function getIncomeSummary()
    {
        return [
            'total' => Income::where('user_id', $this->user->id)
                ->whereBetween('date', [$this->startDate, $this->endDate])
                ->sum('amount'),
            'count' => Income::where('user_id', $this->user->id)
                ->whereBetween('date', [$this->startDate, $this->endDate])
                ->count(),
            'by_source' => Income::where('user_id', $this->user->id)
                ->whereBetween('date', [$this->startDate, $this->endDate])
                ->with('source')
                ->get()
                ->groupBy('income_source_id')
                ->map(function ($incomes, $sourceId) {
                    return [
                        'source' => $incomes->first()->source->name,
                        'total' => $incomes->sum('amount'),
                        'count' => $incomes->count(),
                        'percentage' => 0 // Will be calculated after total is known
                    ];
                })
                ->sortByDesc('total')
                ->values()
        ];
    }

    protected function getExpenseSummary()
    {
        $expenses = Expense::where('user_id', $this->user->id)
            ->whereBetween('date', [$this->startDate, $this->endDate]);
            
        $totalExpenses = $expenses->sum('amount');
        
        return [
            'total' => $totalExpenses,
            'count' => $expenses->count(),
            'by_category' => $expenses->with('category')
                ->get()
                ->groupBy('expense_category_id')
                ->map(function ($expenses, $categoryId) use ($totalExpenses) {
                    $categoryTotal = $expenses->sum('amount');
                    return [
                        'category' => $expenses->first()->category->name,
                        'total' => $categoryTotal,
                        'count' => $expenses->count(),
                        'percentage' => $totalExpenses > 0 ? ($categoryTotal / $totalExpenses) * 100 : 0
                    ];
                })
                ->sortByDesc('total')
                ->values()
        ];
    }

    protected function getBudgetSummary()
    {
        $budgets = Budget::where('user_id', $this->user->id)
            ->with('category')
            ->get();
            
        $expenses = Expense::where('user_id', $this->user->id)
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->select('expense_category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('expense_category_id')
            ->pluck('total', 'expense_category_id');
        
        $totalBudget = $budgets->sum('amount');
        $totalSpent = $expenses->sum();
        
        return [
            'total_budget' => $totalBudget,
            'total_spent' => $totalSpent,
            'remaining' => max(0, $totalBudget - $totalSpent),
            'utilization' => $totalBudget > 0 ? ($totalSpent / $totalBudget) * 100 : 0,
            'is_over_budget' => $totalSpent > $totalBudget,
            'categories' => $budgets->map(function($budget) use ($expenses) {
                $spent = $expenses->get($budget->expense_category_id, 0);
                return [
                    'category' => $budget->category->name,
                    'budgeted' => $budget->amount,
                    'spent' => $spent,
                    'remaining' => max(0, $budget->amount - $spent),
                    'utilization' => $budget->amount > 0 ? ($spent / $budget->amount) * 100 : 0,
                    'is_over_budget' => $spent > $budget->amount
                ];
            })
        ];
    }

    protected function getCashFlow()
    {
        $income = $this->getIncomeSummary();
        $expenses = $this->getExpenseSummary();
        
        return [
            'income' => $income['total'],
            'expenses' => $expenses['total'],
            'net_savings' => $income['total'] - $expenses['total'],
            'savings_rate' => $income['total'] > 0 ? (($income['total'] - $expenses['total']) / $income['total']) * 100 : 0
        ];
    }

    protected function getLoanSummary()
    {
        $loans = Loan::where('user_id', $this->user->id)
            ->where(function($query) {
                $query->where('status', 'active')
                    ->orBetween('date', [$this->startDate, $this->endDate]);
            })
            ->get();
            
        $activeLoans = $loans->where('status', 'active');
        $recentLoans = $loans->where('status', '!=', 'active')
            ->sortByDesc('date')
            ->take(5);
            
        return [
            'active_count' => $activeLoans->count(),
            'total_outstanding' => $activeLoans->sum('outstanding_balance'),
            'recent_loans' => $recentLoans->values(),
            'by_source' => $loans->groupBy('source')
                ->map(function($loans, $source) {
                    return [
                        'source' => $source,
                        'count' => $loans->count(),
                        'total_borrowed' => $loans->sum('original_amount'),
                        'total_repaid' => $loans->sum('total_repaid'),
                        'outstanding' => $loans->sum('outstanding_balance')
                    ];
                })
                ->sortByDesc('outstanding')
                ->values()
        ];
    }

    protected function getNetWorthChange($annual = false)
    {
        $currentPeriod = $this->getNetWorthData();
        
        if ($annual) {
            $this->startDate = now()->subYear()->startOfYear();
            $this->endDate = now()->subYear()->endOfYear();
        } else {
            $this->startDate = now()->subMonth()->startOfMonth();
            $this->endDate = now()->subMonth()->endOfMonth();
        }
        
        $previousPeriod = $this->getNetWorthData();
        
        $change = $currentPeriod['net_worth'] - $previousPeriod['net_worth'];
        $percentage = $previousPeriod['net_worth'] != 0 
            ? ($change / abs($previousPeriod['net_worth'])) * 100 
            : 0;
            
        return [
            'current' => $currentPeriod['net_worth'],
            'previous' => $previousPeriod['net_worth'],
            'change' => $change,
            'percentage' => $percentage,
            'is_positive' => $change >= 0
        ];
    }

    protected function export($type, $data, $format)
    {
        if ($format === 'excel') {
            return $this->exportToExcel($type, $data);
        }
        return $this->exportToPdf($type, $data);
    }

    protected function exportToPdf($type, $data)
    {
        $view = "reports.{$type}-pdf";
        
        if (!view()->exists($view)) {
            $view = 'reports.default-pdf';
        }
        
        $pdf = PDF::loadView($view, $data);
        
        // Create reports directory if it doesn't exist
        $directory = 'reports/' . $this->user->id;
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
        
        $filename = "{$directory}/FinCore_{$type}_report_" . now()->format('Y-m-d_His') . '.pdf';
        Storage::put($filename, $pdf->output());
        
        // Save to report history
        $report = $this->user->reports()->create([
            'name' => ucfirst($type) . ' Financial Report',
            'type' => $type,
            'format' => 'pdf',
            'file_path' => $filename,
            'parameters' => [
                'start_date' => $this->startDate->toDateString(),
                'end_date' => $this->endDate->toDateString(),
            ]
        ]);
        
        return [
            'url' => route('reports.download', $report->id),
            'filename' => "FinCore_{$type}_report_" . now()->format('Y-m-d') . '.pdf',
            'type' => 'pdf'
        ];
    }

    protected function exportToExcel($type, $data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Add report title and metadata
        $sheet->setCellValue('A1', 'FinCore ' . ucfirst($type) . ' Report')
            ->getStyle('A1')
            ->getFont()
            ->setBold(true)
            ->setSize(16);
            
        $sheet->setCellValue('A2', 'Generated on: ' . now()->format('F j, Y'));
        $sheet->setCellValue('A3', 'Period: ' . $this->getPeriodString());
        
        // Add report-specific content
        $this->{"build" . str_replace('-', '', ucwords($type, '-')) . "Excel"}($sheet, $data);
        
        // Auto-size columns
        foreach (range('A', $sheet->getHighestColumn()) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Create reports directory if it doesn't exist
        $directory = 'reports/' . $this->user->id;
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
        
        $filename = "{$directory}/FinCore_{$type}_report_" . now()->format('Y-m-d_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/' . $filename));
        
        // Save to report history
        $report = $this->user->reports()->create([
            'name' => ucfirst($type) . ' Financial Report',
            'type' => $type,
            'format' => 'excel',
            'file_path' => $filename,
            'parameters' => [
                'start_date' => $this->startDate->toDateString(),
                'end_date' => $this->endDate->toDateString(),
            ]
        ]);
        
        return [
            'url' => route('reports.download', $report->id),
            'filename' => "FinCore_{$type}_report_" . now()->format('Y-m-d') . '.xlsx',
            'type' => 'excel'
        ];
    }

    // Excel builders for each report type
    protected function buildMonthlyExcel($sheet, $data)
    {
        $row = 5;
        
        // Income Summary
        $sheet->setCellValue("A{$row}", 'Income Summary')
            ->getStyle("A{$row}")
            ->getFont()
            ->setBold(true)
            ->setSize(14);
        $row++;
        
        $sheet->setCellValue("A{$row}", 'Total Income:');
        $sheet->setCellValue("B{$row}", number_format($data['income']['total'], 2));
        $row++;
        
        // Add more income details...
        
        // Expenses Summary
        $row += 2;
        $sheet->setCellValue("A{$row}", 'Expense Summary')
            ->getStyle("A{$row}")
            ->getFont()
            ->setBold(true)
            ->setSize(14);
        $row++;
        
        $sheet->setCellValue("A{$row}", 'Total Expenses:');
        $sheet->setCellValue("B{$row}", number_format($data['expenses']['total'], 2));
        $row++;
        
        // Add more expense details...
        
        // Budget Summary
        $row += 2;
        $sheet->setCellValue("A{$row}", 'Budget Performance')
            ->getStyle("A{$row}")
            ->getFont()
            ->setBold(true)
            ->setSize(14);
        $row++;
        
        // Add budget details...
        
        // Cash Flow
        $row += 2;
        $sheet->setCellValue("A{$row}", 'Cash Flow')
            ->getStyle("A{$row}")
            ->getFont()
            ->setBold(true)
            ->setSize(14);
        $row++;
        
        $sheet->setCellValue("A{$row}", 'Net Savings:');
        $sheet->setCellValue("B{$row}", number_format($data['cash_flow']['net_savings'], 2));
        $sheet->setCellValue("C{$row}", "(" . number_format($data['cash_flow']['savings_rate'], 2) . "% of income)");
    }
    
    // Other report type builders would go here...
    protected function buildAnnualExcel($sheet, $data) { /* ... */ }
    protected function buildExpenseExcel($sheet, $data) { /* ... */ }
    protected function buildBudgetPerformanceExcel($sheet, $data) { /* ... */ }
    protected function buildLoanExcel($sheet, $data) { /* ... */ }
    protected function buildNetWorthExcel($sheet, $data) { /* ... */ }

    protected function getPeriodString()
    {
        if ($this->startDate->isSameDay($this->startDate->copy()->startOfMonth()) && 
            $this->endDate->isSameDay($this->endDate->copy()->endOfMonth())) {
            return $this->startDate->format('F Y');
        }
        
        return $this->startDate->format('M j, Y') . ' to ' . $this->endDate->format('M j, Y');
    }
}
