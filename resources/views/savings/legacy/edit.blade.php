@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Edit Legacy Savings Entry
                </h3>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                <form action="{{ route('savings.legacy.update', $savingsLegacy) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-4">
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <select id="category" name="category" required
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @foreach(['Emergency Fund', 'Retirement', 'Wealth Building', 'Education', 'Other'] as $cat)
                                    <option value="{{ $cat }}" {{ $savingsLegacy->category == $cat ? 'selected' : '' }}>{{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount (KES)</label>
                            <input type="number" name="amount" id="amount" step="0.01" value="{{ $savingsLegacy->amount }}"
                                required
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="date" id="date" value="{{ $savingsLegacy->date->format('Y-m-d') }}"
                                required
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <label for="percentage_applied" class="block text-sm font-medium text-gray-700">Percentage
                                Applied (Optional)</label>
                            <input type="number" name="percentage_applied" id="percentage_applied" step="0.01" min="0"
                                max="100" value="{{ $savingsLegacy->percentage_applied }}"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end">
                        <a href="{{ route('savings.legacy.index') }}"
                            class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow transition duration-150 ease-in-out">
                            Update Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection