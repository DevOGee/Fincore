@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-6">
            {{ __('Income Dashboard') }}
        </h2>

        <!-- Smart Insights Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-teal">
                <div class="text-sm font-medium text-gray-500 truncate">Monthly Total ({{ \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->format('M Y') }})</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900">KES {{ number_format($monthlyTotal, 2) }}</div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                <div class="text-sm font-medium text-gray-500 truncate">YTD Total</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900">KES {{ number_format($ytdTotal, 2) }}</div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                <div class="text-sm font-medium text-gray-500 truncate">Annual Total</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900">KES {{ number_format($annualTotal, 2) }}</div>
            </div>
            <div
                class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 {{ $momGrowth >= 0 ? 'border-green-500' : 'border-red-500' }}">
                <div class="text-sm font-medium text-gray-500 truncate">MoM Growth</div>
                <div class="mt-1 text-2xl font-semibold {{ $momGrowth >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $momGrowth >= 0 ? '+' : '' }}{{ number_format($momGrowth, 1) }}%
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-gold">
                <div class="text-sm font-medium text-gray-500 truncate">Projected Annual</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900">KES {{ number_format($projectedAnnual, 2) }}</div>
            </div>
        </div>

        <!-- Analytics Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Income Trend -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Income Trend (Last 12 Months)</h3>
                <canvas id="incomeTrendChart" height="200"></canvas>
            </div>

            <!-- Income Distribution & Top Sources -->
            <div class="grid grid-cols-1 gap-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Income Distribution by Type</h3>
                    <div class="h-64">
                        <canvas id="incomeDistributionChart"></canvas>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Top 5 Income Sources (YTD)</h3>
                    <canvas id="topSourcesChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Filters & Actions -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
            <div class="flex space-x-4">
                <form method="GET" action="{{ route('incomes.index') }}" class="flex space-x-2">
                    <input type="month" name="month" value="{{ request('month') }}"
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <select name="source_id"
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">All Sources</option>
                        @foreach($incomeSources as $source)
                            <option value="{{ $source->id }}" {{ request('source_id') == $source->id ? 'selected' : '' }}>
                                {{ $source->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit"
                        class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700">Filter</button>
                    @if(request()->has('month') || request()->has('source_id'))
                        <a href="{{ route('incomes.index') }}"
                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">Clear</a>
                    @endif
                </form>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('income_sources.index') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
                    Manage Sources
                </a>
                <a href="{{ route('recurring_incomes.index') }}"
                    class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded shadow">
                    Recurring Income
                </a>
                <a href="{{ route('incomes.create') }}"
                    class="bg-gold hover:bg-orange-600 text-white font-bold py-2 px-4 rounded shadow">
                    Add Income
                </a>
            </div>
        </div>

        <!-- Income List -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Source</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($incomes as $income)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $income->date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $income->incomeSource ? $income->incomeSource->name : $income->source }}</div>
                                    <div class="text-xs text-gray-500">{{ $income->account_credited }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $income->category }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900">KES
                                    {{ number_format($income->amount, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('incomes.edit', $income) }}"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <form action="{{ route('incomes.destroy', $income) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No income records
                                    found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $incomes->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data from Controller
            const trendData = @json($incomeTrend);
            const distributionData = @json($incomeDistribution);
            const topSourcesData = @json($topSources);

            // 1. Income Trend Chart
            const trendCtx = document.getElementById('incomeTrendChart').getContext('2d');
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: Object.keys(trendData),
                    datasets: [{
                        label: 'Total Income',
                        data: Object.values(trendData),
                        borderColor: '#0D9488', // Teal
                        backgroundColor: 'rgba(13, 148, 136, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // 2. Income Distribution Chart
            const distCtx = document.getElementById('incomeDistributionChart').getContext('2d');
            new Chart(distCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(distributionData),
                    datasets: [{
                        data: Object.values(distributionData),
                        backgroundColor: [
                            '#0D9488', '#F59E0B', '#3B82F6', '#8B5CF6', '#EC4899', '#6B7280'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' }
                    }
                }
            });

            // 3. Top Sources Chart
            const topCtx = document.getElementById('topSourcesChart').getContext('2d');
            new Chart(topCtx, {
                type: 'bar',
                data: {
                    labels: topSourcesData.map(item => item.name),
                    datasets: [{
                        label: 'YTD Amount',
                        data: topSourcesData.map(item => item.total),
                        backgroundColor: '#F59E0B', // Gold
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    indexAxis: 'y',
                    scales: {
                        x: { beginAtZero: true }
                    }
                }
            });
        });
    </script>
@endsection