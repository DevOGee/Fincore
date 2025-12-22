<div class="hidden md:flex flex-col w-64 bg-white border-r border-gray-200 min-h-screen transition-all duration-300">
    <!-- Brand -->
    <div class="flex items-center justify-center h-20 border-b border-gray-100">
        <div class="flex items-center space-x-2">
            <x-application-logo class="block h-8 w-auto fill-current text-teal-700" />
            <span class="text-2xl font-bold tracking-tight bg-gray-900 rounded px-2"><span
                    class="text-[#FF7F50]">Fin</span><span class="text-white">Core</span></span>
        </div>
    </div>

    <!-- Nav -->
    <div class="flex flex-col flex-grow overflow-y-auto pt-6 pb-4">
        <nav class="flex-1 px-4 space-y-6" aria-label="Sidebar">

            <!-- Section: Overview -->
            <div>
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Overview</p>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('dashboard') }}"
                        class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150 {{ request()->routeIs('dashboard') ? 'bg-teal-50 text-teal-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="flex-shrink-0 -ml-1 mr-3 h-6 w-6 {{ request()->routeIs('dashboard') ? 'text-teal-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="truncate">Dashboard</span>
                        @if(request()->routeIs('dashboard'))
                            <span class="ml-auto bg-teal-600 w-1.5 h-1.5 rounded-full" aria-hidden="true"></span>
                        @endif
                    </a>
                </div>
            </div>

            <!-- Section: Financials -->
            <div>
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Financials</p>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('incomes.index') }}"
                        class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150 {{ request()->routeIs('incomes.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="flex-shrink-0 -ml-1 mr-3 h-6 w-6 {{ request()->routeIs('incomes.*') ? 'text-teal-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <span class="truncate">Income</span>
                        @if(request()->routeIs('incomes.*'))
                            <span class="ml-auto bg-teal-600 w-1.5 h-1.5 rounded-full" aria-hidden="true"></span>
                        @endif
                    </a>

                    <a href="{{ route('expenses.index') }}"
                        class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150 {{ request()->routeIs('expenses.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="flex-shrink-0 -ml-1 mr-3 h-6 w-6 {{ request()->routeIs('expenses.*') ? 'text-teal-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="truncate">Expenses</span>
                        @if(request()->routeIs('expenses.*'))
                            <span class="ml-auto bg-teal-600 w-1.5 h-1.5 rounded-full" aria-hidden="true"></span>
                        @endif
                    </a>

                    <a href="{{ route('budgets.index') }}"
                        class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150 {{ request()->routeIs('budgets.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="flex-shrink-0 -ml-1 mr-3 h-6 w-6 {{ request()->routeIs('budgets.*') ? 'text-teal-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span class="truncate">Budgets</span>
                        @if(request()->routeIs('budgets.*'))
                            <span class="ml-auto bg-teal-600 w-1.5 h-1.5 rounded-full" aria-hidden="true"></span>
                        @endif
                    </a>
                </div>
            </div>

            <!-- Section: Growth -->
            <div>
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Growth</p>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('investments.index') }}"
                        class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150 {{ request()->routeIs('investments.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="flex-shrink-0 -ml-1 mr-3 h-6 w-6 {{ request()->routeIs('investments.*') ? 'text-teal-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        <span class="truncate">Investments</span>
                        @if(request()->routeIs('investments.*'))
                            <span class="ml-auto bg-teal-600 w-1.5 h-1.5 rounded-full" aria-hidden="true"></span>
                        @endif
                    </a>

                    <a href="{{ route('savings.index') }}"
                        class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150 {{ request()->routeIs('savings.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="flex-shrink-0 -ml-1 mr-3 h-6 w-6 {{ request()->routeIs('savings.*') ? 'text-teal-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="truncate">Savings</span>
                        @if(request()->routeIs('savings.*'))
                            <span class="ml-auto bg-teal-600 w-1.5 h-1.5 rounded-full" aria-hidden="true"></span>
                        @endif
                    </a>
                </div>
            </div>

            <!-- Section: System -->
            <div>
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">System</p>
                <div class="mt-2 space-y-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                            class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-red-50 hover:text-red-700 transition-colors duration-150">
                            <svg class="flex-shrink-0 -ml-1 mr-3 h-6 w-6 text-gray-400 group-hover:text-red-500"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            <span class="truncate">Log Out</span>
                        </a>
                    </form>
                </div>
            </div>

        </nav>
    </div>
</div>