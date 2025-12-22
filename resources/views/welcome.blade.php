<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FinCore - Integrated Personal Finance Intelligence</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold: '#ff7f50',
                        teal: '#037b90',
                        white: '#ffffff',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 1s ease-out forwards',
                        'fade-in': 'fadeIn 1.2s ease-out forwards',
                        'slide-up': 'slideUp 0.8s ease-out forwards',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(100%)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .bg-teal {
            background-color: #037b90;
        }

        .text-teal {
            color: #037b90;
        }

        .border-teal {
            border-color: #037b90;
        }

        .bg-gold {
            background-color: #ff7f50;
        }

        .text-gold {
            color: #ff7f50;
        }

        .border-gold {
            border-color: #ff7f50;
        }

        .animate-delay-200 {
            animation-delay: 200ms;
        }

        .animate-delay-400 {
            animation-delay: 400ms;
        }

        .animate-delay-600 {
            animation-delay: 600ms;
        }

        /* Floating Animation */
        @keyframes float {
            0% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(5deg);
            }

            100% {
                transform: translateY(0px) rotate(0deg);
            }
        }

        @keyframes float-reverse {
            0% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-15px) rotate(-5deg);
            }

            100% {
                transform: translateY(0px) rotate(0deg);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-float-slow {
            animation: float-reverse 8s ease-in-out infinite;
        }

        .animate-float-fast {
            animation: float 5s ease-in-out infinite;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-white font-sans text-gray-900 antialiased">

    <!-- Navigation Overlay -->
    <div class="absolute top-0 right-0 p-6 z-50">
        <a href="{{ route('login') }}"
            class="px-6 py-2 bg-gold text-white rounded-full font-bold shadow-lg hover:bg-orange-600 transition duration-300 flex items-center transform hover:-translate-y-0.5">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 0 01-3-3V7a3 0 013-3h7a3 0 013 3v1"></path>
            </svg>
            Login
        </a>
    </div>

    <!-- 1. HERO SECTION -->
    <header class="bg-teal text-white pt-20 pb-24 relative overflow-hidden">
        <!-- Animated Background Icons -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <!-- Graduation Cap -->
            <svg class="absolute top-10 left-10 w-24 h-24 text-teal-400 opacity-20 animate-float" fill="currentColor"
                viewBox="0 0 24 24">
                <path d="M12 3L1 9l11 6 9-4.91V17h2V9M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z" />
            </svg>
            <!-- Laptop/Tech -->
            <svg class="absolute bottom-20 right-20 w-32 h-32 text-gold opacity-10 animate-float-slow"
                fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M20 18c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2h-4zM4 6h16v10H4V6z" />
            </svg>
            <!-- Chart/Finance -->
            <svg class="absolute top-1/3 right-10 w-16 h-16 text-white opacity-10 animate-float-fast"
                fill="currentColor" viewBox="0 0 24 24">
                <path d="M3.5 18.49l6-6.01 4 4L22 6.92l-1.41-1.41-7.09 7.97-4-4L2 16.99z" />
            </svg>
            <!-- Diploma -->
            <svg class="absolute bottom-10 left-1/4 w-20 h-20 text-teal-200 opacity-10 animate-float"
                style="animation-delay: 1s;" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm7 16H5V5h14v14z" />
                <path d="M12 18c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z" opacity=".3" />
            </svg>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h1 class="text-5xl md:text-6xl font-bold tracking-tight mb-4 animate-fade-in-up">
                <span class="text-gold">Fin</span>Core
            </h1>
            <p class="text-2xl md:text-3xl font-light mb-8 text-gray-200 animate-fade-in-up animate-delay-200 opacity-0"
                style="animation-fill-mode: forwards;">
                Integrated Personal Finance Intelligence
            </p>
            <div class="flex justify-center space-x-4 mb-12 text-sm md:text-base font-medium tracking-wide uppercase text-teal-200 animate-fade-in-up animate-delay-400 opacity-0"
                style="animation-fill-mode: forwards;">
                <span>Track</span> <span class="text-gold">•</span>
                <span>Control</span> <span class="text-gold">•</span>
                <span>Grow</span> <span class="text-gold">•</span>
                <span>Forecast</span>
            </div>
            <div class="flex flex-col sm:flex-row justify-center gap-4 animate-fade-in-up animate-delay-600 opacity-0"
                style="animation-fill-mode: forwards;">
                <a href="#system-design"
                    class="px-8 py-3 border border-white text-white rounded hover:bg-white hover:text-dark-teal transition duration-300">
                    View System Design
                </a>
                <a href="{{ route('login') }}"
                    class="px-8 py-3 bg-gold text-white rounded hover:bg-orange-600 transition duration-300 shadow-lg">
                    Login Portal
                </a>
            </div>
        </div>
        <!-- Abstract Background Element -->
        <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 L100 0 L100 100 Z" fill="#ffffff" />
            </svg>
        </div>
    </header>

    <!-- 2. SYSTEM OVERVIEW -->
    <section id="system-design" class="py-20 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-dark-teal mb-8">System Overview</h2>
            <p class="text-xl text-gray-600 mb-16 max-w-3xl mx-auto leading-relaxed">
                <span class="text-3xl font-bold bg-gray-900 rounded-lg px-3 py-1 inline-block"><span
                        class="text-[#FF7F50]">Fin</span><span class="text-white">Core</span></span> is a modular
                personal finance system designed to track
                income, expenses, savings, and investments while providing intelligent insights into overall financial
                health through a centralized analytics engine.
            </p>

            <!-- Architecture Diagram -->
            <div class="flex flex-col items-center justify-center space-y-4 md:space-y-0 md:flex-row md:space-x-8 mb-8">
                <div class="flex gap-4 md:gap-8">
                    <div class="px-6 py-3 bg-gray-100 rounded border border-gray-300 text-gray-700 font-semibold">Income
                    </div>
                    <div class="px-6 py-3 bg-gray-100 rounded border border-gray-300 text-gray-700 font-semibold">
                        Expenses</div>
                </div>
                <div class="flex gap-4 md:gap-8 mt-4 md:mt-0">
                    <div class="px-6 py-3 bg-gray-100 rounded border border-gray-300 text-gray-700 font-semibold">
                        Savings</div>
                    <div class="px-6 py-3 bg-gray-100 rounded border border-gray-300 text-gray-700 font-semibold">
                        Investments</div>
                </div>
            </div>

            <!-- Connecting Lines (Visualized via CSS/SVG) -->
            <div class="flex justify-center mb-2">
                <svg width="400" height="40" viewBox="0 0 400 40" class="text-gray-400">
                    <line x1="50" y1="0" x2="200" y2="40" stroke="currentColor" stroke-width="2" />
                    <line x1="150" y1="0" x2="200" y2="40" stroke="currentColor" stroke-width="2" />
                    <line x1="250" y1="0" x2="200" y2="40" stroke="currentColor" stroke-width="2" />
                    <line x1="350" y1="0" x2="200" y2="40" stroke="currentColor" stroke-width="2" />
                </svg>
            </div>

            <div
                class="inline-block px-10 py-4 bg-dark-teal text-white rounded-lg shadow-lg font-bold text-lg mb-4 relative z-10">
                FinCore Engine
            </div>

            <div class="flex justify-center mb-2">
                <svg width="40" height="40" viewBox="0 0 40 40" class="text-gray-400">
                    <line x1="20" y1="0" x2="20" y2="40" stroke="currentColor" stroke-width="2" />
                </svg>
            </div>

            <div class="inline-block px-10 py-4 bg-gold text-white rounded-lg shadow-lg font-bold text-lg">
                Integrated Dashboard
            </div>
        </div>
    </section>

    <!-- 3. CORE MODULES -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-dark-teal mb-12 text-center">Core Modules</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
                <!-- Income -->
                <div class="bg-white p-6 rounded shadow border-t-4 border-teal hover:shadow-md transition">
                    <h3 class="font-bold text-lg mb-3 text-gray-900">Income</h3>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>• Source tracking</li>
                        <li>• Forecasting</li>
                        <li>• Stability analysis</li>
                    </ul>
                </div>
                <!-- Expenses -->
                <div class="bg-white p-6 rounded shadow border-t-4 border-gold hover:shadow-md transition">
                    <h3 class="font-bold text-lg mb-3 text-gray-900">Expenses</h3>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>• Categorization</li>
                        <li>• Budgets</li>
                        <li>• Anomaly detection</li>
                    </ul>
                </div>
                <!-- Savings -->
                <div class="bg-white p-6 rounded shadow border-t-4 border-teal hover:shadow-md transition">
                    <h3 class="font-bold text-lg mb-3 text-gray-900">Savings</h3>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>• Goals</li>
                        <li>• Optimization</li>
                        <li>• Emergency readiness</li>
                    </ul>
                </div>
                <!-- Investments -->
                <div class="bg-white p-6 rounded shadow border-t-4 border-gold hover:shadow-md transition">
                    <h3 class="font-bold text-lg mb-3 text-gray-900">Investments</h3>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>• Portfolio tracking</li>
                        <li>• Growth analysis</li>
                        <li>• Risk awareness</li>
                    </ul>
                </div>
                <!-- Engine -->
                <div class="bg-white p-6 rounded shadow border-t-4 border-dark-teal hover:shadow-md transition">
                    <h3 class="font-bold text-lg mb-3 text-gray-900">Finance Engine</h3>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>• Net worth</li>
                        <li>• Cash flow</li>
                        <li>• Scenario simulation</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. DASHBOARD PREVIEW -->
    <section class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-dark-teal mb-12 text-center">Intelligence Layer</h2>

            <!-- Static Dashboard Mock -->
            <div class="bg-gray-50 border border-gray-200 rounded-xl shadow-2xl overflow-hidden p-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-4 rounded shadow-sm border-l-4 border-teal">
                        <div class="text-xs text-gray-500 uppercase font-bold">Net Worth</div>
                        <div class="text-2xl font-bold text-gray-900">KES 4,250,000</div>
                        <div class="text-xs text-green-600 mt-1">▲ 12% vs last month</div>
                    </div>
                    <div class="bg-white p-4 rounded shadow-sm border-l-4 border-gold">
                        <div class="text-xs text-gray-500 uppercase font-bold">Monthly Cash Flow</div>
                        <div class="text-2xl font-bold text-gray-900">KES 85,000</div>
                        <div class="text-xs text-gray-400 mt-1">Positive</div>
                    </div>
                    <div class="bg-white p-4 rounded shadow-sm border-l-4 border-teal">
                        <div class="text-xs text-gray-500 uppercase font-bold">Savings Rate</div>
                        <div class="text-2xl font-bold text-gray-900">24%</div>
                        <div class="text-xs text-gray-400 mt-1">Target: 20%</div>
                    </div>
                    <div class="bg-white p-4 rounded shadow-sm border-l-4 border-dark-teal">
                        <div class="text-xs text-gray-500 uppercase font-bold">Health Score</div>
                        <div class="text-2xl font-bold text-gray-900">88/100</div>
                        <div class="text-xs text-green-600 mt-1">Excellent</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        class="md:col-span-2 bg-white h-64 rounded shadow-sm border border-gray-100 flex items-center justify-center">
                        <span class="text-gray-400 italic">[ Cash Flow Forecast Chart ]</span>
                    </div>
                    <div
                        class="bg-white h-64 rounded shadow-sm border border-gray-100 flex items-center justify-center">
                        <span class="text-gray-400 italic">[ Asset Allocation ]</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. SYSTEM DESIGN PHASES -->
    <section class="py-20 bg-dark-teal text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold mb-12 text-center">Engineering Maturity</h2>
            <div class="relative">
                <!-- Timeline Line -->
                <div class="hidden md:block absolute top-1/2 left-0 w-full h-1 bg-teal-700 -translate-y-1/2"></div>

                <div class="grid grid-cols-1 md:grid-cols-5 gap-8 relative z-10">
                    <!-- Phase 1 -->
                    <div class="bg-teal-800 p-6 rounded-lg border border-teal-600 text-center">
                        <div class="text-gold font-bold mb-2">Phase 1</div>
                        <h3 class="text-xl font-bold mb-2">Track</h3>
                        <p class="text-sm text-gray-300">Visibility & Basic Logging</p>
                    </div>
                    <!-- Phase 2 -->
                    <div class="bg-teal-800 p-6 rounded-lg border border-teal-600 text-center">
                        <div class="text-gold font-bold mb-2">Phase 2</div>
                        <h3 class="text-xl font-bold mb-2">Control</h3>
                        <p class="text-sm text-gray-300">Budgets & Limits</p>
                    </div>
                    <!-- Phase 3 -->
                    <div class="bg-teal-800 p-6 rounded-lg border border-teal-600 text-center">
                        <div class="text-gold font-bold mb-2">Phase 3</div>
                        <h3 class="text-xl font-bold mb-2">Automate</h3>
                        <p class="text-sm text-gray-300">Imports & Rules</p>
                    </div>
                    <!-- Phase 4 -->
                    <div class="bg-teal-800 p-6 rounded-lg border border-teal-600 text-center">
                        <div class="text-gold font-bold mb-2">Phase 4</div>
                        <h3 class="text-xl font-bold mb-2">Analyze</h3>
                        <p class="text-sm text-gray-300">Insights & Forecasts</p>
                    </div>
                    <!-- Phase 5 -->
                    <div class="bg-teal-800 p-6 rounded-lg border border-teal-600 text-center">
                        <div class="text-gold font-bold mb-2">Phase 5</div>
                        <h3 class="text-xl font-bold mb-2">Scale</h3>
                        <p class="text-sm text-gray-300">Compliance & Wealth</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. TECH STACK & SECURITY -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-16">
            <div>
                <h2 class="text-2xl font-bold text-dark-teal mb-6">Technology Stack</h2>
                <ul class="space-y-4 text-gray-700">
                    <li class="flex items-center">
                        <span class="w-24 font-bold text-teal">Frontend:</span>
                        <span>Laravel Blade + Tailwind + Vite</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-24 font-bold text-teal">Backend:</span>
                        <span>Laravel API</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-24 font-bold text-teal">Database:</span>
                        <span>MySQL / SQLite</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-24 font-bold text-teal">Auth:</span>
                        <span>Session + MFA (Phase 5)</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-24 font-bold text-teal">Analytics:</span>
                        <span>SQL + Rules Engine</span>
                    </li>
                </ul>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-dark-teal mb-6">Data & Security</h2>
                <ul class="space-y-4 text-gray-700">
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-gold mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Encrypted data storage (AES-256)</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-gold mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Role-based access control (RBAC)</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-gold mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Comprehensive audit logs</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-gold mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>User-controlled data exports</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- 7. CALL TO ACTION -->
    <footer class="bg-gray-900 text-white py-16 text-center">
        <div class="max-w-4xl mx-auto px-4">
            <h2 class="text-3xl font-bold mb-8">Ready to Explore the System?</h2>
            <div class="flex flex-col md:flex-row justify-center gap-6">
                <a href="#"
                    class="px-8 py-4 bg-transparent border border-gray-600 rounded hover:bg-gray-800 transition">
                    View System Requirements (SRD)
                </a>
                <a href="#"
                    class="px-8 py-4 bg-transparent border border-gray-600 rounded hover:bg-gray-800 transition">
                    Explore Architecture
                </a>
                <a href="{{ route('dashboard') }}"
                    class="px-8 py-4 bg-gold text-white rounded font-bold hover:bg-orange-600 transition shadow-lg">
                    Dashboard Walkthrough
                </a>
            </div>
            <p class="mt-12 text-gray-500 text-sm">
                &copy; {{ date('Y') }} Wab. Wire Systems. All rights reserved. | Powered by DevGee
            </p>
        </div>
    </footer>

</body>

</html>