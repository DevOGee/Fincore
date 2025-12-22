<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Financial Modules
    Route::resource('investments', \App\Http\Controllers\InvestmentController::class);

    // Budget Custom Routes
    Route::get('budgets/import', [\App\Http\Controllers\BudgetController::class, 'importForm'])->name('budgets.import.form');
    Route::post('budgets/import', [\App\Http\Controllers\BudgetController::class, 'import'])->name('budgets.import');
    Route::get('budgets/export', [\App\Http\Controllers\BudgetController::class, 'exportTemplate'])->name('budgets.export');
    Route::post('budgets/{budget}/approve', [\App\Http\Controllers\BudgetController::class, 'approve'])->name('budgets.approve');
    Route::post('budgets/{budget}/disapprove', [\App\Http\Controllers\BudgetController::class, 'disapprove'])->name('budgets.disapprove');
    Route::resource('budgets', \App\Http\Controllers\BudgetController::class);

    Route::resource('savings', \App\Http\Controllers\SavingController::class);
    Route::resource('savings-goals', \App\Http\Controllers\SavingsGoalController::class);
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class);
    Route::resource('incomes', \App\Http\Controllers\IncomeController::class);
    Route::resource('income_sources', \App\Http\Controllers\IncomeSourceController::class);
    Route::resource('recurring_incomes', \App\Http\Controllers\RecurringIncomeController::class);
    Route::resource('expense_categories', \App\Http\Controllers\ExpenseCategoryController::class); // Likely underscore too based on pattern

    // Charts
    Route::get('/charts/monthly-expenses', [\App\Http\Controllers\ChartController::class, 'monthlyExpenses'])->name('charts.monthly-expenses');
    Route::get('/charts/monthly-income', [\App\Http\Controllers\ChartController::class, 'monthlyIncome'])->name('charts.monthly-income');
    Route::get('/charts/cash-flow', [\App\Http\Controllers\ChartController::class, 'cashFlow'])->name('charts.cash-flow');

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
});

require __DIR__ . '/auth.php';
