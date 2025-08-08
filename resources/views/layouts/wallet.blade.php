<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="StepaKash Wallet - Your digital wallet for M-Pesa, Deriv, and more">
    
    <title>@yield('title') | StepaKash</title>
    
    <!-- Favicons -->
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}" sizes="180x180">
    <link rel="icon" href="{{ asset('images/logo.png') }}" sizes="32x32" type="image/png">
    <link rel="icon" href="{{ asset('images/logo.png') }}" sizes="16x16" type="image/png">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#0f753a',
                            dark: '#3e9965',
                        },
                        secondary: {
                            DEFAULT: '#2c8a53',
                            dark: '#5aab7d',
                        },
                        accent: {
                            DEFAULT: '#efd050',
                            dark: '#f4dc74',
                        },
                        dark: {
                            DEFAULT: '#1e293b',
                            light: '#334155',
                        },
                        light: {
                            DEFAULT: '#f8fafc',
                            dark: '#e2e8f0',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/wallet.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body class="font-sans bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-gray-100">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 h-16 bg-white dark:bg-dark shadow-sm z-50 border-b border-gray-100 dark:border-gray-800">
        <div class="container mx-auto px-4 h-full flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <button id="sidebarToggle" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800">
                    <i class="fas fa-bars text-gray-600 dark:text-gray-300"></i>
                </button>
            </div>
            
            <div class="flex-1 flex justify-center">
                <img src="{{ asset('images/logo-text.png') }}" alt="StepaKash" class="h-8">
            </div>
            
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <button id="profileToggle" class="flex items-center space-x-1 p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                        @if(auth()->user()->agent)
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-accent rounded-full flex items-center justify-center text-xs text-gray-800">✓</span>
                        @endif
                    </button>
                    
                    <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden">
                        <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white">
                                    <i class="fas fa-user text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ auth()->user()->phone }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Wallet ID: {{ auth()->user()->wallet_id }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Sidebar -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden opacity-0 transition-opacity duration-300"></div>
    
    <aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-white dark:bg-gray-800 shadow-lg transform -translate-x-full transition-transform duration-300 z-50">
        <div class="h-full flex flex-col">
            <!-- Sidebar Header -->
            <div class="p-4 bg-gradient-to-r from-primary to-secondary text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-white to-transparent opacity-10"></div>
                <div class="flex items-center space-x-3 relative z-10">
                    <div class="w-12 h-12 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                        <i class="fas fa-wallet text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold">{{ auth()->user()->phone }}</h3>
                        <p class="text-xs opacity-80">Wallet ID: {{ auth()->user()->wallet_id }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar Menu -->
            <nav class="flex-1 overflow-y-auto py-4">
                <ul>
                    <li>
                        <a href="{{ route('wallet.dashboard') }}" class="flex items-center px-6 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition-colors duration-200">
                            <i class="fas fa-home mr-3 text-blue-500"></i> 
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('wallet.balance') }}" class="flex items-center px-6 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition-colors duration-200">
                            <i class="fas fa-wallet mr-3 text-green-500"></i>
                            <span>My Balance</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('wallet.send') }}" class="flex items-center px-6 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition-colors duration-200">
                            <i class="fas fa-paper-plane mr-3 text-purple-500"></i>
                            <span>Send Money</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('wallet.bank') }}" class="flex items-center px-6 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition-colors duration-200">
                            <i class="fas fa-university mr-3 text-yellow-500"></i>
                            <span>Bank Transfer</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('wallet.airtime') }}" class="flex items-center px-6 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition-colors duration-200">
                            <i class="fas fa-mobile-alt mr-3 text-red-500"></i>
                            <span>Buy Airtime</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('wallet.bundles') }}" class="flex items-center px-6 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition-colors duration-200">
                            <i class="fas fa-wifi mr-3 text-indigo-500"></i>
                            <span>Buy Bundles</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('wallet.transactions') }}" class="flex items-center px-6 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition-colors duration-200">
                            <i class="fas fa-exchange-alt mr-3 text-teal-500"></i>
                            <span>Transactions</span>
                        </a>
                    </li>
                    <li class="border-t border-gray-200 dark:border-gray-700 mt-2 pt-2">
                        <a href="{{ route('logout') }}" class="flex items-center px-6 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 text-red-600 dark:text-red-400 transition-colors duration-200">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Sidebar Footer - Contact Info -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col space-y-3">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Need Help?</h4>
                    <a href="https://wa.me/254741554994" target="_blank" class="flex items-center justify-center space-x-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        <i class="fab fa-whatsapp text-xl"></i>
                        <span>Chat on WhatsApp</span>
                    </a>
                    <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                        <p>Support available 24/7</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar Footer - Theme Toggle -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Dark Mode</span>
                    <button id="sidebarThemeToggle" class="p-2 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        <i class="fas fa-moon text-gray-600 dark:text-yellow-300 theme-icon"></i>
                    </button>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 pt-16 pb-20 px-4">
        @yield('content')
    </main>

    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 shadow-lg border-t border-gray-100 dark:border-gray-700 z-40">
        <div class="container mx-auto px-4">
            <div class="flex justify-between">
                <a href="{{ route('wallet.dashboard') }}" class="flex flex-col items-center py-3 px-4 {{ request()->routeIs('wallet.dashboard') ? 'text-primary dark:text-primary-dark' : 'text-gray-500 dark:text-gray-400' }}">
                    <i class="fas fa-home text-lg mb-1"></i>
                    <span class="text-xs">Home</span>
                </a>
                <a href="{{ route('wallet.send') }}" class="flex flex-col items-center py-3 px-4 {{ request()->routeIs('wallet.send*') ? 'text-primary dark:text-primary-dark' : 'text-gray-500 dark:text-gray-400' }}">
                    <i class="fas fa-paper-plane text-lg mb-1"></i>
                    <span class="text-xs">Send</span>
                </a>
                <a href="{{ route('wallet.airtime') }}" class="flex flex-col items-center py-3 px-4 {{ request()->routeIs('wallet.airtime*') || request()->routeIs('wallet.bundles*') ? 'text-primary dark:text-primary-dark' : 'text-gray-500 dark:text-gray-400' }}">
                    <i class="fas fa-mobile-alt text-lg mb-1"></i>
                    <span class="text-xs">Airtime</span>
                </a>
                <a href="{{ route('wallet.transactions') }}" class="flex flex-col items-center py-3 px-4 {{ request()->routeIs('wallet.transactions*') ? 'text-primary dark:text-primary-dark' : 'text-gray-500 dark:text-gray-400' }}">
                    <i class="fas fa-exchange-alt text-lg mb-1"></i>
                    <span class="text-xs">Transactions</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/wallet.js') }}"></script>
    
    @stack('scripts')
</body>
</html>