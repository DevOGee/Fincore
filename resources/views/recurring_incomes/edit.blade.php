@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-6">
            {{ __('Edit Recurring Income Profile') }}
        </h2>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900">
                <form method="POST" action="{{ route('recurring_incomes.update', $recurringIncome) }}"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="income_source_id" class="block text-sm font-medium text-gray-700">Income Source</label>
                        <select id="income_source_id" name="income_source_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required>
                            @foreach($incomeSources as $source)
                                <option value="{{ $source->id }}" {{ $recurringIncome->income_source_id == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                            @endforeach
                        </select>
                        @error('income_source_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                        <input id="amount"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            type="number" step="0.01" name="amount" value="{{ old('amount', $recurringIncome->amount) }}"
                            required />
                        @error('amount')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="frequency" class="block text-sm font-medium text-gray-700">Frequency</label>
                        <select id="frequency" name="frequency"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="monthly" {{ $recurringIncome->frequency == 'monthly' ? 'selected' : '' }}>Monthly
                            </option>
                            <option value="weekly" {{ $recurringIncome->frequency == 'weekly' ? 'selected' : '' }}>Weekly
                            </option>
                            <option value="quarterly" {{ $recurringIncome->frequency == 'quarterly' ? 'selected' : '' }}>
                                Quarterly</option>
                            <option value="annually" {{ $recurringIncome->frequency == 'annually' ? 'selected' : '' }}>
                                Annually</option>
                        </select>
                        @error('frequency')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input id="start_date"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            type="date" name="start_date"
                            value="{{ old('start_date', $recurringIncome->start_date->format('Y-m-d')) }}" required />
                        @error('start_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date (Optional)</label>
                        <input id="end_date"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            type="date" name="end_date"
                            value="{{ old('end_date', $recurringIncome->end_date ? $recurringIncome->end_date->format('Y-m-d') : '') }}" />
                        @error('end_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit"
                            class="bg-gold hover:bg-orange-600 text-white font-bold py-2 px-4 rounded shadow transition duration-150 ease-in-out">
                            {{ __('Update Profile') }}
                        </button>
                        <a href="{{ route('recurring_incomes.index') }}"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded shadow transition duration-150 ease-in-out">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection