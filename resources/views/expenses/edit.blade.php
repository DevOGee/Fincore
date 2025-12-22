@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Edit Expense
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Update the details of your expense.
                </p>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                <form action="{{ route('expenses.update', $expense) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-4">
                            <label for="recipient" class="block text-sm font-medium text-gray-700">Recipient / Merchant</label>
                            <input type="text" name="recipient" id="recipient"
                                value="{{ old('recipient', $expense->recipient) }}"
                                placeholder="e.g. Supermarket, Landlord"
                                class="mt-1 focus:ring-teal focus:border-teal block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">KES</span>
                                </div>
                                <input type="number" name="amount" id="amount" step="0.01" required
                                    value="{{ old('amount', $expense->amount) }}"
                                    class="focus:ring-teal focus:border-teal block w-full pl-12 sm:text-sm border-gray-300 rounded-md"
                                    placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="date" id="date" required
                                value="{{ old('date', $expense->date->format('Y-m-d')) }}"
                                class="mt-1 focus:ring-teal focus:border-teal block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <label for="expense_category_id"
                                class="block text-sm font-medium text-gray-700">Category</label>
                            <select name="expense_category_id" id="expense_category_id"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-teal focus:border-teal sm:text-sm">
                                <option value="">Select a category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $expense->expense_category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }} ({{ ucfirst($cat->type) }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500"><a href="{{ route('expense_categories.index') }}"
                                    class="text-indigo-600 hover:underline">Manage Categories</a></p>
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                            <select id="payment_method" name="payment_method"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-teal focus:border-teal sm:text-sm">
                                <option value="Cash" {{ $expense->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Bank Transfer" {{ $expense->payment_method == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="Mobile Money" {{ $expense->payment_method == 'Mobile Money' ? 'selected' : '' }}>Mobile Money</option>
                                <option value="Card" {{ $expense->payment_method == 'Card' ? 'selected' : '' }}>Card</option>
                                <option value="Other" {{ $expense->payment_method == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="col-span-6 sm:col-span-3 flex items-center h-full pt-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="is_recurring" name="is_recurring" type="checkbox" value="1" {{ $expense->is_recurring ? 'checked' : '' }}
                                        class="focus:ring-teal h-4 w-4 text-teal border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_recurring" class="font-medium text-gray-700">Mark as Recurring</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-6">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" name="description" rows="3"
                                class="shadow-sm focus:ring-teal focus:border-teal mt-1 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('description', $expense->description) }}</textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end">
                        <a href="{{ route('expenses.index') }}"
                            class="text-sm text-gray-600 hover:text-gray-900 mr-4">Back to List</a>
                        <button type="submit"
                            class="bg-gold hover:bg-orange-600 text-white font-bold py-2 px-4 rounded shadow transition duration-150 ease-in-out">
                            Update Expense
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
