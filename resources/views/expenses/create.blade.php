@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Add Expense</h1>
            <a href="{{ route('expenses.index') }}" class="text-teal hover:text-dark-teal font-medium">
                &larr; Back to List
            </a>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-6">
                        <label for="recipient" class="block text-sm font-medium text-gray-700">Recipient / Merchant</label>
                        <div class="mt-1">
                            <input type="text" name="recipient" id="recipient"
                                class="shadow-sm focus:ring-teal focus:border-teal block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                                placeholder="e.g. Supermarket, Landlord">
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">KES</span>
                            </div>
                            <input type="number" name="amount" id="amount" step="0.01" required
                                class="focus:ring-teal focus:border-teal block w-full pl-12 sm:text-sm border-gray-300 rounded-md p-2 border"
                                placeholder="0.00">
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                        <div class="mt-1">
                            <input type="date" name="date" id="date" required value="{{ date('Y-m-d') }}"
                                class="shadow-sm focus:ring-teal focus:border-teal block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                        </div>
                    </div>

                    <div class="sm:col-span-6">
                        <label for="expense_category_id" class="block text-sm font-medium text-gray-700">Category</label>
                        <div class="mt-1">
                            <select id="expense_category_id" name="expense_category_id" required
                                class="shadow-sm focus:ring-teal focus:border-teal block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                                <option value="">Select a category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }} ({{ ucfirst($cat->type) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <p class="mt-1 text-xs text-gray-500"><a href="{{ route('expense_categories.index') }}"
                                class="text-indigo-600 hover:underline">Manage Categories</a></p>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                        <div class="mt-1">
                            <select id="payment_method" name="payment_method"
                                class="shadow-sm focus:ring-teal focus:border-teal block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                                <option value="">Select</option>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="mobile">Mobile Money</option>
                                <option value="bank">Bank Transfer</option>
                            </select>
                        </div>
                    </div>

                    <div class="sm:col-span-3 flex items-end">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_recurring" value="1"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-600">Mark as Recurring</span>
                        </label>
                    </div>

                    <div class="sm:col-span-6">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <div class="mt-1">
                            <textarea id="description" name="description" rows="3"
                                class="shadow-sm focus:ring-teal focus:border-teal block w-full sm:text-sm border-gray-300 rounded-md p-2 border"></textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="bg-gold hover:bg-orange-600 text-white font-bold py-2 px-4 rounded shadow transition duration-150 ease-in-out">
                        Save Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection