@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Edit Savings Goal
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Update your savings target and details.
                </p>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                <form action="{{ route('savings.update', $saving) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Goal Name</label>
                            <input type="text" name="name" id="name" value="{{ $saving->name }}" required
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <label for="balance" class="block text-sm font-medium text-gray-700">Current Balance
                                (KES)</label>
                            <input type="number" name="balance" id="balance" step="0.01" value="{{ $saving->balance }}"
                                required
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <label for="target_amount" class="block text-sm font-medium text-gray-700">Target Amount
                                (KES)</label>
                            <input type="number" name="target_amount" id="target_amount" step="0.01"
                                value="{{ $saving->target_amount }}"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" name="description" rows="3"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md">{{ $saving->description }}</textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end">
                        <a href="{{ route('savings.index') }}"
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