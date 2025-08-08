@extends('layouts.app')

@section('page', 'signin')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-background via-primary-50 to-secondary-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl w-full mx-4">
        <!-- Removed card container for cleaner look -->
        <div class="lg:flex rounded-2xl overflow-hidden">
            <!-- Left Feature Panel - Hidden on mobile -->
            <div class="hidden lg:block lg:w-1/2 bg-gradient-to-br from-primary to-secondary text-white p-8 lg:p-12 xl:p-16">
                <div class="max-w-md mx-auto lg:mx-0 h-full flex flex-col justify-center">
                    <div class="animate-fade-in">
                        <h2 class="text-2xl xl:text-3xl 2xl:text-4xl font-bold mb-4 lg:mb-6 leading-tight">
                            Welcome Back to StepaKash
                        </h2>
                        <p class="text-lg xl:text-xl mb-6 lg:mb-8 text-primary-100 leading-relaxed">
                            Log in to manage your Deriv payments with M-Pesa — deposit or withdraw seamlessly.
                        </p>
                        
                        <ul class="space-y-4 lg:space-y-6">
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 bg-white/20 rounded-lg flex items-center justify-center mr-3 lg:mr-4 group-hover:bg-white/30 transition-colors duration-200">
                                    <i class="fas fa-shield-alt text-lg lg:text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base lg:text-lg mb-1">Secure M-Pesa Transactions</h3>
                                    <p class="text-primary-100 text-xs lg:text-sm">Encrypted and secure M-Pesa payments</p>
                                </div>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 bg-white/20 rounded-lg flex items-center justify-center mr-3 lg:mr-4 group-hover:bg-white/30 transition-colors duration-200">
                                    <i class="fas fa-bolt text-lg lg:text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base lg:text-lg mb-1">Instant Transfers</h3>
                                    <p class="text-primary-100 text-xs lg:text-sm">Instant deposits and withdrawals to/from Deriv</p>
                                </div>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 bg-white/20 rounded-lg flex items-center justify-center mr-3 lg:mr-4 group-hover:bg-white/30 transition-colors duration-200">
                                    <i class="fas fa-chart-line text-lg lg:text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base lg:text-lg mb-1">Transaction History</h3>
                                    <p class="text-primary-100 text-xs lg:text-sm">Track and manage your transaction history</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Right Form Container - Removed card styling -->
            <div class="w-full lg:w-1/2 p-6 sm:p-8 md:p-10 lg:p-12 xl:p-16 bg-white/90 backdrop-blur-sm">
                <div class="max-w-md mx-auto w-full">
                    <!-- Logo -->
                    <div class="text-center mb-6 sm:mb-8">
                        <img src="{{ asset('assets/img/stepakash-money-on-the-go.png') }}" 
                             alt="STEPAKASH Logo" 
                             class="mx-auto h-14 sm:h-16 w-auto mb-4 sm:mb-6 animate-pulse-slow" />
                        <h4 class="text-xl sm:text-2xl font-bold text-text mb-1 sm:mb-2">Welcome Back</h4>
                        <p class="text-text-light text-base sm:text-lg">Sign in to your StepaKash account</p>
                    </div>

                    <!-- Tab Switcher - Updated design -->
                    <div class="flex mb-6 sm:mb-8 bg-gray-100 rounded-xl p-1">
                        <div class="flex-1 text-center py-2 px-2 sm:px-4 bg-gradient-to-r from-primary to-secondary text-white font-semibold rounded-lg text-sm sm:text-base">
                            Login
                        </div>
                        <a href="{{ route('auth.deriv') }}" class="flex-1 text-center py-2 px-2 sm:px-4 text-text hover:text-primary transition-colors duration-200 rounded-lg text-sm sm:text-base">
                            Create Account
                        </a>
                    </div>
                    
                    <!-- Alert Messages -->
                    @if(session('error'))
                        <div class="mb-4 sm:mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg relative animate-slide-up" role="alert">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <span class="flex-1 text-sm">{{ session('error') }}</span>
                                <button type="button" class="ml-2 text-red-600 hover:text-red-800 transition-colors duration-200" onclick="this.parentElement.parentElement.remove()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="mb-4 sm:mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg relative animate-slide-up" role="alert">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span class="flex-1 text-sm">{{ session('success') }}</span>
                                <button type="button" class="ml-2 text-green-600 hover:text-green-800 transition-colors duration-200" onclick="this.parentElement.parentElement.remove()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 sm:mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg relative animate-slide-up" role="alert">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-circle mt-0.5 mr-2"></i>
                                <div>
                                    @foreach($errors->all() as $error)
                                        <p class="text-sm mb-1">{{ $error }}</p>
                                    @endforeach
                                </div>
                                <button type="button" class="ml-2 text-red-600 hover:text-red-800 transition-colors duration-200" onclick="this.parentElement.parentElement.remove()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Loading Bars Container -->
                    <div id="loadingBars" class="mb-4 sm:mb-6 hidden">
                        <div class="space-y-3 p-4 bg-primary/5 rounded-xl border border-primary/10">
                            <div class="text-center mb-2 sm:mb-4">
                                <p class="text-sm font-medium text-primary">Connecting to Deriv</p>
                                <p class="text-xs text-text-light">Please wait while we establish connection...</p>
                            </div>
                            
                            <!-- Main Progress Bar -->
                            <div class="relative">
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div id="mainProgressBar" class="bg-gradient-to-r from-primary to-secondary h-full rounded-full transition-all duration-300 ease-out" style="width: 0%"></div>
                                </div>
                            </div>
                            
                            <!-- Secondary Loading Bars -->
                            <div class="grid grid-cols-3 gap-2">
                                <div class="bg-gray-200 rounded-full h-1 overflow-hidden">
                                    <div class="loading-bar-1 bg-primary/70 h-full rounded-full"></div>
                                </div>
                                <div class="bg-gray-200 rounded-full h-1 overflow-hidden">
                                    <div class="loading-bar-2 bg-secondary/70 h-full rounded-full"></div>
                                </div>
                                <div class="bg-gray-200 rounded-full h-1 overflow-hidden">
                                    <div class="loading-bar-3 bg-primary/70 h-full rounded-full"></div>
                                </div>
                            </div>
                            
                            <!-- Status Text -->
                            <div class="text-center">
                                <p id="loadingStatus" class="text-xs text-text-light">Initializing connection...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Login Form -->
                    <div class="space-y-4 sm:space-y-6">
                        <form action="{{ route('login') }}" method="POST" id="loginFormSubmit" class="space-y-4 sm:space-y-6">
                            @csrf
                            <div>
                                <label for="loginPhone" class="block text-sm font-medium text-text mb-1 sm:mb-2">
                                    Phone Number
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-gray-400"></i>
                                    </div>
                                    <input type="tel" id="loginPhone" name="phone" 
                                           class="block w-full pl-10 pr-3 py-3 border {{ $errors->has('phone') ? 'border-red-300' : 'border-gray-200' }} rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-text"
                                           placeholder="e.g. 0712345678" required
                                           value="{{ old('phone') }}">
                                </div>
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1 sm:mb-2">
                                    <label for="loginpassword" class="block text-sm font-medium text-text">
                                        Password
                                    </label>
                                    <a href="{{ route('password.request') }}" class="text-xs font-medium text-primary hover:text-primary-700 transition-colors duration-200">
                                        Forgot Password?
                                    </a>
                                </div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                    <input type="password" 
                                           name="password" 
                                           id="loginpassword" 
                                           class="block w-full pl-10 pr-10 py-3 border {{ $errors->has('password') ? 'border-red-300' : 'border-gray-200' }} rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-text"
                                           placeholder="Enter your password" 
                                           autocomplete="current-password"
                                           minlength="4" 
                                           required>
                                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <i class="fas fa-eye text-gray-400 hover:text-primary transition-colors duration-200"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('login')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                                <div id="loginPasswordError" class="mt-1 text-xs sm:text-sm text-danger hidden"></div>
                            </div>

                            <br>

                            <div class="flex items-center justify-between">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           id="rememberMe" 
                                           class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="ml-2 text-xs sm:text-sm text-text-light">Remember me</span>
                                </label>
                                @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" 
                                   class="text-xs sm:text-sm text-primary hover:text-primary-600 transition-colors duration-200">
                                    Forgot password?
                                </a>
                                @endif
                            </div>

                            <br>

                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-primary to-secondary hover:from-primary-600 hover:to-secondary-600 text-white font-semibold py-3 sm:py-4 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg flex items-center justify-center group" 
                                    id="loginBtn">
                                <span class="spinner hidden animate-spin rounded-full h-4 w-4 sm:h-5 sm:w-5 border-b-2 border-white mr-2 sm:mr-3"></span>
                                <span class="btn-text text-sm sm:text-base">Sign In</span>
                            </button>
                        </form>

                        <!-- Divider -->
                        <div class="relative my-4 sm:my-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-xs sm:text-sm">
                                <span class="px-2 bg-white text-text-light">OR</span>
                            </div>
                        </div>

                        <!-- Deriv Signup Option -->
                        <a href="{{ route('auth.deriv') }}" 
                           class="block w-full bg-gradient-to-r from-secondary to-primary hover:from-secondary-600 hover:to-primary-600 text-white font-semibold py-3 sm:py-4 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg flex items-center justify-center group" 
                           id="derivSignupBtn">
                            <i class="fab fa-connectdevelop mr-2 sm:mr-3 text-lg sm:text-xl group-hover:rotate-12 transition-transform duration-200"></i>
                            <span class="text-sm sm:text-base">Sign up with Deriv</span>
                        </a>
                        
                        <p class="text-center text-xs sm:text-sm text-text-light leading-relaxed">
                            By connecting your Deriv account, you agree to our 
                            <a href="{{ route('terms') }}" class="text-primary hover:text-primary-600 transition-colors duration-200">Terms</a> 
                            and 
                            <a href="{{ route('privacy') }}" class="text-primary hover:text-primary-600 transition-colors duration-200">Privacy Policy</a>
                        </p>
                    </div>
                    
                    <!-- Footer -->
                    <div class="text-center mt-8 sm:mt-12 pt-6 sm:pt-8 border-t border-gray-100">
                        <p class="text-text-light text-xs sm:text-sm">
                            © {{ date('Y') }} StepaKash. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
/* Loading Bar Animations */
@keyframes loading-bar-slide {
    0% { transform: translateX(-100%); width: 0%; }
    50% { transform: translateX(0%); width: 100%; }
    100% { transform: translateX(100%); width: 0%; }
}

.loading-bar-1 {
    animation: loading-bar-slide 1.5s ease-in-out infinite;
    animation-delay: 0s;
}

.loading-bar-2 {
    animation: loading-bar-slide 1.5s ease-in-out infinite;
    animation-delay: 0.3s;
}

.loading-bar-3 {
    animation: loading-bar-slide 1.5s ease-in-out infinite;
    animation-delay: 0.6s;
}

/* Fade animations */
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slide-up {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}

.animate-slide-up {
    animation: slide-up 0.4s ease-out;
}

.animate-pulse-slow {
    animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Button loading state */
.btn-loading {
    position: relative;
    color: transparent !important;
}

.btn-loading .spinner {
    display: inline-block !important;
}

/* Improved form container */
.bg-white\/90 {
    background-color: rgba(255, 255, 255, 0.9);
}

.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .rounded-2xl {
        border-radius: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('loginpassword');
    const loginForm = document.getElementById('loginFormSubmit');
    const loginBtn = document.getElementById('loginBtn');

    // Password toggle functionality
    if (togglePassword && passwordField) {
        togglePassword.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            if (type === 'text') {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }

    // Form validation
    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + 'Error');
        
        if (field && errorDiv) {
            field.classList.add('border-danger', 'focus:ring-danger');
            field.classList.remove('border-gray-300', 'focus:ring-primary');
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
        }
    }

    function clearError(fieldId) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + 'Error');
        
        if (field && errorDiv) {
            field.classList.remove('border-danger', 'focus:ring-danger');
            field.classList.add('border-gray-300', 'focus:ring-primary');
            errorDiv.textContent = '';
            errorDiv.classList.add('hidden');
        }
    }

    // Login form validation
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const phoneInput = document.getElementById('loginPhone');
            const passwordInput = document.getElementById('loginpassword');
            let isValid = true;

            // Clear previous errors
            clearError('loginPhone');
            clearError('loginpassword');

            // Validate phone
            if (!phoneInput.value.trim()) {
                showError('loginPhone', 'Phone number is required');
                isValid = false;
            }

            // Validate password
            if (!passwordInput.value.trim()) {
                showError('loginpassword', 'Password is required');
                isValid = false;
            } else if (passwordInput.value.length < 4) {
                showError('loginpassword', 'Password must be at least 4 characters');
                isValid = false;
            }

            if (isValid && loginBtn) {
                // Show loading state
                loginBtn.classList.add('btn-loading');
                const spinner = loginBtn.querySelector('.spinner');
                const btnText = loginBtn.querySelector('.btn-text');
                
                if (spinner) spinner.classList.remove('hidden');
                if (btnText) btnText.textContent = 'Authenticating...';
            } else {
                e.preventDefault();
            }
        });
    }

    function showToastError(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 z-50 max-w-xs sm:max-w-sm bg-white border-l-4 border-danger shadow-lg rounded-lg animate-slide-up';
        toast.innerHTML = `
            <div class="p-3 sm:p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-danger text-lg sm:text-xl"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-text">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <button class="text-text-light hover:text-text transition-colors duration-200" onclick="this.closest('.fixed').remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            if (toast.parentNode) toast.remove();
        }, 5000);
    }
});
</script>
@endpush
@endsection