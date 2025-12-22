@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Savings Allocation Settings</h1>
        <a href="{{ route('savings.legacy.index') }}" class="text-blue-600 hover:text-blue-800">
            &larr; Back to Dashboard
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl mx-auto">
        <p class="text-gray-600 mb-6">
            Define the percentage of every income that should be automatically allocated to your legacy savings.
            These funds are deducted immediately when income is recorded.
        </p>

        <form action="{{ route('savings.legacy.update-settings') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <!-- Emergency Fund -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <h3 class="font-semibold text-gray-800">Emergency Fund</h3>
                        <p class="text-sm text-gray-500">For unexpected expenses and safety net.</p>
                        <input type="hidden" name="rules[0][category]" value="emergency">
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <input type="number" name="rules[0][percentage]" 
                                value="{{ $rules->where('category', 'emergency')->first()->percentage ?? 10 }}"
                                class="w-20 rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                min="0" max="100" step="0.5">
                            <span class="ml-2 text-gray-600">%</span>
                        </div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="rules[0][is_active]" value="1"
                                {{ ($rules->where('category', 'emergency')->first()->is_active ?? true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Active</span>
                        </label>
                    </div>
                </div>

                <!-- Wealth Building -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <h3 class="font-semibold text-gray-800">Wealth Building</h3>
                        <p class="text-sm text-gray-500">Long-term investments and asset accumulation.</p>
                        <input type="hidden" name="rules[1][category]" value="wealth">
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <input type="number" name="rules[1][percentage]" 
                                value="{{ $rules->where('category', 'wealth')->first()->percentage ?? 8 }}"
                                class="w-20 rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                min="0" max="100" step="0.5">
                            <span class="ml-2 text-gray-600">%</span>
                        </div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="rules[1][is_active]" value="1"
                                {{ ($rules->where('category', 'wealth')->first()->is_active ?? true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Active</span>
                        </label>
                    </div>
                </div>

                <!-- Legacy / Family -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <h3 class="font-semibold text-gray-800">Legacy / Family</h3>
                        <p class="text-sm text-gray-500">Generational wealth and family projects.</p>
                        <input type="hidden" name="rules[2][category]" value="legacy">
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <input type="number" name="rules[2][percentage]" 
                                value="{{ $rules->where('category', 'legacy')->first()->percentage ?? 5 }}"
                                class="w-20 rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                min="0" max="100" step="0.5">
                            <span class="ml-2 text-gray-600">%</span>
                        </div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="rules[2][is_active]" value="1"
                                {{ ($rules->where('category', 'legacy')->first()->is_active ?? true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Active</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow">
                    Save Allocation Rules
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
