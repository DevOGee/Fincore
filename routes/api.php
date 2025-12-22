<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExpenseAnalyticsController;
use App\Http\Controllers\Api\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Expense Analytics API Routes
Route::middleware('auth:sanctum')->group(function () {
    // Expense Analytics Routes
    Route::get('/expenses/monthly-summary', [ExpenseAnalyticsController::class, 'monthlySummary']);
    Route::get('/expenses/daily-trend', [ExpenseAnalyticsController::class, 'dailyTrend']);
    Route::get('/expenses/by-category', [ExpenseAnalyticsController::class, 'byCategory']);
    
    // Report Routes
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index']);
        Route::post('/generate', [ReportController::class, 'generate']);
        Route::get('/history', [ReportController::class, 'history']);
        Route::get('/{id}/download', [ReportController::class, 'download']);
        Route::delete('/{id}', [ReportController::class, 'destroy']);
    });
});
