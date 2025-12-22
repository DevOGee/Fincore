@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Add Savings Goal</h1>
            <a href="{{ route('savings.index') }}" class="text-teal hover:text-dark-teal font-medium">
                &larr; Back to List
            </a>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
            <form action="{{ route('savings.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-6">
                        <label for="name" class="block text-sm font-medium text-gray-700">Goal Name</label>
                        <div class="mt-1">
                            <input type="text" name="name" id="name" required
                                class="shadow-sm focus:ring-teal focus:border-teal block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                                placeholder="e.g. Emergency Fund, New Car">
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="balance" class="block text-sm font-medium text-gray-700">Current Balance</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">KES</span>
                            </div>
                            <input type="number" name="balance" id="balance" step="0.01" value="0.00"
                                class="focus:ring-teal focus:border-teal block w-full pl-12 sm:text-sm border-gray-300 rounded-md p-2 border">
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="target_amount" class="block text-sm font-medium text-gray-700">Target Amount</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">KES</span>
                            </div>
                            <input type="number" name="target_amount" id="target_amount" step="0.01"
                                class="focus:ring-teal focus:border-teal block w-full pl-12 sm:text-sm border-gray-300 rounded-md p-2 border"
                                placeholder="Optional">
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <div class="mt-1">
                        <input type="date" name="date" id="date" value="{{ date('Y-m-d') }}"
                            class="shadow-sm focus:ring-teal focus:border-teal block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                    </div>
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
                Save Goal
            </button>
        </div>
        </form>
    </div>
    </div>
@endsection