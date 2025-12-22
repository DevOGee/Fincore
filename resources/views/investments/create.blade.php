@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Investment') }}
        </h2>
    </div>

    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <form method="POST" action="{{ route('investments.store') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Investment Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                            class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Type -->
                    <div class="mb-4">
                        <label for="type" class="block text-sm font-medium text-gray-700">Asset Type</label>
                        <select id="type" name="type"
                            class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                            <option value="stock" {{ old('type') == 'stock' ? 'selected' : '' }}>Stock / ETF</option>
                            <option value="bond" {{ old('type') == 'bond' ? 'selected' : '' }}>Bond</option>
                            <option value="real_estate" {{ old('type') == 'real_estate' ? 'selected' : '' }}>Real Estate
                            </option>
                            <option value="business" {{ old('type') == 'business' ? 'selected' : '' }}>Business</option>
                            <option value="crypto" {{ old('type') == 'crypto' ? 'selected' : '' }}>Crypto</option>
                            <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="status" name="status"
                            class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="exited" {{ old('status') == 'exited' ? 'selected' : '' }}>Exited</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Start Date -->
                    <div class="mb-4">
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}"
                            class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                        @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Initial Investment -->
                    <div class="mb-4">
                        <label for="initial_investment" class="block text-sm font-medium text-gray-700">Initial Investment
                            Amount (KES)</label>
                        <input type="number" id="initial_investment" name="initial_investment" step="0.01"
                            value="{{ old('initial_investment') }}" required
                            class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                        @error('initial_investment') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Current Value -->
                    <div class="mb-4">
                        <label for="current_value" class="block text-sm font-medium text-gray-700">Current Value
                            (KES)</label>
                        <input type="number" id="current_value" name="current_value" step="0.01"
                            value="{{ old('current_value') }}" required
                            class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                        @error('current_value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-4">
                        <a href="{{ route('investments.index') }}"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Cancel</a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add Investment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection