@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Savings Legacy</h1>
            <p class="text-gray-500 mt-1">Long-term wealth accumulation and preservation</p>
        </div>
        <a href="{{ route('savings.legacy.settings') }}"
            class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-2 px-4 rounded-lg shadow-sm transition duration-200 ease-in-out flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Allocation Settings
        </a>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Balance -->
        <div class="bg-white rounded-2xl shadow-sm p-6 relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Legacy Balance</p>
                <h2 class="text-3xl font-bold text-gray-900 mt-2">KES {{ number_format($totalLegacy, 0) }}</h2>
                <p class="text-xs text-gray-400 mt-1">Accumulated Wealth</p>
            </div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-indigo-50 to-transparent opacity-50">
            </div>
        </div>

        <!-- Monthly Contribution -->
        <div class="bg-white rounded-2xl shadow-sm p-6 relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">This Month</p>
                <h2 class="text-3xl font-bold text-green-600 mt-2">+{{ number_format($thisMonthContribution, 0) }}</h2>
                <p class="text-xs text-gray-400 mt-1">New Contributions</p>
            </div>
            <div class="absolute right-4 top-4 text-green-100">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </div>
        </div>

        <!-- Quarterly Growth -->
        <div class="bg-white rounded-2xl shadow-sm p-6 relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Quarterly Growth</p>
                <div class="flex items-baseline mt-2">
                    <h2 class="text-3xl font-bold {{ $quarterlyGrowth >= 0 ? 'text-indigo-600' : 'text-red-600' }}">
                        {{ number_format($quarterlyGrowth, 1) }}%
                    </h2>
                    <span class="ml-2 text-xs text-gray-500">vs last Q</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Portfolio Performance</p>
            </div>
            <div class="absolute right-4 top-4 text-indigo-50">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
        </div>

        <!-- Status -->
        <div
            class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl shadow-sm p-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-indigo-200 text-xs font-bold uppercase tracking-wider">Status</p>
                <h2 class="text-xl font-bold mt-2">Active & Protected</h2>
                <p class="text-xs text-indigo-100 mt-1">Your legacy is secure.</p>
            </div>
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Growth Chart (Area) -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-900">Growth Over Time</h3>
                <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded">Last 12 Months</span>
            </div>
            <div id="growthChart" class="h-80"></div>
        </div>

        <!-- Allocation Chart (Donut) -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Allocation Breakdown</h3>
            <div id="allocationChart" class="h-80 flex justify-center"></div>
        </div>
    </div>

    <!-- Recent Entries Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mt-8">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Entries</h3>
            <a href="{{ route('savings.legacy.create') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                + Add New Entry
            </a>
        </div>
        <div class="border-t border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($entries as $entry)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $entry->date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $entry->category }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">KES {{ number_format($entry->amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('savings.legacy.edit', $entry) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <form action="{{ route('savings.legacy.destroy', $entry) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this entry?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No entries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $entries->links() }}
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Growth Chart
                var growthOptions = {
                    series: [{
                        name: 'Total Legacy',
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

                // Allocation Chart
                var allocationOptions = {
                    series: @json($allocationSeries),
                    labels: @json($allocationLabels),
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
                new ApexCharts(document.querySelector("#allocationChart"), allocationOptions).render();
            });
        </script>
    @endpush
@endsection