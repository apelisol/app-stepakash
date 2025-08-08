@extends('layouts.app')

@section('title', 'Forgot Password | StepaKash')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-background via-primary-50 to-secondary-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl w-full mx-4">
        <div class="lg:flex rounded-2xl overflow-hidden">
            <!-- Left Feature Panel -->
            <div class="hidden lg:block lg:w-1/2 bg-gradient-to-br from-primary to-secondary text-white p-8 lg:p-12 xl:p-16">
                <div class="max-w-md mx-auto lg:mx-0 h-full flex flex-col justify-center">
                    <div class="animate-fade-in">
                        <h2 class="text-2xl xl:text-3xl 2xl:text-4xl font-bold mb-4 lg:mb-6 leading-tight">
                            Reset Your Password
                        </h2>
                        <p class="text-lg xl:text-xl mb-6 lg:mb-8 text-primary-100 leading-relaxed">
                            Secure and quick password recovery for your StepaKash account.
                        </p>
                        
                        <ul class="space-y-4 lg:space-y-6">
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 bg-white/20 rounded-lg flex items-center justify-center mr-3 lg:mr-4 group-hover:bg-white/30 transition-colors duration-200">
                                    <i class="fas fa-shield-alt text-lg lg:text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base lg:text-lg mb-1">Secure Process</h3>
                                    <p class="text-primary-100 text-xs lg:text-sm">Encrypted password reset</p>
                                </div>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 bg-white/20 rounded-lg flex items-center justify-center mr-3 lg:mr-4 group-hover:bg-white/30 transition-colors duration-200">
                                    <i class="fas fa-envelope text-lg lg:text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base lg:text-lg mb-1">Email Recovery</h3>
                                    <p class="text-primary-100 text-xs lg:text-sm">Reset link sent to your email</p>
                                </div>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 bg-white/20 rounded-lg flex items-center justify-center mr-3 lg:mr-4 group-hover:bg-white/30 transition-colors duration-200">
                                    <i class="fas fa-mobile-alt text-lg lg:text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base lg:text-lg mb-1">SMS Recovery</h3>
                                    <p class="text-primary-100 text-xs lg:text-sm">Reset code sent to your phone</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Form Container -->
            <div class="w-full lg:w-1/2 p-6 sm:p-8 md:p-10 lg:p-12 xl:p-16 bg-white/90 backdrop-blur-sm">
                <div class="max-w-md mx-auto w-full">
                    <!-- Logo -->
                    <div class="text-center mb-6 sm:mb-8">
                        <img src="{{ asset('assets/img/stepakash-money-on-the-go.png') }}" 
                             alt="STEPAKASH Logo" 
                             class="mx-auto h-14 sm:h-16 w-auto mb-4 sm:mb-6 animate-pulse-slow" />
                        <h2 class="text-xl sm:text-2xl font-bold text-text mb-1 sm:mb-2">Forgot Password?</h2>
                        <p class="text-text-light text-base sm:text-lg">Enter your email or phone number to reset your password</p>
                    </div>

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

                    @if(session('status'))
                        <div class="mb-4 sm:mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg relative animate-slide-up" role="alert">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span class="flex-1 text-sm">{{ session('status') }}</span>
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

                    <!-- Forgot Password Form -->
                    <form action="{{ route('password.email') }}" method="POST" id="forgotPasswordForm" class="space-y-4 sm:space-y-6">
                        @csrf
                        <div>
                            <label for="email" class="block text-sm font-medium text-text mb-1 sm:mb-2">
                                Email Address
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       class="block w-full pl-10 pr-3 py-3 border {{ $errors->has('email') ? 'border-red-300' : 'border-gray-200' }} rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-text"
                                       placeholder="Enter your email address" 
                                       value="{{ old('email') }}"
                                       autocomplete="email" 
                                       required>
                            </div>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-primary to-secondary hover:from-primary-600 hover:to-secondary-600 text-white font-semibold py-3 sm:py-4 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg flex items-center justify-center group" 
                                id="resetBtn">
                            <span class="spinner hidden animate-spin rounded-full h-4 w-4 sm:h-5 sm:w-5 border-b-2 border-white mr-2 sm:mr-3"></span>
                            <span class="btn-text text-sm sm:text-base">Send Reset Link</span>
                        </button>
                    </form>

                    <!-- Back to Login Link -->
                    <div class="text-center mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-100">
                        <a href="{{ route('login') }}" 
                           class="text-primary hover:text-primary-600 font-medium transition-colors duration-200 flex items-center justify-center group text-sm sm:text-base">
                            <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform duration-200"></i>
                            Back to Login
                        </a>
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
@endsection

@push('scripts')
<style>
/* Animations */
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

/* Form styling */
.bg-white\/90 {
    background-color: rgba(255, 255, 255, 0.9);
}

.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}

/* Input validation states */
.is-invalid {
    border-color: #ef4444 !important;
    background-color: rgba(239, 68, 68, 0.05);
}

.is-valid {
    border-color: #10b981 !important;
    background-color: rgba(16, 185, 129, 0.05);
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
    const form = document.getElementById('forgotPasswordForm');
    const identifierInput = document.getElementById('identifier');
    const identifierError = document.getElementById('identifierError');
    const resetBtn = document.getElementById('resetBtn');

    // Form submission handler
    form.addEventListener('submit', function(e) {
        let isValid = true;
        const value = identifierInput.value.trim();

        // Validate identifier (email or phone)
        if (!value) {
            identifierError.textContent = 'Email or phone number is required';
            identifierError.classList.remove('hidden');
            identifierInput.classList.add('is-invalid');
            isValid = false;
        } else if (!isValidEmail(value) && !isValidPhone(value)) {
            identifierError.textContent = 'Please enter a valid email or phone number';
            identifierError.classList.remove('hidden');
            identifierInput.classList.add('is-invalid');
            isValid = false;
        } else {
            identifierError.textContent = '';
            identifierError.classList.add('hidden');
            identifierInput.classList.remove('is-invalid');
            identifierInput.classList.add('is-valid');
        }

        if (isValid) {
            // Show loading state
            resetBtn.disabled = true;
            const spinner = resetBtn.querySelector('.spinner');
            const btnText = resetBtn.querySelector('.btn-text');
            
            if (spinner) spinner.classList.remove('hidden');
            if (btnText) btnText.textContent = 'Sending Reset Link...';
        } else {
            e.preventDefault();
        }
    });

    // Real-time identifier validation
    identifierInput.addEventListener('input', function() {
        const value = this.value.trim();

        if (value.length === 0) {
            identifierError.textContent = '';
            identifierError.classList.add('hidden');
            this.classList.remove('is-invalid', 'is-valid');
        } else if (!isValidEmail(value) && !isValidPhone(value)) {
            identifierError.textContent = 'Please enter a valid email or phone number';
            identifierError.classList.remove('hidden');
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
        } else {
            identifierError.textContent = '';
            identifierError.classList.add('hidden');
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });

    // Helper functions for validation
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function isValidPhone(phone) {
        const re = /^[0-9]{9,12}$/;
        return re.test(phone);
    }

    // Format phone number input (remove non-numeric chars)
    identifierInput.addEventListener('input', function(e) {
        // Only format if it looks like a phone number (starts with digit)
        if (/^\d/.test(e.target.value)) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 12) {
                value = value.slice(0, 12);
            }
            e.target.value = value;
        }
    });

    // Reset button state when page loads
    if (resetBtn) {
        resetBtn.disabled = false;
        const spinner = resetBtn.querySelector('.spinner');
        const btnText = resetBtn.querySelector('.btn-text');
        
        if (spinner) spinner.classList.add('hidden');
        if (btnText) btnText.textContent = 'Send Reset Link';
    }
});
</script>
@endpush