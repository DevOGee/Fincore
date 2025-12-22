<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Budget::observe(\App\Observers\BudgetObserver::class);
        \App\Models\Income::observe(\App\Observers\IncomeObserver::class);
    }
}
