@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Edit Savings Goal
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Update your savings target details.
                </p>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                <form action="{{ route('savings_goals.update', $savingsGoal) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Goal Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $savingsGoal->name) }}" required
                                class="mt-1 focus:ring-teal focus:border-teal block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <label for="target_amount" class="block text-sm font-medium text-gray-700">Target Amount
                                (KES)</label>
                            <input type="number" name="target_amount" id="target_amount" step="0.01"
                                value="{{ old('target_amount', $savingsGoal->target_amount) }}" required
                                class="mt-1 focus:ring-teal focus:border-teal block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <label for="current_amount" class="block text-sm font-medium text-gray-700">Current Saved Amount
                                (KES)</label>
                            <input type="number" name="current_amount" id="current_amount" step="0.01"
                                value="{{ old('current_amount', $savingsGoal->current_amount) }}" required
                                class="mt-1 focus:ring-teal focus:border-teal block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date
                                (Optional)</label>
                            <input type="date" name="start_date" id="start_date"
                                value="{{ old('start_date', $savingsGoal->start_date?->format('Y-m-d')) }}"
                                class="mt-1 focus:ring-teal focus:border-teal block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="deadline" class="block text-sm font-medium text-gray-700">Maturity/End Date
                                (Optional)</label>
                            <input type="date" name="deadline" id="deadline"
                                value="{{ old('deadline', $savingsGoal->deadline?->format('Y-m-d')) }}"
                                class="mt-1 focus:ring-teal focus:border-teal block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="funding_source" class="block text-sm font-medium text-gray-700">Funding
                                Source</label>
                            <select id="funding_source" name="funding_source" required
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-teal focus:border-teal sm:text-sm">
                                <option value="income_percentage" {{ $savingsGoal->funding_source == 'income_percentage' ? 'selected' : '' }}>Percentage of Income</option>
                                <option value="surplus" {{ $savingsGoal->funding_source == 'surplus' ? 'selected' : '' }}>
                                    Monthly Surplus</option>
                                <option value="legacy" {{ $savingsGoal->funding_source == 'legacy' ? 'selected' : '' }}>Legacy
                                    Savings</option>
                            </select>
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="monthly_contribution" class="block text-sm font-medium text-gray-700">Monthly
                                Contribution (KES)</label>
                            <input type="number" name="monthly_contribution" id="monthly_contribution" step="0.01"
                                value="{{ old('monthly_contribution', $savingsGoal->monthly_contribution) }}"
                                class="mt-1 focus:ring-teal focus:border-teal block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description
                                (Optional)</label>
                            <textarea id="description" name="description" rows="3"
                                class="shadow-sm focus:ring-teal focus:border-teal mt-1 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('description', $savingsGoal->description) }}</textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end">
                        <a href="{{ route('savings_goals.index') }}"
                            class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow transition duration-150 ease-in-out">
                            Update Goal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection