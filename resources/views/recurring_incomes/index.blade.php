@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-6">
            {{ __('Recurring Income Profiles') }}
        </h2>

        <!-- Add New Profile Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-medium mb-4">Add Recurring Income</h3>
                <form method="POST" action="{{ route('recurring_incomes.store') }}"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @csrf
                    <div>
                        <label for="income_source_id" class="block text-sm font-medium text-gray-700">Income Source</label>
                        <select id="income_source_id" name="income_source_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required>
                            @foreach($incomeSources as $source)
                                <option value="{{ $source->id }}">{{ $source->name }}</option>
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
                            type="number" step="0.01" name="amount" value="{{ old('amount') }}" required />
                        @error('amount')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="frequency" class="block text-sm font-medium text-gray-700">Frequency</label>
                        <select id="frequency" name="frequency"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="monthly">Monthly</option>
                            <option value="weekly">Weekly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="annually">Annually</option>
                        </select>
                        @error('frequency')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input id="start_date"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            type="date" name="start_date" value="{{ old('start_date') }}" required />
                        @error('start_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date (Optional)</label>
                        <input id="end_date"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            type="date" name="end_date" value="{{ old('end_date') }}" />
                        @error('end_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="bg-gold hover:bg-orange-600 text-white font-bold py-2 px-4 rounded shadow transition duration-150 ease-in-out">
                            {{ __('Create Profile') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Profiles List -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-medium mb-4">Active Profiles</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Source</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Frequency</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next
                                Run</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($recurringIncomes as $profile)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $profile->incomeSource->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($profile->amount, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $profile->frequency }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $profile->next_run_date ? $profile->next_run_date->format('Y-m-d') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('recurring_incomes.edit', $profile) }}"
                                        class="text-teal hover:text-dark-teal mr-3">Edit</a>
                                    <form action="{{ route('recurring_incomes.destroy', $profile) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $recurringIncomes->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection