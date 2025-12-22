@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Investment Policy Statement') }}
        </h2>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- IPS Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Your IPS Settings</h3>
                <form method="POST" action="{{ route('ips.store') }}">
                    @csrf

                    <!-- Risk Profile -->
                    <div class="mb-4">
                        <label for="risk_profile" class="block text-sm font-medium text-gray-700">Risk Profile</label>
                        <select id="risk_profile" name="risk_profile"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="conservative" {{ ($ips->risk_profile ?? '') == 'conservative' ? 'selected' : '' }}>
                                Conservative</option>
                            <option value="moderate" {{ ($ips->risk_profile ?? 'moderate') == 'moderate' ? 'selected' : '' }}>
                                Moderate</option>
                            <option value="aggressive" {{ ($ips->risk_profile ?? '') == 'aggressive' ? 'selected' : '' }}>
                                Aggressive</option>
                        </select>
                    </div>

                    <!-- Allocation Targets -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Allocation Targets (%)</label>
                        @php $targets = $ips->allocation_targets ?? ['stock' => 50, 'bond' => 20, 'real_estate' => 20, 'crypto' => 5, 'other' => 5]; @endphp
                        <div class="grid grid-cols-2 gap-2">
                            @foreach(['stock' => 'Stocks', 'bond' => 'Bonds', 'real_estate' => 'Real Estate', 'crypto' => 'Crypto', 'other' => 'Other'] as $key => $label)
                                <div>
                                    <label class="text-xs text-gray-500">{{ $label }}</label>
                                    <input type="number" name="allocation_targets[{{ $key }}]" value="{{ $targets[$key] ?? 0 }}"
                                        class="block w-full border-gray-300 rounded-md shadow-sm text-sm" min="0" max="100">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Max Single Asset -->
                    <div class="mb-4">
                        <label for="max_single_asset_pct" class="block text-sm font-medium text-gray-700">Max Single Asset
                            (%)</label>
                        <input type="number" id="max_single_asset_pct" name="max_single_asset_pct"
                            value="{{ $ips->max_single_asset_pct ?? 25 }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" min="5" max="100" step="0.01">
                        <p class="text-xs text-gray-500 mt-1">Alert if any single investment exceeds this percentage</p>
                    </div>

                    <!-- Rebalance Frequency -->
                    <div class="mb-4">
                        <label for="rebalance_frequency" class="block text-sm font-medium text-gray-700">Rebalance
                            Frequency</label>
                        <select id="rebalance_frequency" name="rebalance_frequency"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="monthly" {{ ($ips->rebalance_frequency ?? '') == 'monthly' ? 'selected' : '' }}>
                                Monthly</option>
                            <option value="quarterly" {{ ($ips->rebalance_frequency ?? 'quarterly') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                            <option value="annually" {{ ($ips->rebalance_frequency ?? '') == 'annually' ? 'selected' : '' }}>
                                Annually</option>
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Save IPS
                    </button>
                </form>
            </div>

            <!-- Compliance Status -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Compliance Status</h3>
                @if($compliance)
                    @if($compliance['compliant'])
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            <strong>✓ Compliant!</strong> Your portfolio aligns with your IPS.
                        </div>
                    @else
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <strong>⚠ Non-Compliant</strong>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach($compliance['alerts'] as $alert)
                                    <li>{{ $alert }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(!empty($compliance['current_allocation']))
                        <h4 class="font-medium text-gray-700 mt-4 mb-2">Current Allocation</h4>
                        <div class="space-y-2">
                            @foreach($compliance['current_allocation'] as $type => $pct)
                                <div class="flex justify-between text-sm">
                                    <span>{{ ucfirst($type) }}</span>
                                    <span class="font-medium">{{ number_format($pct, 1) }}%</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <p class="text-gray-500">No IPS configured or no investments to analyze.</p>
                @endif
            </div>
        </div>
    </div>
@endsection