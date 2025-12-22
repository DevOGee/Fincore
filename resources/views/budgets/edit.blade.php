@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Edit Budget
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Update the spending limit for this category.
                </p>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                <form action="{{ route('budgets.update', $budget) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-4">
                            <label for="expense_category_id"
                                class="block text-sm font-medium text-gray-700">Category</label>
                            <select name="expense_category_id" id="expense_category_id"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-teal focus:border-teal sm:text-sm">
                                <option value="">Global Budget (All Categories)</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $budget->expense_category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }} ({{ ucfirst($cat->type) }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500"><a href="{{ route('expense_categories.index') }}"
                                    class="text-indigo-600 hover:underline">Manage Categories</a></p>
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <label for="limit" class="block text-sm font-medium text-gray-700">Limit Amount (KES)</label>
                            <input type="number" name="limit" id="limit" step="0.01" required
                                value="{{ old('limit', $budget->limit) }}"
                                class="mt-1 focus:ring-teal focus:border-teal block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="period" class="block text-sm font-medium text-gray-700">Period</label>
                            <select id="period" name="period"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-teal focus:border-teal sm:text-sm">
                                <option value="monthly" {{ $budget->period == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="yearly" {{ $budget->period == 'yearly' ? 'selected' : '' }}>Yearly</option>
                            </select>
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="flexibility" class="block text-sm font-medium text-gray-700">Flexibility</label>
                            <select id="flexibility" name="flexibility" required
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-teal focus:border-teal sm:text-sm">
                                <option value="soft" {{ $budget->flexibility == 'soft' ? 'selected' : '' }}>Soft (Allow over-budget, record as debt)</option>
                                <option value="strict" {{ $budget->flexibility == 'strict' ? 'selected' : '' }}>Strict (Block over-budget transactions)</option>
                            </select>
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" name="start_date" id="start_date"
                                value="{{ old('start_date', $budget->start_date ? $budget->start_date->format('Y-m-d') : '') }}"
                                class="mt-1 focus:ring-teal focus:border-teal block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" name="end_date" id="end_date"
                                value="{{ old('end_date', $budget->end_date ? $budget->end_date->format('Y-m-d') : '') }}"
                                class="mt-1 focus:ring-teal focus:border-teal block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="status" name="status" {{ !auth()->user()->can('updateStatus', $budget) ? 'disabled' : '' }}
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-teal focus:border-teal sm:text-sm">
                                @foreach(\App\Models\Budget::$statuses as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', $budget->status) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @if(!auth()->user()->can('updateStatus', $budget))
                                <input type="hidden" name="status" value="{{ $budget->status }}">
                                <p class="mt-1 text-xs text-gray-500">You don't have permission to change the status.</p>
                            @endif
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end">
                        <a href="{{ route('budgets.index') }}"
                            class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <button type="submit"
                            class="bg-gold hover:bg-orange-600 text-white font-bold py-2 px-4 rounded shadow transition duration-150 ease-in-out">
                            Update Budget
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
