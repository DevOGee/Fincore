@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Savings Goals</h1>
            <p class="text-gray-500 mt-1">Track and manage your financial targets</p>
        </div>
        <a href="{{ route('savings.create') }}"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition duration-200 ease-in-out flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add New Goal
        </a>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Saved -->
        <div class="bg-white rounded-2xl shadow-sm p-6 relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Saved</p>
                <h2 class="text-3xl font-bold text-gray-900 mt-2">KES {{ number_format($totalSavings, 0) }}</h2>
                <p class="text-xs text-gray-400 mt-1">Across all goals</p>
            </div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-green-50 to-transparent opacity-50"></div>
        </div>

        <!-- Total Target -->
        <div class="bg-white rounded-2xl shadow-sm p-6 relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Target</p>
                <h2 class="text-3xl font-bold text-gray-900 mt-2">KES {{ number_format($totalTarget, 0) }}</h2>
                <p class="text-xs text-gray-400 mt-1">Combined goal targets</p>
            </div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-50 to-transparent opacity-50"></div>
        </div>

        <!-- Overall Progress -->
        <div class="bg-white rounded-2xl shadow-sm p-6 relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Overall Progress</p>
                <div class="flex items-baseline mt-2">
                    <h2 class="text-3xl font-bold {{ $overallProgress >= 50 ? 'text-green-600' : 'text-indigo-600' }}">
                        {{ number_format($overallProgress, 1) }}%
                    </h2>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5 mt-3">
                    <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ min(100, $overallProgress) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Growth Chart (Area) -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-900">Savings Growth</h3>
                <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded">Last 6 Months</span>
            </div>
            <div id="growthChart" class="h-80"></div>
        </div>

        <!-- Distribution Chart (Donut) -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Goal Distribution</h3>
            <div id="distributionChart" class="h-80 flex justify-center"></div>
        </div>
    </div>

    <!-- Goals Grid -->
    <h3 class="text-xl font-bold text-gray-900 mb-6">Your Goals</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($savings as $saving)
            <div
                class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="text-lg font-bold text-gray-900 truncate">{{ $saving->name }}</h4>
                            <p class="text-sm text-gray-500 truncate">{{ $saving->description ?? 'No description' }}</p>
                        </div>
                        <div class="bg-indigo-50 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-500">Progress</span>
                            <span
                                class="font-bold text-gray-900">{{ $saving->target_amount > 0 ? round(($saving->balance / $saving->target_amount) * 100) : 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500"
                                style="width: {{ $saving->target_amount > 0 ? min(100, ($saving->balance / $saving->target_amount) * 100) : 0 }}%">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold">Current</p>
                            <p class="text-xl font-bold text-gray-900">KES {{ number_format($saving->balance, 0) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 uppercase font-bold">Target</p>
                            <p class="text-sm font-medium text-gray-600">KES {{ number_format($saving->target_amount, 0) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex justify-between items-center">
                    <a href="{{ route('savings.edit', $saving) }}"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Edit Goal</a>
                    <form action="{{ route('savings.destroy', $saving) }}" method="POST" class="inline-block"
                        onsubmit="return confirm('Are you sure you want to delete this goal?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No savings goals yet</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new savings goal.</p>
                <div class="mt-6">
                    <a href="{{ route('savings.create') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                clip-rule="evenodd" />
                        </svg>
                        Create Goal
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $savings->links() }}
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Growth Chart
                var growthOptions = {
                    series: [{
                        name: 'Total Savings',
                        data: @json($growthSeries)
                    }],
                    chart: {
                        type: 'area',
                        height: 320,
                        toolbar: { show: false },
                        fontFamily: 'Inter, sans-serif',
                        zoom: { enabled: false }
                    },
                    dataLabels: { enabled: false },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    xaxis: {
                        categories: @json($growthLabels),
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: { style: { colors: '#9CA3AF', fontSize: '12px' } }
                    },
                    yaxis: {
                        labels: {
                            style: { colors: '#9CA3AF', fontSize: '12px' },
                            formatter: function (value) {
                                return 'KES ' + new Intl.NumberFormat('en-US', { notation: "compact", compactDisplay: "short" }).format(value);
                            }
                        }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.1,
                            stops: [0, 90, 100]
                        }
                    },
                    colors: ['#4F46E5'],
                    grid: {
                        borderColor: '#F3F4F6',
                        strokeDashArray: 4,
                        yaxis: { lines: { show: true } }
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return "KES " + new Intl.NumberFormat().format(val);
                            }
                        }
                    }
                };
                new ApexCharts(document.querySelector("#growthChart"), growthOptions).render();

                // Distribution Chart
                var distributionOptions = {
                    series: @json($distributionSeries),
                    labels: @json($distributionLabels),
                    chart: {
                        type: 'donut',
                        height: 320,
                        fontFamily: 'Inter, sans-serif',
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    name: { show: true, fontSize: '14px', fontFamily: 'Inter, sans-serif', color: '#6B7280' },
                                    value: {
                                        show: true,
                                        fontSize: '20px',
                                        fontFamily: 'Inter, sans-serif',
                                        fontWeight: 700,
                                        color: '#111827',
                                        formatter: function (val) {
                                            return 'KES ' + new Intl.NumberFormat('en-US', { notation: "compact", compactDisplay: "short" }).format(val);
                                        }
                                    },
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        fontSize: '14px',
                                        fontFamily: 'Inter, sans-serif',
                                        color: '#6B7280',
                                        formatter: function (w) {
                                            const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                            return 'KES ' + new Intl.NumberFormat('en-US', { notation: "compact", compactDisplay: "short" }).format(total);
                                        }
                                    }
                                }
                            }
                        }
                    },
                    dataLabels: { enabled: false },
                    legend: {
                        position: 'bottom',
                        fontFamily: 'Inter, sans-serif',
                        itemMargin: { horizontal: 10, vertical: 5 }
                    },
                    colors: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: { width: 280 },
                            legend: { position: 'bottom' }
                        }
                    }]
                };
                new ApexCharts(document.querySelector("#distributionChart"), distributionOptions).render();
            });
        </script>
    @endpush
@endsection