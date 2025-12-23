@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Savings Goals</h1>
            <p class="text-gray-500 mt-1">Track your progress towards your financial dreams</p>
        </div>
        <a href="{{ route('savings_goals.create') }}"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition duration-200 ease-in-out flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Goal
        </a>
    </div>

    <!-- Summary Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <!-- Overall Progress Card -->
        <div class="bg-white rounded-2xl shadow-sm p-6 flex items-center justify-between relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total Saved</p>
                <h2 class="text-3xl font-bold text-gray-900 mt-1">KES {{ number_format($totalSaved, 0) }}</h2>
                <p class="text-sm text-gray-500 mt-2">of KES {{ number_format($totalTarget, 0) }} target</p>
            </div>
            <div class="h-24 w-24">
                <div id="overallProgressChart"></div>
            </div>
            <!-- Decorative background blob -->
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-indigo-50 rounded-full z-0"></div>
        </div>

        <!-- Active Goals Count -->
        <div class="bg-white rounded-2xl shadow-sm p-6 flex items-center relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Active Goals</p>
                <h2 class="text-3xl font-bold text-gray-900 mt-1">{{ $savingsGoals->total() }}</h2>
                <p class="text-sm text-green-600 mt-2 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Working towards your future
                </p>
            </div>
            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-indigo-100">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 2L2 7l10 5 10-5-10-5zm0 9l2.5-1.25L12 8.5l-2.5 1.25L12 11zm0 2.5l-5-2.5-5 2.5L12 22l10-8.5-5-2.5-5 2.5z" />
                </svg>
            </div>
        </div>

        <!-- Motivation Card -->
        <div
            class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl shadow-sm p-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-lg font-bold mb-2">Keep it up! 🚀</h3>
                <p class="text-indigo-100 text-sm">"Do not save what is left after spending, but spend what is left after
                    saving."</p>
                <p class="text-indigo-200 text-xs mt-2">- Warren Buffett</p>
            </div>
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
        </div>
    </div>

    <!-- Growth & Distribution Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <!-- Savings Growth Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Savings Growth</h3>
                    <p class="text-sm text-gray-500">Projected Value over time</p>
                </div>
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button onclick="updateGrowthChart('monthly')"
                        class="growth-toggle px-3 py-1 rounded-md text-sm font-medium transition-all text-indigo-600 bg-white shadow-sm"
                        id="toggle-monthly">Monthly</button>
                    <button onclick="updateGrowthChart('quarterly')"
                        class="growth-toggle px-3 py-1 rounded-md text-sm font-medium transition-all text-gray-500 hover:text-gray-900"
                        id="toggle-quarterly">Quarterly</button>
                    <button onclick="updateGrowthChart('yearly')"
                        class="growth-toggle px-3 py-1 rounded-md text-sm font-medium transition-all text-gray-500 hover:text-gray-900"
                        id="toggle-yearly">Yearly</button>
                </div>
            </div>
            <div id="savingsGrowthChart" class="w-full h-80"></div>
        </div>

        <!-- Goal Distribution Chart -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Goal Distribution</h3>
            <p class="text-sm text-gray-500 mb-6">Allocated savings by goal</p>
            <div id="goalDistributionChart" class="flex justify-center h-64"></div>
        </div>
    </div>

    <!-- Goals Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($savingsGoals as $goal)
            <div
                class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden flex flex-col">
                <div class="p-6 flex-1">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 line-clamp-1" title="{{ $goal->name }}">{{ $goal->name }}
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">
                                Deadline: {{ $goal->deadline ? $goal->deadline->format('M d, Y') : 'No deadline' }}
                            </p>
                        </div>
                        <div class="relative">
                            <!-- Feasibility Badge -->
                            @php
                                $feasibility = $goal->feasibilityScore();
                                $badgeColor = $feasibility >= 80 ? 'bg-green-100 text-green-800' : ($feasibility >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                                $badgeText = $feasibility >= 80 ? 'On Track' : ($feasibility >= 50 ? 'At Risk' : 'Off Track');
                            @endphp
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeColor }}">
                                {{ $badgeText }}
                            </span>
                        </div>
                    </div>

                    <!-- Progress Chart -->
                    <div class="flex justify-center my-4">
                        <div id="goalChart-{{ $goal->id }}" class="goal-chart"
                            data-percent="{{ $goal->completionPercentage() }}"></div>
                    </div>

                    <div class="flex justify-between items-end mb-2">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Saved</p>
                            <p class="text-xl font-bold text-indigo-600">KES {{ number_format($goal->current_amount, 0) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Target</p>
                            <p class="text-sm font-semibold text-gray-700">KES {{ number_format($goal->target_amount, 0) }}</p>
                        </div>
                    </div>

                    @if($goal->monthly_contribution > 0)
                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center text-sm">
                            <span class="text-gray-500">Monthly Plan:</span>
                            <span class="font-medium text-gray-900">KES {{ number_format($goal->monthly_contribution, 0) }}</span>
                        </div>
                    @endif
                </div>

                <!-- Actions Footer -->
                <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                    <a href="{{ route('savings_goals.show', $goal) }}"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                        View Details
                    </a>
                    <div class="flex gap-2">
                        {{-- <button
                            class="p-2 text-gray-400 hover:text-indigo-600 transition-colors rounded-full hover:bg-indigo-50"
                            title="Add Funds">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </button> --}}
                        <form action="{{ route('savings_goals.destroy', $goal) }}" method="POST" class="inline-block"
                            onsubmit="return confirm('Delete this goal?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="p-2 text-gray-400 hover:text-red-600 transition-colors rounded-full hover:bg-red-50"
                                title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 bg-white rounded-2xl shadow-sm">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No savings goals</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new savings goal.</p>
                <div class="mt-6">
                    <a href="{{ route('savings_goals.create') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                clip-rule="evenodd" />
                        </svg>
                        New Goal
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $savingsGoals->links() }}
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.addEventListener('load', function () {
                    // --- Savings Growth Chart ---
                    try {
                        var growthData = @json($growthData);

                        if (typeof ApexCharts === 'undefined') {
                            console.error('ApexCharts library not loaded');
                            var errorEl = document.querySelector("#savingsGrowthChart");
                            if (errorEl) errorEl.innerHTML = 'Error: API not loaded';
                            return;
                        }

                        var growthOptions = {
                            series: [{
                                name: 'Projected Savings',
                                data: growthData.monthly.data
                            }],
                            chart: {
                                type: 'area', // Area chart for growth
                                height: 320,
                                fontFamily: 'Inter, sans-serif',
                                toolbar: { show: false },
                                animations: { enabled: true }
                            },
                            dataLabels: { enabled: false },
                            stroke: {
                                curve: 'smooth',
                                width: 3
                            },
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    shadeIntensity: 1,
                                    opacityFrom: 0.4,
                                    opacityTo: 0.05,
                                    stops: [0, 90, 100]
                                }
                            },
                            xaxis: {
                                categories: growthData.monthly.labels,
                                axisBorder: { show: false },
                                axisTicks: { show: false },
                                labels: { style: { colors: '#64748b' } }
                            },
                            yaxis: {
                                labels: {
                                    style: { colors: '#64748b' },
                                    formatter: function (value) {
                                        if (value >= 1000000) return (value / 1000000).toFixed(1) + 'm';
                                        if (value >= 1000) return (value / 1000).toFixed(1) + 'k';
                                        return value;
                                    }
                                }
                            },
                            colors: ['#4F46E5'],
                            grid: {
                                borderColor: '#f1f5f9',
                                strokeDashArray: 4,
                                padding: { top: 0, right: 0, bottom: 0, left: 10 }
                            },
                            tooltip: {
                                theme: 'light',
                                y: { formatter: function (val) { return "KES " + val.toLocaleString(); } }
                            }
                        };

                        var savingsGrowthChart = new ApexCharts(document.querySelector("#savingsGrowthChart"), growthOptions);
                        savingsGrowthChart.render();

                        // Global function for toggle
                        window.updateGrowthChart = function (timeframe) {
                            try {
                                // Update button styles
                                document.querySelectorAll('.growth-toggle').forEach(el => {
                                    el.classList.remove('text-indigo-600', 'bg-white', 'shadow-sm');
                                    el.classList.add('text-gray-500');
                                });
                                var activeBtn = document.getElementById('toggle-' + timeframe);
                                if (activeBtn) {
                                    activeBtn.classList.remove('text-gray-500');
                                    activeBtn.classList.add('text-indigo-600', 'bg-white', 'shadow-sm');
                                }

                                // Update chart data
                                savingsGrowthChart.updateOptions({
                                    xaxis: { categories: growthData[timeframe].labels }
                                });
                                savingsGrowthChart.updateSeries([{
                                    data: growthData[timeframe].data
                                }]);
                            } catch (e) { console.error('Error updating chart:', e); }
                        };
                    } catch (e) { console.error('Growth Chart Error:', e); }


                    // --- Goal Distribution Chart ---
                    try {
                        var distData = @json($goalDistribution);
                        if (distData.series && distData.series.length > 0 && distData.series.some(val => val > 0)) {
                            var distOptions = {
                                series: distData.series,
                                labels: distData.labels,
                                chart: {
                                    type: 'donut',
                                    height: 280,
                                    fontFamily: 'Inter, sans-serif',
                                },
                                plotOptions: {
                                    pie: {
                                        donut: {
                                            size: '70%',
                                            labels: {
                                                show: true,
                                                total: {
                                                    show: true,
                                                    label: 'Total',
                                                    formatter: function (w) {
                                                        return "KES " + (w.globals.seriesTotals.reduce((a, b) => a + b, 0) / 1000).toFixed(1) + "k";
                                                    }
                                                }
                                            }
                                        }
                                    }
                                },
                                colors: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899'],
                                legend: {
                                    position: 'bottom',
                                    fontFamily: 'Inter, sans-serif',
                                },
                                dataLabels: { enabled: false },
                                tooltip: {
                                    y: { formatter: function (val) { return "KES " + val.toLocaleString(); } }
                                },
                                stroke: { show: false }
                            };
                            new ApexCharts(document.querySelector("#goalDistributionChart"), distOptions).render();
                        } else {
                            document.querySelector("#goalDistributionChart").innerHTML =
                                '<div class="flex flex-col items-center justify-center h-full text-gray-400 text-sm">' +
                                '<svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>' +
                                'No savings data yet</div>';
                        }
                    } catch (e) { console.error('Distribution Chart Error:', e); }



                var savingsGrowthChart = new ApexCharts(document.querySelector("#savingsGrowthChart"), growthOptions);
                savingsGrowthChart.render();

                // Global function for toggle
                window.updateGrowthChart = function (timeframe) {
                    // Update button styles
                    document.querySelectorAll('.growth-toggle').forEach(el => {
                        el.classList.remove('text-indigo-600', 'bg-white', 'shadow-sm');
                        el.classList.add('text-gray-500');
                    });
                    var activeBtn = document.getElementById('toggle-' + timeframe);
                    activeBtn.classList.remove('text-gray-500');
                    activeBtn.classList.add('text-indigo-600', 'bg-white', 'shadow-sm');

                    // Update chart data
                    savingsGrowthChart.updateOptions({
                        xaxis: { categories: growthData[timeframe].labels }
                    });
                    savingsGrowthChart.updateSeries([{
                        data: growthData[timeframe].data
                    }]);
                };


                // --- Goal Distribution Chart ---
                var distData = @json($goalDistribution);
                var distOptions = {
                    series: distData.series,
                    labels: distData.labels,
                    chart: {
                        type: 'donut',
                        height: 280,
                        fontFamily: 'Inter, sans-serif',
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        formatter: function (w) {
                                            return "KES " + (w.globals.seriesTotals.reduce((a, b) => a + b, 0) / 1000).toFixed(1) + "k";
                                        }
                                    }
                                }
                            }
                        }
                    },
                    colors: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899'],
                    legend: {
                        position: 'bottom',
                        fontFamily: 'Inter, sans-serif',
                    },
                    dataLabels: { enabled: false },
                    tooltip: {
                        y: { formatter: function (val) { return "KES " + val.toLocaleString(); } }
                    },
                    stroke: { show: false }
                };

                if (distData.series.length > 0 && distData.series.some(val => val > 0)) {
                    new ApexCharts(document.querySelector("#goalDistributionChart"), distOptions).render();
                } else {
                    document.querySelector("#goalDistributionChart").innerHTML =
                        '<div class="flex flex-col items-center justify-center h-full text-gray-400 text-sm">' +
                        '<svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>' +
                        'No savings data yet</div>';
                }

                // Overall Progress Chart
                var overallOptions = {
                    series: [{{ round($overallProgress) }}],
                    chart: {
                        height: 140,
                        type: 'radialBar',
                        fontFamily: 'Inter, sans-serif',
                    },
                    plotOptions: {
                        radialBar: {
                            hollow: { size: '60%' },
                            dataLabels: {
                                show: true,
                                name: { show: false },
                                value: {
                                    offsetY: 8,
                                    fontSize: '18px',
                                    fontWeight: 'bold',
                                    color: '#4F46E5',
                                    formatter: function (val) { return val + '%'; }
                                }
                            },
                            track: { background: '#EEF2FF' }
                        },
                    },
                    colors: ['#4F46E5'],
                    stroke: { lineCap: 'round' },
                };
                new ApexCharts(document.querySelector("#overallProgressChart"), overallOptions).render();

                // Individual Goal Charts
                document.querySelectorAll('.goal-chart').forEach(function (chartEl) {
                    var percent = chartEl.getAttribute('data-percent');
                    var options = {
                        series: [percent],
                        chart: {
                            height: 180,
                            type: 'radialBar',
                            fontFamily: 'Inter, sans-serif',
                            sparkline: { enabled: true }
                        },
                        plotOptions: {
                            radialBar: {
                                startAngle: -90,
                                endAngle: 90,
                                track: {
                                    background: "#e7e7e7",
                                    strokeWidth: '97%',
                                    margin: 5, // margin is in pixels
                                },
                                dataLabels: {
                                    name: { show: false },
                                    value: {
                                        offsetY: -2,
                                        fontSize: '22px',
                                        fontWeight: 'bold',
                                        color: '#111827'
                                    }
                                }
                            }
                        },
                        grid: { padding: { top: -10 } },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shade: 'light',
                                shadeIntensity: 0.4,
                                inverseColors: false,
                                opacityFrom: 1,
                                opacityTo: 1,
                                stops: [0, 50, 53, 91]
                            },
                        },
                        colors: [percent >= 100 ? '#10B981' : '#4F46E5'],
                        labels: ['Progress'],
                    };
                    new ApexCharts(chartEl, options).render();
                });
            });
        </script>
    @endpush
@endsection