@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-6">
            {{ __('Edit Income Source') }}
        </h2>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900">
                <form method="POST" action="{{ route('income_sources.update', $incomeSource) }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Source Name</label>
                        <input id="name"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            type="text" name="name" value="{{ old('name', $incomeSource->name) }}" required />
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select id="type" name="type"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="Active Income" {{ $incomeSource->type == 'Active Income' ? 'selected' : '' }}>
                                Active Income</option>
                            <option value="Passive Income" {{ $incomeSource->type == 'Passive Income' ? 'selected' : '' }}>
                                Passive Income</option>
                            <option value="Investment Income" {{ $incomeSource->type == 'Investment Income' ? 'selected' : '' }}>Investment Income</option>
                            <option value="Business Income" {{ $incomeSource->type == 'Business Income' ? 'selected' : '' }}>
                                Business Income</option>
                            <option value="Informal & Side Hustles" {{ $incomeSource->type == 'Informal & Side Hustles' ? 'selected' : '' }}>Informal & Side Hustles</option>
                            <option value="Other Income Sources" {{ $incomeSource->type == 'Other Income Sources' ? 'selected' : '' }}>Other Income Sources</option>
                        </select>
                        @error('type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="linked_account" class="block text-sm font-medium text-gray-700">Linked Account
                            (Optional)</label>
                        <input id="linked_account"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            type="text" name="linked_account"
                            value="{{ old('linked_account', $incomeSource->linked_account) }}" />
                        @error('linked_account')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit"
                            class="bg-gold hover:bg-orange-600 text-white font-bold py-2 px-4 rounded shadow transition duration-150 ease-in-out">
                            {{ __('Update Source') }}
                        </button>
                        <a href="{{ route('income_sources.index') }}"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded shadow transition duration-150 ease-in-out">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection