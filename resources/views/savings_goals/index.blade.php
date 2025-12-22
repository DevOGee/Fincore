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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Working towards your future
                </p>
            </div>
            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-indigo-100">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zm0 9l2.5-1.25L12 8.5l-2.5 1.25L12 11zm0 2.5l-5-2.5-5 2.5L12 22l10-8.5-5-2.5-5 2.5z" />
                </svg>
            </div>
        </div>

        <!-- Motivation Card -->
        <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl shadow-sm p-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-lg font-bold mb-2">Keep it up! 🚀</h3>
                <p class="text-indigo-100 text-sm">"Do not save what is left after spending, but spend what is left after saving."</p>
                <p class="text-indigo-200 text-xs mt-2">- Warren Buffett</p>
            </div>
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
        </div>
    </div>

    <!-- Goals Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($savingsGoals as $goal)
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden flex flex-col">
                <div class="p-6 flex-1">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 line-clamp-1" title="{{ $goal->name }}">{{ $goal->name }}</h3>
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
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeColor }}">
                                {{ $badgeText }}
                            </span>
                        </div>
                    </div>

                    <!-- Progress Chart -->
                    <div class="flex justify-center my-4">
                        <div id="goalChart-{{ $goal->id }}" class="goal-chart" data-percent="{{ $goal->completionPercentage() }}"></div>
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
                    <a href="{{ route('savings_goals.show', $goal) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                        View Details
                    </a>
                    <div class="flex gap-2">
                        {{-- <button class="p-2 text-gray-400 hover:text-indigo-600 transition-colors rounded-full hover:bg-indigo-50" title="Add Funds">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </button> --}}
                        <form action="{{ route('savings_goals.destroy', $goal) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this goal?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors rounded-full hover:bg-red-50" title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 bg-white rounded-2xl shadow-sm">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No savings goals</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new savings goal.</p>
                <div class="mt-6">
                    <a href="{{ route('savings_goals.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
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
                document.querySelectorAll('.goal-chart').forEach(function(chartEl) {
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