@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-6">
            {{ __('Income Sources') }}
        </h2>

        <!-- Add New Source Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-medium mb-4">Add New Income Source</h3>
                <form method="POST" action="{{ route('income_sources.store') }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Source Name</label>
                        <input id="name"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            type="text" name="name" value="{{ old('name') }}" required />
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select id="type" name="type"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="Active Income">Active Income</option>
                            <option value="Passive Income">Passive Income</option>
                            <option value="Investment Income">Investment Income</option>
                            <option value="Business Income">Business Income</option>
                            <option value="Informal & Side Hustles">Informal & Side Hustles</option>
                            <option value="Other Income Sources">Other Income Sources</option>
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
                            type="text" name="linked_account" value="{{ old('linked_account') }}" />
                        @error('linked_account')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="bg-gold hover:bg-orange-600 text-white font-bold py-2 px-4 rounded shadow transition duration-150 ease-in-out">
                            {{ __('Add Source') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sources List -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-medium mb-4">Existing Sources</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Linked Account</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($incomeSources as $source)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $source->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $source->type }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $source->linked_account ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('income_sources.edit', $source) }}"
                                        class="text-teal hover:text-dark-teal mr-3">Edit</a>
                                    <form action="{{ route('income_sources.destroy', $source) }}" method="POST" class="inline">
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
                    {{ $incomeSources->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection