@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Investments') }}
        </h2>
        <a href="{{ route('investments.create') }}"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Add Investment
        </a>
    </div>

    <div class="">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- KPIs -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Total Portfolio Value</div>
                    <div class="text-2xl font-bold">{{ number_format($investments->sum('current_value'), 2) }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Total Invested</div>
                    <div class="text-2xl font-bold">{{ number_format($investments->sum('total_invested'), 2) }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Total Gain/Loss</div>
                    <div
                        class="text-2xl font-bold {{ $investments->sum('total_gain_loss') >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($investments->sum('total_gain_loss'), 2) }}
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Portfolio ROI</div>
                    @php
                        $totalInvested = $investments->sum('total_invested');
                        $portfolioRoi = $totalInvested > 0 ? ($investments->sum('total_gain_loss') / $totalInvested) * 100 : 0;
                    @endphp
                    <div class="text-2xl font-bold {{ $portfolioRoi >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($portfolioRoi, 2) }}%
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Portfolio Growth Chart -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Portfolio Growth (Last 6 Months)</h3>
                    <div id="growthChart" class="h-80"></div>
                </div>

                <!-- Asset Allocation Chart -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Asset Allocation</h3>
                    <div id="allocationChart" class="h-80"></div>
                </div>
            </div>

            <!-- Investments List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Invested</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Current Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ROI</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($investments as $investment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $investment->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $investment->status }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ ucfirst($investment->type) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($investment->total_invested, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                        {{ number_format($investment->current_value, 2) }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm {{ $investment->roi >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($investment->roi, 2) }}%
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2 relative z-10">
                                            <a href="{{ route('investments.show', $investment) }}"
                                                class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200"
                                                title="View">
                                                <svg class="w-5 h-5 pointer-events-none" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('investments.edit', $investment) }}"
                                                class="text-yellow-600 hover:text-yellow-900 transition-colors duration-200"
                                                title="Edit">
                                                <svg class="w-5 h-5 pointer-events-none" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('investments.destroy', $investment) }}"
                                                class="inline-block"
                                                onsubmit="return confirm('Are you sure you want to delete this investment?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 cursor-pointer transition-colors duration-200"
                                                    title="Delete">
                                                    <svg class="w-5 h-5 pointer-events-none" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $investments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                console.log('DOM fully loaded and parsed');

                if (typeof ApexCharts === 'undefined') {
                    console.error('ApexCharts library not loaded!');
                    return;
                }

                try {
                    // Portfolio Growth Chart
                    const growthData = @json($growthData);
                    console.log('Growth Data:', growthData);

                    if (!document.querySelector("#growthChart")) {
                        console.error('Growth Chart container not found!');
                    } else {
                        const growthOptions = {
                            chart: {
                                type: 'area',
                                height: 350,
                                fontFamily: 'Inter, sans-serif',
                                toolbar: { show: false },
                                animations: { enabled: true }
                            },
                            dataLabels: { enabled: false },
                            stroke: { curve: 'monotoneCubic', width: 3 },
                            markers: {
                                size: 5,
                                colors: ['#fff'],
                                strokeColors: '#4F46E5',
                                strokeWidth: 2,
                                hover: { size: 7 }
                            },
                            xaxis: {
                                categories: growthData.map(item => item.month),
                                labels: { style: { colors: '#6B7280', fontSize: '12px' } },
                                axisBorder: { show: false },
                                axisTicks: { show: false }
                            },
                            yaxis: {
                                labels: {
                                    style: { colors: '#6B7280', fontSize: '12px' },
                                    formatter: function (value) { return 'KES ' + value.toLocaleString(); }
                                }
                            },
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    shadeIntensity: 1,
                                    opacityFrom: 0.7,
                                    opacityTo: 0.1,
                                    stops: [0, 100]
                                }
                            },
                            colors: ['#4F46E5'],
                            series: [{
                                name: 'Portfolio Value',
                                data: growthData.map(item => item.value)
                            }],
                            tooltip: {
                                theme: 'light',
                                y: { formatter: function (val) { return 'KES ' + val.toLocaleString(); } },
                                marker: { show: false }
                            },
                            grid: {
                                borderColor: '#F3F4F6',
                                strokeDashArray: 4,
                                yaxis: { lines: { show: true } },
                                padding: { top: 0, right: 0, bottom: 0, left: 10 }
                            }
                        };
                        var growthChart = new ApexCharts(document.querySelector("#growthChart"), growthOptions);
                        growthChart.render();
                        console.log('Growth Chart rendered');
                    }

                    // Asset Allocation Chart
                    const allocationData = @json($allocationData);
                    console.log('Allocation Data:', allocationData);

                    if (!document.querySelector("#allocationChart")) {
                        console.error('Allocation Chart container not found!');
                    } else {
                        const allocationOptions = {
                            chart: {
                                type: 'donut',
                                height: 350,
                                fontFamily: 'Inter, sans-serif',
                            },
                            labels: allocationData.map(item => item.name),
                            series: allocationData.map(item => item.value),
                            colors: ['#4F46E5', '#10B981', '#F59E0B', '#EC4899', '#8B5CF6', '#3B82F6'],
                            legend: {
                                position: 'right',
                                offsetY: 50,
                                markers: { radius: 12 },
                                itemMargin: { horizontal: 10, vertical: 8 }
                            },
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '70%',
                                        labels: {
                                            show: true,
                                            name: { show: true, fontSize: '14px', fontFamily: 'Inter, sans-serif', color: '#6B7280', offsetY: -10 },
                                            value: {
                                                show: true,
                                                fontSize: '24px',
                                                fontFamily: 'Inter, sans-serif',
                                                color: '#111827',
                                                fontWeight: 700,
                                                offsetY: 5,
                                                formatter: function (val) { return 'KES ' + parseInt(val).toLocaleString(); }
                                            },
                                            total: {
                                                show: true,
                                                label: 'Total Portfolio',
                                                color: '#6B7280',
                                                fontSize: '14px',
                                                formatter: function (w) {
                                                    return 'KES ' + w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString();
                                                }
                                            }
                                        }
                                    }
                                }
                            },
                            tooltip: {
                                y: {
                                    formatter: function (val) {
                                        return 'KES ' + val.toLocaleString();
                                    }
                                }
                            },
                            dataLabels: { enabled: false },
                            responsive: [{
                                breakpoint: 480,
                                options: {
                                    chart: { height: 300 },
                                    legend: { position: 'bottom' }
                                }
                            }]
                        };
                        var allocationChart = new ApexCharts(document.querySelector("#allocationChart"), allocationOptions);
                        allocationChart.render();
                        console.log('Allocation Chart rendered');
                    }
                } catch (e) {
                    console.error('Error rendering charts:', e);
                }
            });
        </script>
    @endpush
@endsection