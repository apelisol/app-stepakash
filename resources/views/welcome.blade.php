<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StepaKash - Simple way to manage your money</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
        extend: {
            colors: {
            primary: {
                DEFAULT: '#0f753a',
                50: '#f0f9f4',
                100: '#dcf2e4',
                200: '#bbe5cd',
                300: '#8dd2ac',
                400: '#58b885',
                500: '#0f753a',
                600: '#0d6632',
                700: '#0b5529',
                800: '#094420',
                900: '#07381b',
            },
            secondary: {
                DEFAULT: '#2c8a53',
                50: '#f1f9f4',
                100: '#def2e6',
                200: '#c0e5d1',
                300: '#93d1b0',
                400: '#5fb588',
                500: '#2c8a53',
                600: '#237643',
                700: '#1d6137',
                800: '#194e2e',
                900: '#154025',
            },
            accent: {
                DEFAULT: '#efd050',
                50: '#fefce8',
                100: '#fef9c3',
                200: '#fef08a',
                300: '#fde047',
                400: '#efd050',
                500: '#eab308',
                600: '#ca8a04',
                700: '#a16207',
                800: '#854d0e',
                900: '#713f12',
                hover: '#f4dc74',
            },
            text: {
                DEFAULT: '#333333',
                light: '#666666',
            },
            background: '#e8e8e8',
            'card-bg': '#ffffff',
            success: '#0f753a',
            danger: '#e74c3c',
            warning: '#f39c12',
            },
            animation: {
                'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                'fade-in-down': 'fadeInDown 0.8s ease-out forwards',
                'fade-in': 'fadeIn 1s ease-out forwards',
                'float': 'float 6s ease-in-out infinite',
                'float-delayed': 'float 6s ease-in-out infinite -3s',
                'pulse-ring': 'pulse-ring 2s infinite',
            },
            keyframes: {
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                fadeInDown: {
                    '0%': { opacity: '0', transform: 'translateY(-20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0px) rotate(0deg)' },
                    '50%': { transform: 'translateY(-20px) rotate(2deg)' },
                },
                'pulse-ring': {
                    '0%': {
                        transform: 'scale(0.33)',
                        opacity: '1',
                    },
                    '80%, 100%': {
                        transform: 'scale(2.33)',
                        opacity: '0',
                    },
                },
            }
        }
        }
    }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #0f753a 0%, #2c8a53 100%);
        }
        
        .phone-shadow {
            filter: drop-shadow(0 25px 50px rgba(15, 117, 58, 0.15));
        }
        
        .hover-lift {
            transition: all 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-5px);
        }
        
        .card-glow {
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(15, 117, 58, 0.1);
        }
        
        .card-glow:hover {
            box-shadow: 0 10px 40px rgba(15, 117, 58, 0.2);
            transform: translateY(-2px);
        }

        /* Animation delays */
        .animation-delay-100 {
            animation-delay: 0.1s;
        }
        .animation-delay-200 {
            animation-delay: 0.2s;
        }
        .animation-delay-300 {
            animation-delay: 0.3s;
        }
        .animation-delay-400 {
            animation-delay: 0.4s;
        }
        .animation-delay-500 {
            animation-delay: 0.5s;
        }
        .animation-delay-600 {
            animation-delay: 0.6s;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-primary-50 to-secondary-50 overflow-x-hidden">
    @include('partials.header');
    <!-- Hero Section -->
    <main class="relative min-h-screen flex items-center">
        <!-- Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-20 left-10 w-32 h-32 bg-accent-200 rounded-full opacity-20 animate-float"></div>
            <div class="absolute top-40 right-20 w-24 h-24 bg-primary-200 rounded-full opacity-30 animate-float-delayed"></div>
            <div class="absolute bottom-20 left-1/4 w-16 h-16 bg-secondary-200 rounded-full opacity-25 animate-float"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center relative z-10">
            <!-- Left Content -->
            <div class="space-y-8">
                <div class="space-y-6">
                    <h1 class="text-5xl lg:text-6xl font-bold text-text leading-tight animate-fade-in-up animation-delay-100 opacity-0">
                        Simple way<br>
                        to <span class="text-primary">manage</span><br>
                        your <span class="text-primary">Deriv</span> account
                    </h1>
                    
                    <p class="text-xl text-text-light max-w-md animate-fade-in-up animation-delay-300 opacity-0">
                        Connect your money to your friends & brands with StepaKash's secure and innovative platform.
                    </p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <button class="group bg-primary hover:bg-primary-600 text-white px-8 py-4 rounded-full font-semibold transition-all duration-300 hover:scale-105 hover:shadow-xl flex items-center space-x-2 animate-fade-in-up animation-delay-500 opacity-0">
                        <span>Get Started</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </button>
                    
                    <button class="group border-2 border-primary text-primary hover:bg-primary-50 px-8 py-4 rounded-full font-semibold transition-all duration-300 hover:scale-105 flex items-center space-x-2 animate-fade-in-up animation-delay-600 opacity-0">
                        <span>Learn More</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Sparkle decoration -->
                <div class="relative">
                    <div class="absolute -top-4 left-32 text-accent text-2xl animate-fade-in animation-delay-700 opacity-0">✨</div>
                </div>
                
                <!-- User Avatars -->
                <div class="flex items-center space-x-4 pt-8 animate-fade-in-up animation-delay-800 opacity-0">
                    <div class="flex -space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full border-3 border-white"></div>
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-cyan-400 rounded-full border-3 border-white"></div>
                        <div class="w-12 h-12 bg-gradient-to-r from-green-400 to-emerald-400 rounded-full border-3 border-white"></div>
                    </div>
                    <div class="text-sm text-text-light">
                        <p class="font-semibold text-text">Over 2.5 million users worldwide,</p>
                        <p>everyone loves our secure service.</p>
                    </div>
                </div>
            </div>
            
            <!-- Right Content - Phone Mockup -->
            <div class="relative flex justify-center lg:justify-end">
                <!-- Phone Container -->
                <div class="relative phone-shadow animate-fade-in-down animation-delay-200 opacity-0">
                    <!-- Phone Frame -->
                    <div class="relative w-80 h-[640px] bg-gray-800 rounded-[3rem] p-2">
                        <!-- Screen -->
                        <div class="w-full h-full bg-gray-700 rounded-[2.5rem] overflow-hidden relative">
                            <!-- Status Bar -->
                            <div class="flex justify-between items-center px-6 py-3 text-white text-sm">
                                <span class="font-medium">9:41</span>
                                <div class="flex space-x-1">
                                    <div class="w-4 h-2 bg-white rounded-sm"></div>
                                    <div class="w-1 h-2 bg-white rounded-sm"></div>
                                    <div class="w-6 h-2 bg-white rounded-sm"></div>
                                </div>
                            </div>
                            
                            <!-- App Content -->
                            <div class="px-6 space-y-6">
                                <!-- Welcome Header -->
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-white text-lg font-semibold">Hello Apeli</h3>
                                        <p class="text-gray-300 text-sm">Welcome back</p>
                                    </div>
                                    <div class="w-10 h-10 bg-gradient-to-r from-accent to-accent-300 rounded-full"></div>
                                </div>
                                
                                <!-- Balance Card -->
                                <div class="bg-white rounded-2xl p-6 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                                            <span class="text-white font-bold text-lg">SK</span>
                                        </div>
                                        <span class="text-gray-400 text-sm">SK0010N</span>
                                    </div>
                                    
                                    <div class="space-y-1">
                                        <div class="text-3xl font-bold text-text">$3,247.82</div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-text-light">Livingstone Apeli</span>
                                            <span class="text-xs text-text-light">Exp. 08/28</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="grid grid-cols-4 gap-4">
                                    <div class="flex flex-col items-center space-y-2">
                                        <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                            </svg>
                                        </div>
                                        <span class="text-xs text-white">Send</span>
                                    </div>
                                    <div class="flex flex-col items-center space-y-2">
                                        <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <span class="text-xs text-white">Bill</span>
                                    </div>
                                    <div class="flex flex-col items-center space-y-2">
                                        <div class="w-12 h-12 bg-secondary-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <span class="text-xs text-white">Mobile</span>
                                    </div>
                                    <div class="flex flex-col items-center space-y-2">
                                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                            </svg>
                                        </div>
                                        <span class="text-xs text-white">More</span>
                                    </div>
                                </div>
                                
                                <!-- Transactions -->
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-white font-semibold">Transactions</h4>
                                        <span class="text-accent text-sm">See all</span>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                                    <span class="text-white text-sm font-bold">DW</span>
                                                </div>
                                                <div>
                                                    <p class="text-white text-sm font-medium">Deriv Withd...</p>
                                                    <p class="text-gray-400 text-xs">30 July 2025</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-white font-semibold">$1,200.99</p>
                                                <p class="text-gray-400 text-xs">6:40 PM</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center">
                                                    <span class="text-white text-sm font-bold">E</span>
                                                </div>
                                                <div>
                                                    <p class="text-white text-sm font-medium">Apeli Living..</p>
                                                    <p class="text-gray-400 text-xs">28 July 2024</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-white font-semibold">$49.00</p>
                                                <p class="text-gray-400 text-xs">3:26 AM</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center">
                                                    <span class="text-white text-sm font-bold">E</span>
                                                </div>
                                                <div>
                                                    <p class="text-white text-sm font-medium">Apeli Living..</p>
                                                    <p class="text-gray-400 text-xs">28 July 2024</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-white font-semibold">$49.00</p>
                                                <p class="text-gray-400 text-xs">3:26 AM</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Floating Feature Cards -->
                <div class="absolute -right-8 top-16 card-glow hover-lift animate-fade-in animation-delay-700 opacity-0">
                    <div class="bg-white rounded-2xl p-4 shadow-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse-ring"></div>
                            <div>
                                <p class="font-semibold text-sm text-text">24/7 Support</p>
                                <p class="text-xs text-text-light">Always here to help</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="absolute -left-12 bottom-32 card-glow hover-lift animate-fade-in animation-delay-900 opacity-0">
                    <div class="bg-white rounded-2xl p-4 shadow-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-text">100% Safe</p>
                                <p class="text-xs text-text-light">Your money is secure</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Decorative Circle -->
                <div class="absolute -top-8 right-12 w-20 h-20 border-2 border-accent rounded-full animate-float-delayed opacity-30"></div>
            </div>
        </div>
    </main>
    
    <!-- Download Section -->
    <section class="py-12 px-6">
        <div class="max-w-7xl mx-auto flex justify-center space-x-4">
            <!-- App Store Button -->
            <div class="bg-black hover:bg-gray-800 text-white px-6 py-3 rounded-lg flex items-center space-x-3 cursor-pointer transition-all hover:scale-105 hover-lift">
                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                </svg>
                <div>
                    <p class="text-xs">Available on</p>
                    <p class="font-semibold">App Store</p>
                </div>
            </div>
            
            <!-- Google Play Button -->
            <div class="bg-black hover:bg-gray-800 text-white px-6 py-3 rounded-lg flex items-center space-x-3 cursor-pointer transition-all hover:scale-105 hover-lift">
                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.15L13.69,12L3.84,21.85C3.34,21.6 3,21.09 3,20.5M16.81,15.12L6.05,21.34L14.54,12.85L16.81,15.12M20.16,10.81C20.5,11.08 20.75,11.5 20.75,12C20.75,12.5 20.53,12.9 20.18,13.18L17.89,14.5L15.39,12L17.89,9.5L20.16,10.81M6.05,2.66L16.81,8.88L14.54,11.15L6.05,2.66Z"/>
                </svg>
                <div>
                    <p class="text-xs">Available on</p>
                    <p class="font-semibold">Google Play</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('partials.footer');

    <script>
        // Optional: Add intersection observer for scroll animations if needed
        document.addEventListener('DOMContentLoaded', function() {
            // This ensures all animations play when the page loads
            // No additional JS is needed for the initial animations as they're handled by CSS
        });
    </script>
</body>
</html>