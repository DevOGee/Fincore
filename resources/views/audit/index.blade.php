@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Audit Logs') }}
        </h2>
        <p class="text-sm text-gray-500 mt-1">Track all system activities in real-time</p>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="text-sm text-gray-500">Today's Actions</div>
                <div class="text-2xl font-bold text-gray-900">
                    {{ $logs->where('created_at', '>=', now()->startOfDay())->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="text-sm text-gray-500">Total Logs</div>
                <div class="text-2xl font-bold text-gray-900">{{ $logs->total() }}</div>
            </div>
            <div class="bg-green-50 rounded-lg shadow-sm p-4">
                <div class="text-sm text-green-600">Creates</div>
                <div class="text-2xl font-bold text-green-700">{{ $logs->where('action_type', 'create')->count() }}</div>
            </div>
            <div class="bg-blue-50 rounded-lg shadow-sm p-4">
                <div class="text-sm text-blue-600">Logins</div>
                <div class="text-2xl font-bold text-blue-700">{{ $logs->where('action_type', 'login')->count() }}</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 mb-6">
            <form method="GET" action="{{ route('audit.index') }}" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Action Type</label>
                    <select name="action_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">All</option>
                        <option value="create" {{ request('action_type') == 'create' ? 'selected' : '' }}>Create</option>
                        <option value="update" {{ request('action_type') == 'update' ? 'selected' : '' }}>Update</option>
                        <option value="delete" {{ request('action_type') == 'delete' ? 'selected' : '' }}>Delete</option>
                        <option value="login" {{ request('action_type') == 'login' ? 'selected' : '' }}>Login</option>
                        <option value="logout" {{ request('action_type') == 'logout' ? 'selected' : '' }}>Logout</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Entity Type</label>
                    <select name="entity_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">All</option>
                        <option value="User" {{ request('entity_type') == 'User' ? 'selected' : '' }}>User</option>
                        <option value="Income" {{ request('entity_type') == 'Income' ? 'selected' : '' }}>Income</option>
                        <option value="Expense" {{ request('entity_type') == 'Expense' ? 'selected' : '' }}>Expense</option>
                        <option value="Investment" {{ request('entity_type') == 'Investment' ? 'selected' : '' }}>Investment
                        </option>
                        <option value="Budget" {{ request('entity_type') == 'Budget' ? 'selected' : '' }}>Budget</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" name="date" value="{{ request('date') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Filter</button>
                <a href="{{ route('audit.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Reset</a>
            </form>
        </div>

        <!-- Logs Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entity</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Details</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $log->created_at->format('l') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $log->created_at->format('d M Y') }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $log->created_at->format('H:i:s') }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $log->action_type == 'create' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $log->action_type == 'update' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $log->action_type == 'delete' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $log->action_type == 'login' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $log->action_type == 'logout' ? 'bg-purple-100 text-purple-800' : '' }}">
                                        {{ ucfirst($log->action_type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $log->entity_type }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ $log->entity_id ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                        </svg>
                                        <span class="text-sm text-gray-600 font-mono">{{ $log->ip_address ?? 'Unknown' }}</span>
                                    </div>
                                    <div class="text-xs text-gray-400 truncate max-w-[150px]" title="{{ $log->user_agent }}">
                                        {{ Str::limit($log->user_agent, 30) ?? 'Unknown' }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500">
                                    @if($log->action_type == 'create')
                                        <span class="text-green-600">Created new {{ strtolower($log->entity_type) }}</span>
                                    @elseif($log->action_type == 'update')
                                        <span class="text-yellow-600">Updated {{ strtolower($log->entity_type) }}</span>
                                    @elseif($log->action_type == 'delete')
                                        <span class="text-red-600">Deleted {{ strtolower($log->entity_type) }}</span>
                                    @elseif($log->action_type == 'login')
                                        <span class="text-blue-600">User logged in</span>
                                    @elseif($log->action_type == 'logout')
                                        <span class="text-purple-600">User logged out</span>
                                    @else
                                        {{ $log->action_type }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="mt-2 text-gray-500">No audit logs found</p>
                                    <p class="text-sm text-gray-400">Actions will appear here as you use the system</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection