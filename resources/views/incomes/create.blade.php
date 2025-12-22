@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ isset($income) ? 'Edit Income' : 'Add Income' }}</h1>
            <a href="{{ route('incomes.index') }}" class="text-teal hover:text-dark-teal font-medium">
                &larr; Back to List
            </a>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
            <form action="{{ isset($income) ? route('incomes.update', $income) : route('incomes.store') }}" method="POST">
                @csrf
                @if(isset($income))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-6">
                        <label for="income_source_id" class="block text-sm font-medium text-gray-700">Income Source</label>
                        <div class="mt-1">
                            <select id="income_source_id" name="income_source_id" required
                                class="shadow-sm focus:ring-teal focus:border-teal block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                                <option value="">Select a source</option>
                                @foreach($incomeSources as $source)
                                    <option value="{{ $source->id }}" {{ (old('income_source_id') ?? ($income->income_source_id ?? '')) == $source->id ? 'selected' : '' }}>
                                        {{ $source->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if($incomeSources->isEmpty())
                                <p class="text-sm text-red-500 mt-1">No income sources found. <a
                                        href="{{ route('income_sources.index') }}" class="underline">Create one first.</a></p>
                            @endif
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">KES</span>
                            </div>
                            <input type="number" name="amount" id="amount" step="0.01" required
                                value="{{ old('amount') ?? ($income->amount ?? '') }}"
                                class="focus:ring-teal focus:border-teal block w-full pl-12 sm:text-sm border-gray-300 rounded-md p-2 border"
                                placeholder="0.00">
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                        <div class="mt-1">
                            <input type="date" name="date" id="date" required
                                value="{{ old('date') ?? (isset($income) && $income->date ? $income->date->format('Y-m-d') : date('Y-m-d')) }}"
                                class="shadow-sm focus:ring-teal focus:border-teal block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                        <div class="mt-1">
                            <select id="category" name="category"
                                class="shadow-sm focus:ring-teal focus:border-teal block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                                <option value="">Select a category</option>
                                @foreach(['Salary', 'Freelance', 'Business', 'Gift', 'Other'] as $cat)
                                    <option value="{{ $cat }}" {{ (old('category') ?? ($income->category ?? '')) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="account_credited" class="block text-sm font-medium text-gray-700">Account
                            Credited</label>
                        <div class="mt-1">
                            <input type="text" name="account_credited" id="account_credited"
                                value="{{ old('account_credited') ?? ($income->account_credited ?? '') }}"
                                class="shadow-sm focus:ring-teal focus:border-teal block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                                placeholder="e.g. Bank, M-Pesa">
                        </div>
                    </div>

                    <div class="sm:col-span-6">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <div class="mt-1">
                            <textarea id="description" name="description" rows="3"
                                class="shadow-sm focus:ring-teal focus:border-teal block w-full sm:text-sm border-gray-300 rounded-md p-2 border">{{ old('description') ?? ($income->description ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="bg-gold hover:bg-orange-600 text-white font-bold py-2 px-4 rounded shadow transition duration-150 ease-in-out">
                        {{ isset($income) ? 'Update Income' : 'Save Income' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection