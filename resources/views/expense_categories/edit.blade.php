@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">Edit Category</h2>
                <a href="{{ route('expense_categories.index') }}" class="text-indigo-600 hover:text-indigo-800">&larr;
                    Back</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('expense_categories.update', $expenseCategory) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" value="{{ old('name', $expenseCategory->name) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="fixed" {{ $expenseCategory->type === 'fixed' ? 'selected' : '' }}>Fixed</option>
                            <option value="variable" {{ $expenseCategory->type === 'variable' ? 'selected' : '' }}>Variable
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Default Budget Cap (KES)</label>
                        <input type="number" step="0.01" name="default_budget_cap"
                            value="{{ old('default_budget_cap', $expenseCategory->default_budget_cap) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_essential" value="1" {{ $expenseCategory->is_essential ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <label class="ml-2 text-sm text-gray-600">Essential (Non-discretionary)</label>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Update Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection