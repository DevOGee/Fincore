@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $investment->name }} ({{ ucfirst($investment->type) }})
            </h2>
            <p class="text-sm text-gray-500">Status: <span class="font-medium">{{ ucfirst($investment->status) }}</span></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('investments.edit', $investment) }}" 
                class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">Edit</a>
            <form method="POST" action="{{ route('investments.destroy', $investment) }}" onsubmit="return confirm('Are you sure you want to delete this investment?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">Delete</button>
            </form>
            <a href="{{ route('investments.index') }}" 
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Back</a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-gray-500 text-sm">Current Value</div>
                <div class="text-2xl font-bold">KES {{ number_format($investment->current_value, 2) }}</div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-gray-500 text-sm">Initial Investment</div>
                <div class="text-2xl font-bold">KES {{ number_format($investment->initial_investment, 2) }}</div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-gray-500 text-sm">Total Invested</div>
                <div class="text-2xl font-bold">KES {{ number_format($investment->total_invested, 2) }}</div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-gray-500 text-sm">ROI</div>
                <div class="text-2xl font-bold {{ $investment->roi >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ number_format($investment->roi, 2) }}%
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Transactions Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Transactions</h3>

                <!-- Add Transaction Form -->
                <form method="POST" action="{{ route('investments.transactions.store', $investment) }}"
                    class="mb-6 p-4 bg-gray-50 rounded-lg">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                            <select id="type" name="type"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="buy">Buy / Contribution</option>
                                <option value="withdraw">Withdraw / Sell</option>
                                <option value="add">Add (Fee/Tax)</option>
                            </select>
                        </div>
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                            <input type="number" step="0.01" id="amount" name="amount" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="transaction_date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" id="transaction_date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add Transaction
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Transactions List -->
                <div class="overflow-y-auto max-h-64">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($investment->transactions->sortByDesc('transaction_date') as $transaction)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                        {{ $transaction->transaction_date->format('Y-m-d') }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                        {{ ucfirst($transaction->type) }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                        KES {{ number_format($transaction->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-center text-gray-500">No transactions yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Valuations Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Valuation History</h3>

                <!-- Add Valuation Form -->
                <form method="POST" action="{{ route('investments.valuations.store', $investment) }}"
                    class="mb-6 p-4 bg-gray-50 rounded-lg">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="valuation_amount" class="block text-sm font-medium text-gray-700">New Value</label>
                            <input type="number" step="0.01" id="valuation_amount" name="valuation_amount" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="valuation_date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" id="valuation_date" name="valuation_date" value="{{ date('Y-m-d') }}" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="col-span-2">
                            <button type="submit" class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Record Valuation
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Valuations List -->
                <div class="overflow-y-auto max-h-64">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($investment->valuations->sortByDesc('valuation_date') as $valuation)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                        {{ $valuation->valuation_date->format('Y-m-d') }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                        KES {{ number_format($valuation->valuation_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-4 text-center text-gray-500">No valuations recorded</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection