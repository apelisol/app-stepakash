@extends('layouts.app')

@section('title', 'Reset Password | StepaKash')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-background via-primary-50 to-secondary-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl w-full mx-4">
        <div class="lg:flex rounded-2xl overflow-hidden">
            <!-- Left Feature Panel -->
            <div class="hidden lg:block lg:w-1/2 bg-gradient-to-br from-primary to-secondary text-white p-8 lg:p-12 xl:p-16">
                <div class="max-w-md mx-auto lg:mx-0 h-full flex flex-col justify-center">
                    <div class="animate-fade-in">
                        <h2 class="text-2xl xl:text-3xl 2xl:text-4xl font-bold mb-4 lg:mb-6 leading-tight">
                            Secure Password Reset
                        </h2>
                        <p class="text-lg xl:text-xl mb-6 lg:mb-8 text-primary-100 leading-relaxed">
                            Create a strong new password to protect your StepaKash account.
                        </p>
                        
                        <ul class="space-y-4 lg:space-y-6">
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 bg-white/20 rounded-lg flex items-center justify-center mr-3 lg:mr-4 group-hover:bg-white/30 transition-colors duration-200">
                                    <i class="fas fa-lock text-lg lg:text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base lg:text-lg mb-1">Strong Protection</h3>
                                    <p class="text-primary-100 text-xs lg:text-sm">Minimum 8 characters with complexity</p>
                                </div>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 bg-white/20 rounded-lg flex items-center justify-center mr-3 lg:mr-4 group-hover:bg-white/30 transition-colors duration-200">
                                    <i class="fas fa-sync-alt text-lg lg:text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base lg:text-lg mb-1">One-Time Reset</h3>
                                    <p class="text-primary-100 text-xs lg:text-sm">Previous passwords can't be reused</p>
                                </div>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 bg-white/20 rounded-lg flex items-center justify-center mr-3 lg:mr-4 group-hover:bg-white/30 transition-colors duration-200">
                                    <i class="fas fa-shield-alt text-lg lg:text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base lg:text-lg mb-1">Instant Security</h3>
                                    <p class="text-primary-100 text-xs lg:text-sm">All devices will be logged out</p>
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
                        <h2 class="text-xl sm:text-2xl font-bold text-text mb-1 sm:mb-2">Reset Password</h2>
                        <p class="text-text-light text-base sm:text-lg">Create a new password for your account</p>
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
                    
                    @if($errors->any())
                        <div class="mb-4 sm:mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg relative animate-slide-up" role="alert">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                                <div>
                                    <p class="font-medium text-red-800">Please fix the following errors:</p>
                                    <ul class="list-disc list-inside text-sm text-red-600 mt-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <button type="button" class="ml-auto text-red-500 hover:text-red-700 transition-colors duration-200" onclick="this.parentElement.parentElement.remove()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Reset Password Form -->
                    <form action="{{ route('password.update') }}" method="POST" id="resetForm" class="space-y-4 sm:space-y-6">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token ?? session('reset_token') }}">
                        <input type="hidden" name="email" value="{{ $email ?? session('reset_email') }}">

                        <div>
                            <label for="password" class="block text-sm font-medium text-text mb-1 sm:mb-2">
                                New Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-text-light text-sm sm:text-base"></i>
                                </div>
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       class="block w-full pl-10 pr-10 py-2 sm:py-3 border border-gray-300 rounded-xl placeholder-text-light focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-sm sm:text-base"
                                       placeholder="Enter new password" 
                                       minlength="8"
                                       required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <button type="button" 
                                            id="togglePassword" 
                                            class="text-text-light hover:text-text transition-colors duration-200">
                                        <i class="fas fa-eye text-sm sm:text-base"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="passwordError" class="mt-1 text-xs sm:text-sm text-danger hidden"></div>
                            <div class="password-strength mt-2 hidden" id="passwordStrength">
                                <div class="flex items-center space-x-2">
                                    <div class="h-1 w-full bg-gray-200 rounded-full overflow-hidden">
                                        <div id="strengthBar" class="h-full transition-all duration-300"></div>
                                    </div>
                                    <span id="strengthText" class="text-xs font-medium"></span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="confirmpassword" class="block text-sm font-medium text-text mb-1 sm:mb-2">
                                Confirm Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-text-light text-sm sm:text-base"></i>
                                </div>
                                <input type="password" 
                                       name="confirmpassword" 
                                       id="confirmpassword" 
                                       class="block w-full pl-10 pr-10 py-2 sm:py-3 border border-gray-300 rounded-xl placeholder-text-light focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-sm sm:text-base"
                                       placeholder="Confirm your password" 
                                       minlength="8"
                                       required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <button type="button" 
                                            id="toggleConfirmPassword" 
                                            class="text-text-light hover:text-text transition-colors duration-200">
                                        <i class="fas fa-eye text-sm sm:text-base"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="confirmError" class="mt-1 text-xs sm:text-sm text-danger hidden"></div>
                        </div>

                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-primary to-secondary hover:from-primary-600 hover:to-secondary-600 text-white font-semibold py-3 sm:py-4 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg flex items-center justify-center group" 
                                id="resetBtn">
                            <span class="spinner hidden animate-spin rounded-full h-4 w-4 sm:h-5 sm:w-5 border-b-2 border-white mr-2 sm:mr-3"></span>
                            <span class="btn-text text-sm sm:text-base">Update Password</span>
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

/* Password strength meter */
.password-strength {
    transition: all 0.3s ease;
}

#strengthBar {
    width: 0%;
    background-color: #ef4444; /* Starts red by default */
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
    const resetForm = document.getElementById('resetForm');
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirmpassword');
    const passwordError = document.getElementById('passwordError');
    const confirmError = document.getElementById('confirmError');
    const resetBtn = document.getElementById('resetBtn');
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const passwordStrength = document.getElementById('passwordStrength');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
        
        // Update title for accessibility
        this.setAttribute('title', type === 'password' ? 'Show password' : 'Hide password');
    });

    toggleConfirmPassword.addEventListener('click', function() {
        const type = confirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmInput.setAttribute('type', type);
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
        
        // Update title for accessibility
        this.setAttribute('title', type === 'password' ? 'Show password' : 'Hide password');
    });
    
    // Show error message and highlight field
    function showError(message, errorElement, inputElement) {
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
        inputElement.classList.add('border-red-500');
        inputElement.focus();
    }

    // Real-time password strength check
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        const strengthMeter = document.getElementById('passwordStrength');
        
        if (password.length === 0) {
            strengthMeter.classList.add('hidden');
            return;
        }
        
        strengthMeter.classList.remove('hidden');
        
        // Reset password error if user starts typing
        if (passwordError && !passwordError.classList.contains('hidden')) {
            passwordError.textContent = '';
            passwordError.classList.add('hidden');
            passwordInput.classList.remove('border-red-500');
        }
        
        // Calculate password strength (0-100)
        let strength = 0;
        const hasLower = /[a-z]/.test(password);
        const hasUpper = /[A-Z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecial = /[^A-Za-z0-9]/.test(password);
        
        // Length check (max 25 points for length)
        strength += Math.min(25, (password.length * 2));
        
        // Complexity checks
        if (hasLower) strength += 15;
        if (hasUpper) strength += 15;
        if (hasNumber) strength += 15;
        if (hasSpecial) strength += 20;
        
        // Cap at 100
        strength = Math.min(strength, 100);
        
        // Update strength bar and text
        strengthBar.style.width = strength + '%';
        
        if (strength < 40) {
            strengthBar.className = 'h-full transition-all duration-300 bg-red-500';
            strengthText.textContent = 'Weak';
            strengthText.className = 'text-xs font-medium text-red-600';
        } else if (strength < 70) {
            strengthBar.className = 'h-full transition-all duration-300 bg-yellow-500';
            strengthText.textContent = 'Moderate';
            strengthText.className = 'text-xs font-medium text-yellow-600';
        } else {
            strengthBar.className = 'h-full transition-all duration-300 bg-green-500';
            strengthText.textContent = 'Strong';
            strengthText.className = 'text-xs font-medium text-green-600';
        }
    });
    
    // Show password requirements on focus
    passwordInput.addEventListener('focus', function() {
        const requirements = document.createElement('div');
        requirements.id = 'password-requirements';
        requirements.className = 'mt-2 p-3 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-600';
        requirements.innerHTML = `
            <p class="font-medium mb-2">Password must include:</p>
            <ul class="space-y-1">
                <li class="flex items-center" id="req-length">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span>At least 8 characters</span>
                </li>
                <li class="flex items-center" id="req-lower">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span>One lowercase letter</span>
                </li>
                <li class="flex items-center" id="req-upper">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span>One uppercase letter</span>
                </li>
                <li class="flex items-center" id="req-number">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span>One number</span>
                </li>
                <li class="flex items-center" id="req-special">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span>One special character</span>
                </li>
            </ul>
        `;
        
        // Only add if not already added
        if (!document.getElementById('password-requirements')) {
            this.parentNode.insertBefore(requirements, this.nextSibling);
            
            // Update requirements in real-time
            this.addEventListener('input', updatePasswordRequirements);
        }
    });
    
    // Update password requirements in real-time
    function updatePasswordRequirements() {
        const password = this.value;
        const reqs = {
            length: document.getElementById('req-length'),
            lower: document.getElementById('req-lower'),
            upper: document.getElementById('req-upper'),
            number: document.getElementById('req-number'),
            special: document.getElementById('req-special')
        };
        
        // Check each requirement
        const checks = {
            length: password.length >= 8,
            lower: /[a-z]/.test(password),
            upper: /[A-Z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[^A-Za-z0-9]/.test(password)
        };
        
        // Update UI for each requirement
        Object.entries(checks).forEach(([key, isValid]) => {
            if (reqs[key]) {
                const icon = reqs[key].querySelector('i');
                if (isValid) {
                    icon.className = 'fas fa-check-circle text-green-500 mr-2';
                    reqs[key].classList.remove('text-gray-400');
                    reqs[key].classList.add('text-gray-700');
                } else {
                    icon.className = 'far fa-circle text-gray-400 mr-2';
                    reqs[key].classList.add('text-gray-400');
                    reqs[key].classList.remove('text-gray-700');
                }
            }
        });
    }
    
    // Remove requirements on blur if input is empty
    passwordInput.addEventListener('blur', function(e) {
        if (this.value === '') {
            const requirements = document.getElementById('password-requirements');
            if (requirements) {
                requirements.remove();
                // Remove the input event listener
                this.removeEventListener('input', updatePasswordRequirements);
            }
        }
    });

    // Form submission handler
    resetForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const password = passwordInput.value.trim();
        const confirmPassword = confirmInput.value.trim();
        let isValid = true;

        // Reset error states
        passwordError.textContent = '';
        passwordError.classList.add('hidden');
        confirmError.textContent = '';
        confirmError.classList.add('hidden');
        passwordInput.classList.remove('border-red-500', 'border-green-500');
        confirmInput.classList.remove('border-red-500', 'border-green-500');

        // Validate password
        if (!password) {
            showError('Please enter a new password', passwordError, passwordInput);
            isValid = false;
        } else if (password.length < 8) {
            showError('Password must be at least 8 characters', passwordError, passwordInput);
            isValid = false;
        } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}/.test(password)) {
            showError('Password must include uppercase, lowercase, number, and special character', passwordError, passwordInput);
            isValid = false;
        } else {
            passwordInput.classList.add('border-green-500');
        }

        // Validate confirm password
        if (!confirmPassword) {
            showError('Please confirm your password', confirmError, confirmInput);
            isValid = false;
        } else if (password !== confirmPassword) {
            showError('Passwords do not match', confirmError, confirmInput);
            isValid = false;
        } else if (confirmPassword) {
            confirmInput.classList.add('border-green-500');
        }

        if (!isValid) {
            // Scroll to first error
            const firstError = document.querySelector('.border-red-500');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
            return;
        }

        // Show loading state
        const btnText = resetBtn.querySelector('.btn-text');
        const spinner = resetBtn.querySelector('.spinner');
        
        if (btnText) btnText.textContent = 'Updating...';
        if (spinner) spinner.classList.remove('hidden');
        resetBtn.disabled = true;
        
        // Submit the form
        this.submit();
    });

    // Helper function to show toast messages
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        const iconColor = type === 'success' ? 'text-primary' : 'text-danger';
        const borderColor = type === 'success' ? 'border-primary' : 'border-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        toast.className = `fixed top-4 right-4 z-50 max-w-xs sm:max-w-sm bg-white border-l-4 ${borderColor} shadow-lg rounded-lg animate-slide-up`;
        toast.innerHTML = `
            <div class="p-3 sm:p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas ${icon} ${iconColor} text-lg sm:text-xl"></i>
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