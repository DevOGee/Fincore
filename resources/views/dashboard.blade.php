<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Top KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Net Worth -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-teal-500">
                    <div class="text-sm font-medium text-gray-500">Net Worth</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900">KES {{ number_format($netWorth, 2) }}</div>
                </div>

                <!-- Monthly Income -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-sm font-medium text-gray-500">Monthly Income</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900">KES {{ number_format($monthlyIncome, 2) }}</div>
                </div>

                <!-- Monthly Expenses -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <div class="text-sm font-medium text-gray-500">Monthly Expenses</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900">KES {{ number_format($monthlyExpenses, 2) }}
                    </div>
                </div>

                <!-- Savings Rate -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500">Savings Rate</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($savingsRate, 1) }}%</div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Income vs Expense Trend -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">6 Month Trend</h3>
                    <canvas id="trendChart" height="200"></canvas>
                </div>

                <!-- Wealth Composition -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">Wealth Composition</h3>
                    <div class="relative h-64">
                        <canvas id="wealthChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Budget & Transactions Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Active Budgets -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">Budget Status</h3>
                    <div class="space-y-4">
                        @forelse($budgetStatus as $budget)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-medium text-gray-700">{{ $budget['name'] }}</span>
                                    <span class="text-gray-500">{{ number_format($budget['percentage'], 1) }}%
                                        ({{ number_format($budget['spent']) }} /
                                        {{ number_format($budget['limit']) }})</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-{{ $budget['is_over'] ? 'red' : 'green' }}-600 h-2.5 rounded-full"
                                        style="width: {{ $budget['percentage'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">No active budgets found.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">Recent Transactions</h3>
                    <div class="flow-root">
                        <ul role="list" class="-my-5 divide-y divide-gray-200">
                            @forelse($recentTransactions as $transaction)
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            @if($transaction->type === 'income')
                                                <span
                                                    class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-green-100">
                                                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                                    </svg>
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-red-100">
                                                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $transaction->description }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($transaction->date)->format('M d, Y') }}
                                            </p>
                                        </div>
                                        <div
                                            class="inline-flex items-center text-sm font-semibold {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $transaction->type === 'income' ? '+' : '-' }} KES
                                            {{ number_format($transaction->amount, 2) }}
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="py-4 text-sm text-gray-500">No recent transactions.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Trend Chart
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            new Chart(trendCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($monthlyTrend->pluck('month')) !!},
                    datasets: [
                        {
                            label: 'Income',
                            data: {!! json_encode($monthlyTrend->pluck('income')) !!},
                            backgroundColor: 'rgba(34, 197, 94, 0.5)',
                            borderColor: 'rgba(34, 197, 94, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Expenses',
                            data: {!! json_encode($monthlyTrend->pluck('expense')) !!},
                            backgroundColor: 'rgba(239, 68, 68, 0.5)',
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Wealth Chart
            const wealthCtx = document.getElementById('wealthChart').getContext('2d');
            new Chart(wealthCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode(array_keys($wealthComposition)) !!},
                    datasets: [{
                        data: {!! json_encode(array_values($wealthComposition)) !!},
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.6)',  // Cash - Green
                            'rgba(59, 130, 246, 0.6)', // Savings - Blue
                            'rgba(168, 85, 247, 0.6)', // Investments - Purple
                            'rgba(239, 68, 68, 0.6)'   // Liabilities - Red
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
    </script>
</x-app-layout>