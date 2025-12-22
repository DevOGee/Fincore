@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Expenses</h1>
        <a href="{{ route('expenses.create') }}"
            class="bg-gold hover:bg-orange-600 text-white font-bold py-2 px-4 rounded shadow transition duration-150 ease-in-out">
            Add Expense
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Monthly Expenses Trend</h3>
        <div class="h-64">
            <canvas id="monthlyExpensesChart"></canvas>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Recipient
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Category
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Amount
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($expenses as $expense)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $expense->date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $expense->recipient ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $expense->category ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                            KES {{ number_format($expense->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('expenses.edit', $expense) }}"
                                class="text-teal hover:text-dark-teal mr-3">Edit</a>
                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900"
                                    onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No expense records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $expenses->links() }}
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetch('{{ route('charts.monthly-expenses') }}')
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('monthlyExpensesChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Total Expenses',
                                data: data.data,
                                backgroundColor: '#037b90',
                                borderColor: '#025f70',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function (value) {
                                            return 'KES ' + value;
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
        });
    </script>
@endsection