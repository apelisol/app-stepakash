<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StepaKash - Help Center</title>
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
        
        .help-search {
            background: linear-gradient(rgba(15, 117, 58, 0.03), rgba(15, 117, 58, 0.03)), url('https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
        }
        
        .chat-container {
            height: 500px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .chat-messages {
            height: calc(100% - 60px);
            overflow-y: auto;
        }
        
        .user-message {
            background-color: #f0f9f4;
            border-radius: 12px 12px 0 12px;
        }
        
        .agent-message {
            background-color: #ffffff;
            border-radius: 12px 12px 12px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .typing-indicator:after {
            content: '.';
            animation: typing 1.5s infinite;
        }
        
        @keyframes typing {
            0% { content: '.'; }
            33% { content: '..'; }
            66% { content: '...'; }
        }
        
        .faq-item {
            transition: all 0.3s ease;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .faq-item:hover {
            background-color: #f5f5f5;
        }
        
        .faq-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .faq-item.active .faq-content {
            max-height: 300px;
        }
        
        .rotate-180 {
            transform: rotate(180deg);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-primary-50 to-secondary-50 overflow-x-hidden">
    <!-- Navigation -->
    @include('partials.header')

    <!-- Help Center Header -->
    <section class="help-search py-20">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6 text-primary">How can we help you today?</h1>
            <div class="max-w-2xl mx-auto relative">
                <input type="text" placeholder="Search help articles..." class="w-full px-6 py-4 rounded-full border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent shadow-sm">
                <button class="absolute right-2 top-2 bg-primary text-white p-2 rounded-full hover:bg-primary-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <!-- Main Help Content -->
    <section class="py-12 px-6">
        <div class="max-w-7xl mx-auto grid md:grid-cols-4 gap-8">
            <!-- Sidebar Navigation -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
                    <h3 class="font-semibold text-lg mb-4 text-primary">Help Topics</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-text hover:text-primary font-medium block py-2 border-b border-gray-100">Getting Started</a></li>
                        <li><a href="#" class="text-text hover:text-primary font-medium block py-2 border-b border-gray-100">Account Management</a></li>
                        <li><a href="#" class="text-text hover:text-primary font-medium block py-2 border-b border-gray-100">Deposits & Withdrawals</a></li>
                        <li><a href="#" class="text-text hover:text-primary font-medium block py-2 border-b border-gray-100">Deriv Integration</a></li>
                        <li><a href="#" class="text-text hover:text-primary font-medium block py-2 border-b border-gray-100">Security</a></li>
                        <li><a href="#" class="text-text hover:text-primary font-medium block py-2 border-b border-gray-100">Fees & Limits</a></li>
                        <li><a href="#" class="text-text hover:text-primary font-medium block py-2">Troubleshooting</a></li>
                    </ul>
                    
                    <div class="mt-8">
                        <h3 class="font-semibold text-lg mb-4 text-primary">Contact Options</h3>
                        <div class="space-y-4">
                            <button id="liveChatBtn" class="w-full bg-primary hover:bg-primary-600 text-white py-3 px-4 rounded-lg flex items-center justify-center space-x-2 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span>Live Chat</span>
                            </button>
                            <a href="mailto:support@stepakash.com" class="block text-center border border-primary text-primary hover:bg-primary-50 py-3 px-4 rounded-lg transition-colors">Email Support</a>
                            <a href="tel:+254741554994" class="block text-center border border-gray-300 hover:border-primary text-text hover:text-primary py-3 px-4 rounded-lg transition-colors">Call Support</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content Area -->
            <div class="md:col-span-3">
                <!-- Popular Articles -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <h2 class="text-2xl font-bold text-primary mb-6">Popular Help Articles</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <a href="#" class="p-4 border border-gray-100 hover:border-primary rounded-lg transition-colors group">
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-text group-hover:text-primary transition-colors">Creating Your StepaKash Account</h3>
                                    <p class="text-sm text-text-light">Step-by-step guide to setting up your account</p>
                                </div>
                            </div>
                        </a>
                        
                        <a href="#" class="p-4 border border-gray-100 hover:border-primary rounded-lg transition-colors group">
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-text group-hover:text-primary transition-colors">Linking Your Deriv Account</h3>
                                    <p class="text-sm text-text-light">How to connect your Deriv trading account</p>
                                </div>
                            </div>
                        </a>
                        
                        <a href="#" class="p-4 border border-gray-100 hover:border-primary rounded-lg transition-colors group">
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-text group-hover:text-primary transition-colors">Two-Factor Authentication</h3>
                                    <p class="text-sm text-text-light">Setting up extra security for your account</p>
                                </div>
                            </div>
                        </a>
                        
                        <a href="#" class="p-4 border border-gray-100 hover:border-primary rounded-lg transition-colors group">
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-text group-hover:text-primary transition-colors">Resetting Your Password</h3>
                                    <p class="text-sm text-text-light">What to do if you forget your password</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- FAQ Section -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <h2 class="text-2xl font-bold text-primary mb-6">Frequently Asked Questions</h2>
                    <div class="space-y-4">
                        <!-- FAQ Item 1 -->
                        <div class="faq-item p-4 rounded-lg cursor-pointer">
                            <div class="flex justify-between items-center">
                                <h3 class="font-medium text-text">How do I deposit funds into my StepaKash account?</h3>
                                <svg class="w-5 h-5 text-primary transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                            <div class="faq-content mt-2">
                                <div class="pt-2 text-text-light">
                                    <p>You can deposit funds into your StepaKash account through multiple methods:</p>
                                    <ul class="list-disc pl-5 space-y-1 mt-2">
                                        <li>Bank transfer (direct from your bank account)</li>
                                        <li>Mobile money (M-Pesa, Airtel Money, etc.)</li>
                                        <li>Debit/Credit card</li>
                                        <li>Crypto deposits (Bitcoin, Ethereum, USDT)</li>
                                    </ul>
                                    <p class="mt-2">Navigate to the "Deposit" section in your account dashboard and select your preferred method.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- FAQ Item 2 -->
                        <div class="faq-item p-4 rounded-lg cursor-pointer">
                            <div class="flex justify-between items-center">
                                <h3 class="font-medium text-text">What are the withdrawal limits on StepaKash?</h3>
                                <svg class="w-5 h-5 text-primary transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                            <div class="faq-content mt-2">
                                <div class="pt-2 text-text-light">
                                    <p>StepaKash has the following withdrawal limits:</p>
                                    <ul class="list-disc pl-5 space-y-1 mt-2">
                                        <li><strong>Basic accounts:</strong> $1,000 per day</li>
                                        <li><strong>Verified accounts:</strong> $5,000 per day</li>
                                        <li><strong>Premium accounts:</strong> $20,000 per day</li>
                                    </ul>
                                    <p class="mt-2">Higher limits may be available for institutional clients. All withdrawals are subject to verification for security purposes.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- FAQ Item 3 -->
                        <div class="faq-item p-4 rounded-lg cursor-pointer">
                            <div class="flex justify-between items-center">
                                <h3 class="font-medium text-text">How long do withdrawals to Deriv take?</h3>
                                <svg class="w-5 h-5 text-primary transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                            <div class="faq-content mt-2">
                                <div class="pt-2 text-text-light">
                                    <p>Withdrawals to Deriv are typically processed within:</p>
                                    <ul class="list-disc pl-5 space-y-1 mt-2">
                                        <li><strong>Standard processing:</strong> 5-15 minutes during business hours</li>
                                        <li><strong>During high traffic:</strong> Up to 1 hour</li>
                                    </ul>
                                    <p class="mt-2">If your withdrawal takes longer than 2 hours, please contact our support team with your transaction reference number.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- FAQ Item 4 -->
                        <div class="faq-item p-4 rounded-lg cursor-pointer">
                            <div class="flex justify-between items-center">
                                <h3 class="font-medium text-text">Is there a fee for transferring funds to Deriv?</h3>
                                <svg class="w-5 h-5 text-primary transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                            <div class="faq-content mt-2">
                                <div class="pt-2 text-text-light">
                                    <p>StepaKash charges a small fee for Deriv transfers:</p>
                                    <ul class="list-disc pl-5 space-y-1 mt-2">
                                        <li><strong>Standard transfer:</strong> 0.5% of amount (min $0.50)</li>
                                        <li><strong>Express transfer:</strong> 1% of amount (min $1.00, processed within 5 minutes)</li>
                                    </ul>
                                    <p class="mt-2">Premium account holders enjoy reduced fees of 0.25% for all transfers.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Video Tutorials -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-2xl font-bold text-primary mb-6">Video Tutorials</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-gray-100 rounded-lg overflow-hidden">
                            <div class="aspect-w-16 aspect-h-9 bg-black">
                                <div class="w-full h-full flex items-center justify-center text-white">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-text">Getting Started with StepaKash</h3>
                                <p class="text-sm text-text-light mt-1">3 min video</p>
                            </div>
                        </div>
                        
                        <div class="bg-gray-100 rounded-lg overflow-hidden">
                            <div class="aspect-w-16 aspect-h-9 bg-black">
                                <div class="w-full h-full flex items-center justify-center text-white">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-text">Linking Your Deriv Account</h3>
                                <p class="text-sm text-text-light mt-1">5 min video</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Live Chat Modal (Hidden by default) -->
    <div id="chatModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black bg-opacity-50">
        <div class="bg-white rounded-xl w-full max-w-md chat-container">
            <!-- Chat Header -->
            <div class="bg-primary text-white p-4 flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold">StepaKash Support</h3>
                        <p class="text-xs opacity-80">Typically replies in 2 minutes</p>
                    </div>
                </div>
                <button id="closeChatBtn" class="text-white hover:text-accent transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Chat Messages -->
            <div class="chat-messages p-4 space-y-4">
                <!-- Initial greeting -->
                <div class="flex justify-start">
                    <div class="agent-message p-3 max-w-xs md:max-w-md">
                        <p>Hello! Welcome to StepaKash support. How can we help you today?</p>
                        <p class="text-xs text-text-light mt-2">10:32 AM</p>
                    </div>
                </div>
                
                <!-- Sample user message -->
                <div class="flex justify-end">
                    <div class="user-message p-3 max-w-xs md:max-w-md">
                        <p>Hi, I'm having trouble withdrawing to my Deriv account</p>
                        <p class="text-xs text-text-light mt-2">10:33 AM</p>
                    </div>
                </div>
                
                <!-- Sample agent reply -->
                <div class="flex justify-start">
                    <div class="agent-message p-3 max-w-xs md:max-w-md">
                        <p>I'm sorry to hear that. Let me help you with that. Could you please tell me what error message you're seeing?</p>
                        <p class="text-xs text-text-light mt-2">10:34 AM</p>
                    </div>
                </div>
                
                <!-- Typing indicator -->
                <div class="flex justify-start">
                    <div class="agent-message p-3 max-w-xs md:max-w-md typing-indicator">
                        <p>Agent is typing</p>
                    </div>
                </div>
            </div>
            
            <!-- Chat Input -->
            <div class="border-t border-gray-200 p-3">
                <div class="flex items-center space-x-2">
                    <input type="text" placeholder="Type your message..." class="flex-1 border border-gray-300 rounded-full py-2 px-4 focus:outline-none focus:ring-1 focus:ring-primary">
                    <button class="bg-primary hover:bg-primary-600 text-white p-2 rounded-full transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    @include('partials.footer')

    <script>
        // Toggle FAQ items
        document.querySelectorAll('.faq-item').forEach(item => {
            item.addEventListener('click', () => {
                item.classList.toggle('active');
                const icon = item.querySelector('svg');
                icon.classList.toggle('rotate-180');
            });
        });
        
        // Live Chat Modal
        const chatModal = document.getElementById('chatModal');
        const liveChatBtn = document.getElementById('liveChatBtn');
        const closeChatBtn = document.getElementById('closeChatBtn');
        
        liveChatBtn.addEventListener('click', () => {
            chatModal.classList.remove('hidden');
        });
        
        closeChatBtn.addEventListener('click', () => {
            chatModal.classList.add('hidden');
        });
        
        // Close modal when clicking outside
        chatModal.addEventListener('click', (e) => {
            if (e.target === chatModal) {
                chatModal.classList.add('hidden');
            }
        });
    </script>
</body>
</html>