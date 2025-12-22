@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Expense Analytics</h1>
        <div class="flex items-center space-x-4">
            <input 
                type="month" 
                id="monthSelector" 
                class="rounded-md border-gray-300 shadow-sm"
                value="{{ now()->format('Y-m') }}"
            >
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6" id="kpiCards">
        <!-- Will be populated by JavaScript -->
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Trend -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Monthly Expense Trend</h3>
            <canvas id="monthlyTrendChart" height="300"></canvas>
        </div>

        <!-- Category Distribution -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Expenses by Category</h3>
            <canvas id="categoryChart" height="300"></canvas>
        </div>

        <!-- Budget vs Actual -->
        <div class="bg-white p-4 rounded-lg shadow col-span-2">
            <h3 class="text-lg font-semibold mb-4">Budget vs Actual by Category</h3>
            <canvas id="budgetVsActualChart" height="300"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let monthlyTrendChart, categoryChart, budgetVsActualChart;
    const monthSelector = document.getElementById('monthSelector');

    // Format currency
    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('en-KE', {
            style: 'currency',
            currency: 'KES',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    };

    // Load all data
    async function loadDashboardData() {
        const month = monthSelector.value;
        await Promise.all([
            loadKPIs(month),
            loadMonthlyTrend(month),
            loadCategoryData(month),
            loadBudgetVsActual(month)
        ]);
    }

    // Load KPI Cards
    async function loadKPIs(month) {
        try {
            const response = await fetch(`/api/expenses/monthly-summary?month=${month}`);
            const data = await response.json();
            
            const kpiCards = `
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-gray-500 text-sm font-medium">Total Budget</h3>
                    <p class="text-2xl font-bold">${formatCurrency(data.total_budget)}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-gray-500 text-sm font-medium">Total Expenses</h3>
                    <p class="text-2xl font-bold">${formatCurrency(data.total_expenses)}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-gray-500 text-sm font-medium">Remaining</h3>
                    <p class="text-2xl font-bold ${data.remaining_budget < 0 ? 'text-red-600' : 'text-green-600'}">
                        ${formatCurrency(data.remaining_budget)}
                    </p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-gray-500 text-sm font-medium">Status</h3>
                    <p class="text-2xl font-bold ${data.over_budget ? 'text-red-600' : 'text-green-600'}">
                        ${data.over_budget ? 'Over Budget' : 'Within Budget'}
                        ${data.over_budget ? '⚠️' : '✓'}
                    </p>
                </div>`;

            document.getElementById('kpiCards').innerHTML = kpiCards;
        } catch (error) {
            console.error('Error loading KPIs:', error);
        }
    }

    // Load Monthly Trend Chart
    async function loadMonthlyTrend(month) {
        try {
            const response = await fetch(`/api/expenses/daily-trend?month=${month}`);
            const data = await response.json();

            const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
            
            if (monthlyTrendChart) {
                monthlyTrendChart.destroy();
            }

            monthlyTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Daily Expenses',
                            data: data.expenses,
                            borderColor: 'rgb(99, 102, 241)',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Cumulative',
                            data: data.cumulative,
                            borderColor: 'rgb(16, 185, 129)',
                            borderDash: [5, 5],
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Daily Budget',
                            data: data.budget_line,
                            borderColor: 'rgba(239, 68, 68, 0.7)',
                            borderWidth: 1,
                            borderDash: [3, 3],
                            pointRadius: 0,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'KES ' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += 'KES ' + context.parsed.y.toLocaleString();
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error loading monthly trend:', error);
        }
    }

    // Load Category Chart
    async function loadCategoryData(month) {
        try {
            const response = await fetch(`/api/expenses/by-category?month=${month}`);
            const data = await response.json();

            const ctx = document.getElementById('categoryChart').getContext('2d');
            
            if (categoryChart) {
                categoryChart.destroy();
            }

            categoryChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.spent,
                        backgroundColor: [
                            '#3b82f6', '#10b981', '#f59e0b', '#ef4444', 
                            '#8b5cf6', '#ec4899', '#14b8a6', '#f97316',
                            '#6366f1', '#06b6d4', '#d946ef', '#f43f5e'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: KES ${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error loading category data:', error);
        }
    }

    // Load Budget vs Actual Chart
    async function loadBudgetVsActual(month) {
        try {
            const response = await fetch(`/api/expenses/by-category?month=${month}`);
            const data = await response.json();

            const ctx = document.getElementById('budgetVsActualChart').getContext('2d');
            
            if (budgetVsActualChart) {
                budgetVsActualChart.destroy();
            }

            budgetVsActualChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Budget',
                            data: data.budget,
                            backgroundColor: 'rgba(59, 130, 246, 0.7)',
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 1
                        },
                        {
                            label: 'Spent',
                            data: data.spent,
                            backgroundColor: 'rgba(16, 185, 129, 0.7)',
                            borderColor: 'rgb(16, 185, 129)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            stacked: false,
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'KES ' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += 'KES ' + context.parsed.y.toLocaleString();
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error loading budget vs actual:', error);
        }
    }

    // Event Listeners
    monthSelector.addEventListener('change', loadDashboardData);

    // Initial load
    document.addEventListener('DOMContentLoaded', loadDashboardData);
</script>
@endpush
@endsection
