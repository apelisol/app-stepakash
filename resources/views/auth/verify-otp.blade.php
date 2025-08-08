@extends('layouts.app')

@section('title', 'OTP Verification | StepaKash')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-background via-primary-50 to-secondary-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl w-full mx-4">
        <div class="lg:flex rounded-2xl overflow-hidden">
            <!-- Left Feature Panel -->
            <div class="hidden lg:block lg:w-1/2 bg-gradient-to-br from-primary to-secondary text-white p-8 lg:p-12 xl:p-16">
                <div class="max-w-md mx-auto lg:mx-0 h-full flex flex-col justify-center">
                    <div class="animate-fade-in">
                        <h2 class="text-2xl xl:text-3xl 2xl:text-4xl font-bold mb-4 lg:mb-6 leading-tight">
                            Secure Verification
                        </h2>
                        <p class="text-lg xl:text-xl mb-6 lg:mb-8 text-primary-100 leading-relaxed">
                            We've sent a 6-digit verification code to your registered phone number.
                        </p>
                        
                        <ul class="space-y-4 lg:space-y-6">
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 bg-white/20 rounded-lg flex items-center justify-center mr-3 lg:mr-4 group-hover:bg-white/30 transition-colors duration-200">
                                    <i class="fas fa-shield-alt text-lg lg:text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base lg:text-lg mb-1">Secure Authentication</h3>
                                    <p class="text-primary-100 text-xs lg:text-sm">One-time code for your protection</p>
                                </div>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 bg-white/20 rounded-lg flex items-center justify-center mr-3 lg:mr-4 group-hover:bg-white/30 transition-colors duration-200">
                                    <i class="fas fa-clock text-lg lg:text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base lg:text-lg mb-1">Time-Sensitive</h3>
                                    <p class="text-primary-100 text-xs lg:text-sm">Code expires in 10 minutes</p>
                                </div>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 bg-white/20 rounded-lg flex items-center justify-center mr-3 lg:mr-4 group-hover:bg-white/30 transition-colors duration-200">
                                    <i class="fas fa-mobile-alt text-lg lg:text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base lg:text-lg mb-1">Instant Delivery</h3>
                                    <p class="text-primary-100 text-xs lg:text-sm">Sent directly to your phone</p>
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
                        <h2 class="text-xl sm:text-2xl font-bold text-text mb-1 sm:mb-2">OTP Verification</h2>
                        <p class="text-text-light text-base sm:text-lg">Enter the 6-digit code sent to your {{ session('channel') === 'email' ? 'email' : 'phone' }}</p>
                    </div>

                    @if(session('error'))
                    <div class="mb-4 sm:mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg relative animate-slide-up" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                            <div>
                                <p class="font-medium text-red-800">Error</p>
                                <p class="text-sm text-red-600">{{ session('error') }}</p>
                            </div>
                            <button type="button" class="ml-auto text-red-500 hover:text-red-700 transition-colors duration-200" onclick="this.parentElement.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    @endif
                    
                    @if(session('success'))
                    <div class="mb-4 sm:mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg relative animate-slide-up" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <div>
                                <p class="font-medium text-green-800">Success</p>
                                <p class="text-sm text-green-600">{{ session('success') }}</p>
                            </div>
                            <button type="button" class="ml-auto text-green-500 hover:text-green-700 transition-colors duration-200" onclick="this.parentElement.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    @endif
                    
                    @if(session('message'))
                    <div class="mb-4 sm:mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg relative animate-slide-up" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            <p class="text-sm text-blue-700">{{ session('message') }}</p>
                            <button type="button" class="ml-auto text-blue-500 hover:text-blue-700 transition-colors duration-200" onclick="this.parentElement.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- OTP Form -->
                    <form action="{{ route('password.verify') }}" method="POST" id="otpForm" class="space-y-4 sm:space-y-6">
                        @csrf
                        <div>
                            <label for="otp" class="block text-sm font-medium text-text mb-1 sm:mb-2">
                                Verification Code
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-shield-alt text-text-light text-sm sm:text-base"></i>
                                </div>
                                <input type="text" 
                                       name="otp" 
                                       id="otp" 
                                       class="block w-full pl-10 pr-3 py-2 sm:py-3 border border-gray-300 rounded-xl placeholder-text-light focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-sm sm:text-base otp-input"
                                       placeholder="Enter 6-digit code" 
                                       inputmode="numeric"
                                       pattern="[0-9]*"
                                       maxlength="6"
                                       autocomplete="one-time-code"
                                       required>
                            </div>
                            <div id="otpError" class="mt-1 text-xs sm:text-sm text-danger hidden"></div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div id="countdown" class="text-sm text-text-light">
                                @if(session('otp_resent'))
                                    <span class="text-green-600">New code sent!</span>
                                @else
                                    Resend code in <span id="timer">01:00</span>
                                @endif
                            </div>
                            <button type="button" id="resendBtn" class="text-sm text-primary hover:text-primary-600 font-medium transition-colors duration-200 {{ session('otp_resent') ? 'hidden' : '' }}">
                                Resend Code
                            </button>
                        </div>

                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-primary to-secondary hover:from-primary-600 hover:to-secondary-600 text-white font-semibold py-3 sm:py-4 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg flex items-center justify-center group" 
                                id="verifyBtn">
                            <span class="spinner hidden animate-spin rounded-full h-4 w-4 sm:h-5 sm:w-5 border-b-2 border-white mr-2 sm:mr-3"></span>
                            <span class="btn-text text-sm sm:text-base">Verify Code</span>
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

/* OTP input specific styling */
.otp-input {
    letter-spacing: 0.5em;
    font-family: monospace;
    font-size: 1.25rem;
    text-align: center;
    padding-left: 0.5rem !important;
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
    
    .otp-input {
        font-size: 1rem;
        letter-spacing: 0.3em;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const otpForm = document.getElementById('otpForm');
    const otpInput = document.getElementById('otp');
    const otpError = document.getElementById('otpError');
    const verifyBtn = document.getElementById('verifyBtn');
    const resendBtn = document.getElementById('resendBtn');
    const countdown = document.getElementById('countdown');
    const timer = document.getElementById('timer');

    function startTimer(duration, display) {
        let timer = duration, minutes, seconds;
        const timerInterval = setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = minutes + ":" + seconds;

            if (--timer < 0) {
                clearInterval(timerInterval);
                const resendBtn = document.getElementById('resendBtn');
                const countdown = document.getElementById('countdown');
                if (resendBtn) resendBtn.classList.remove('hidden');
                if (countdown) countdown.textContent = 'Didn\'t receive a code?';
            }
        }, 1000);
    }

    startTimer(60, timer);

    // Resend OTP functionality
    resendBtn.addEventListener('click', function() {
        // Show loading state
        resendBtn.disabled = true;
        resendBtn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span> Sending...';
        
        // Here you would typically make an AJAX call to resend the OTP
        // For demonstration, we'll just reset the timer
        setTimeout(function() {
            startTimer(60, timer);
            resendBtn.classList.add('hidden');
            resendBtn.disabled = false;
            resendBtn.textContent = 'Resend Code';
            
            // Show success message
            showToast('New verification code sent successfully');
        }, 1000);
    });

    // Form submission handler
    otpForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btnText = verifyBtn.querySelector('.btn-text');
        const spinner = verifyBtn.querySelector('.spinner');
        const otpValue = otpInput.value.trim();
        
        // Basic validation
        if (otpValue.length !== 6) {
            showError('Please enter a valid 6-digit OTP code');
            otpInput.focus();
            return;
        }
        
        // Show loading state
        btnText.textContent = 'Verifying...';
        spinner.classList.remove('hidden');
        verifyBtn.disabled = true;
        
        // Submit the form
        this.submit();
    });
    
    function showError(message) {
        // Remove any existing error messages
        const existingError = document.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Create and show new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message mt-2 text-sm text-red-600 flex items-start';
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-circle mt-0.5 mr-1.5"></i>
            <span>${message}</span>
        `;
        
        const otpContainer = otpInput.closest('div');
        otpContainer.appendChild(errorDiv);
        
        // Add error styling to input
        otpInput.classList.remove('border-gray-300', 'border-green-500');
        otpInput.classList.add('border-red-500');
        
        // Scroll to error
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (errorDiv.parentNode) errorDiv.remove();
        }, 5000);
    }

    // Auto submit form when all OTP digits are entered
    otpInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Update UI to show validation state
        if (this.value.length === 6) {
            this.classList.remove('border-gray-300', 'border-red-500');
            this.classList.add('border-green-500');
            
            // Small delay to show the green border before submission
            setTimeout(() => {
                verifyBtn.click();
            }, 200);
        } else if (this.value.length > 0) {
            this.classList.remove('border-gray-300', 'border-green-500');
            this.classList.add('border-red-500');
        } else {
            this.classList.remove('border-red-500', 'border-green-500');
            this.classList.add('border-gray-300');
        }
    });

    // Auto-focus OTP input on page load
    otpInput.focus();

    // Helper function to show toast messages
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 z-50 max-w-xs sm:max-w-sm bg-white border-l-4 border-primary shadow-lg rounded-lg animate-slide-up';
        toast.innerHTML = `
            <div class="p-3 sm:p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-primary text-lg sm:text-xl"></i>
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