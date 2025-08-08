@extends('layouts.app')

@section('page', 'signin')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-background via-primary-50 to-secondary-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl w-full mx-4">
        <div class="lg:flex rounded-2xl overflow-hidden">
            <!-- Left Feature Panel - Hidden on mobile -->
            <div class="hidden lg:block lg:w-1/2 bg-gradient-to-br from-primary to-secondary text-white p-8 lg:p-12 xl:p-16">
                <div class="max-w-md mx-auto lg:mx-0 h-full flex flex-col justify-center">
                    <div class="animate-fade-in">
                        <h2 class="text-2xl xl:text-3xl 2xl:text-4xl font-bold mb-4 lg:mb-6 leading-tight">
                            Join StepaKash Today
                        </h2>
                        <p class="text-lg xl:text-xl mb-6 lg:mb-8 text-primary-100 leading-relaxed">
                            Complete your registration to start seamless M-Pesa transactions with your Deriv account.
                        </p>
                        
                        <ul class="space-y-4 lg:space-y-6">
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 bg-white/20 rounded-lg flex items-center justify-center mr-3 lg:mr-4 group-hover:bg-white/30 transition-colors duration-200">
                                    <i class="fas fa-check-circle text-lg lg:text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base lg:text-lg mb-1">Deriv Account Connected</h3>
                                    <p class="text-primary-100 text-xs lg:text-sm">Your Deriv credentials are securely linked</p>
                                </div>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 bg-white/20 rounded-lg flex items-center justify-center mr-3 lg:mr-4 group-hover:bg-white/30 transition-colors duration-200">
                                    <i class="fas fa-mobile-alt text-lg lg:text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base lg:text-lg mb-1">M-Pesa Integration</h3>
                                    <p class="text-primary-100 text-xs lg:text-sm">Link your phone number for instant transactions</p>
                                </div>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 bg-white/20 rounded-lg flex items-center justify-center mr-3 lg:mr-4 group-hover:bg-white/30 transition-colors duration-200">
                                    <i class="fas fa-shield-alt text-lg lg:text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base lg:text-lg mb-1">Secure Protection</h3>
                                    <p class="text-primary-100 text-xs lg:text-sm">Your data is encrypted and protected</p>
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
                        <h4 class="text-xl sm:text-2xl font-bold text-text mb-1 sm:mb-2">Complete Registration</h4>
                        <p class="text-text-light text-base sm:text-lg">Just a few more details to get started</p>
                    </div>

                    <!-- Tab Switcher -->
                    <div class="flex mb-6 sm:mb-8 bg-gray-100 rounded-xl p-1">
                        <a href="{{ route('login') }}" class="flex-1 text-center py-2 px-2 sm:px-4 text-text hover:text-primary transition-colors duration-200 rounded-lg text-sm sm:text-base">
                            Login
                        </a>
                        <div class="flex-1 text-center py-2 px-2 sm:px-4 bg-gradient-to-r from-primary to-secondary text-white font-semibold rounded-lg text-sm sm:text-base">
                            Register
                        </div>
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

                    <!-- Registration Form -->
                    <form action="{{ route('signup') }}" method="POST" id="registerFormSubmit" class="space-y-4 sm:space-y-6">
                        @csrf
                        <!-- Basic Account Information -->
                        <input type="hidden" name="deriv_account" id="derivAccount" value="1">
                        <input type="hidden" name="deriv_token" id="derivToken" value="{{ session('deriv_data.deriv_token') ?? '' }}">
                        <input type="hidden" name="deriv_email" id="derivEmail" value="{{ session('deriv_data.email') ?? '' }}">
                        <input type="hidden" name="account_number" id="account_number" value="{{ session('deriv_data.deriv_account_number') ?? '' }}">
                        <input type="hidden" name="deriv_login_id" id="derivLoginId" value="{{ session('deriv_data.deriv_login_id') ?? '' }}">
                        <input type="hidden" name="deriv_account_number" id="derivAccountNumber" value="{{ session('deriv_data.deriv_account_number') ?? '' }}">
                        <input type="hidden" name="deriv_user_id" value="{{ session('deriv_data.user_id') ?? '' }}">
                        
                        <!-- User Information -->
                        <input type="hidden" name="email" value="{{ session('deriv_data.email') ?? '' }}">
                        <input type="hidden" name="first_name" value="{{ session('deriv_data.first_name') ?? '' }}">
                        <input type="hidden" name="last_name" value="{{ session('deriv_data.last_name') ?? '' }}">
                        <input type="hidden" name="country" value="{{ session('deriv_data.country') ?? '' }}">
                        
                        <!-- Company Information -->
                        <input type="hidden" name="landing_company_name" value="{{ session('deriv_data.landing_company_name') ?? '' }}">
                        <input type="hidden" name="landing_company_fullname" value="{{ session('deriv_data.landing_company_fullname') ?? '' }}">
                        
                        <!-- Additional User Details -->
                        <input type="hidden" name="is_virtual" value="{{ session('deriv_data.is_virtual') ? '1' : '0' }}">
                        <input type="hidden" name="date_of_birth" value="{{ session('deriv_data.date_of_birth') ?? '' }}">
                        <input type="hidden" name="place_of_birth" value="{{ session('deriv_data.place_of_birth') ?? '' }}">
                        
                        <!-- Address Information -->
                        <input type="hidden" name="address_line_1" value="{{ session('deriv_data.address.line_1') ?? '' }}">
                        <input type="hidden" name="address_line_2" value="{{ session('deriv_data.address.line_2') ?? '' }}">
                        <input type="hidden" name="address_city" value="{{ session('deriv_data.address.city') ?? '' }}">
                        <input type="hidden" name="address_state" value="{{ session('deriv_data.address.state') ?? '' }}">
                        <input type="hidden" name="address_postcode" value="{{ session('deriv_data.address.postcode') ?? '' }}">
                        
                        <!-- Tax Information -->
                        <input type="hidden" name="tax_identification_number" value="{{ session('deriv_data.tax_information.identification_number') ?? '' }}">
                        <input type="hidden" name="tax_residence" value="{{ session('deriv_data.tax_information.residence') ?? '' }}">
                        
                        <!-- Account Information -->
                        <input type="hidden" name="has_secret_answer" value="{{ session('deriv_data.has_secret_answer') ? '1' : '0' }}">
                        <input type="hidden" name="email_consent" value="{{ session('deriv_data.email_consent') ? '1' : '0' }}">
                        <input type="hidden" name="scopes" value="{{ json_encode(session('deriv_data.scopes') ?? []) }}">
                        <input type="hidden" name="account_list" value="{{ json_encode(session('deriv_data.account_list') ?? []) }}">

                        <div>
                            <label for="fullname" class="block text-sm font-medium text-text mb-1 sm:mb-2">
                                Full Name
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-text-light text-sm sm:text-base"></i>
                                </div>
                                <input type="text" 
                                       name="fullname" 
                                       id="fullname"
                                       class="block w-full pl-10 pr-3 py-2 sm:py-3 border border-gray-300 rounded-xl placeholder-text-light focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-sm sm:text-base"
                                       placeholder="Your full name" 
                                       required
                                       value="{{ old('fullname') }}"
                                       autocomplete="name">
                            </div>
                            @error('fullname')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-text mb-1 sm:mb-2">
                                Phone Number
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-gray-400"></i>
                                </div>
                                <input type="tel" 
                                       name="phone" 
                                       id="phone" 
                                       class="block w-full pl-10 pr-3 py-3 border {{ $errors->has('phone') ? 'border-red-300' : 'border-gray-200' }} rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-text"
                                       placeholder="e.g. 712345678" 
                                       autocomplete="off" 
                                       required>
                            </div>
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-text mb-1 sm:mb-2">
                                Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       class="block w-full pl-10 pr-10 py-3 border {{ $errors->has('password') ? 'border-red-300' : 'border-gray-200' }} rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-text"
                                       placeholder="Create a password" 
                                       required
                                       minlength="8"
                                       autocomplete="new-password">
                                <button type="button" 
                                        id="togglePassword" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-eye text-gray-400 hover:text-primary transition-colors duration-200"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="confirmpassword" class="block text-sm font-medium text-text mb-1 sm:mb-2">
                                Confirm Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password" 
                                       name="confirmpassword" 
                                       id="confirmpassword" 
                                       class="block w-full pl-10 pr-10 py-3 border {{ $errors->has('confirmpassword') ? 'border-red-300' : 'border-gray-200' }} rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-text"
                                       placeholder="Confirm your password" 
                                       required
                                       minlength="8"
                                       autocomplete="new-password">
                                <button type="button" 
                                        id="toggleConfirmPassword" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-eye text-gray-400 hover:text-primary transition-colors duration-200"></i>
                                </button>
                            </div>
                            @error('confirmpassword')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       id="termsCheck" 
                                       class="w-4 h-4 text-primary focus:ring-primary border-gray-300 rounded">
                            </div>
                            <div class="ml-3">
                                <label for="termsCheck" class="text-xs sm:text-sm text-text-light">
                                    I agree to the <a href="{{ route('terms') }}" class="text-primary hover:text-primary-600 transition-colors duration-200">Terms and Conditions</a>
                                </label>
                            </div>
                        </div>

                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-primary to-secondary hover:from-primary-600 hover:to-secondary-600 text-white font-semibold py-3 sm:py-4 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg flex items-center justify-center group" 
                                id="registerBtn">
                            <span class="spinner hidden animate-spin rounded-full h-4 w-4 sm:h-5 sm:w-5 border-b-2 border-white mr-2 sm:mr-3"></span>
                            <span class="btn-text text-sm sm:text-base">Complete Registration</span>
                        </button>
                    </form>

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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password toggle functionality
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPasswordField = document.getElementById('confirmpassword');

    function setupPasswordToggle(button, field) {
        button.addEventListener('click', function() {
            const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
            field.setAttribute('type', type);
            
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

    if (togglePassword && passwordField) {
        setupPasswordToggle(togglePassword, passwordField);
    }

    if (toggleConfirmPassword && confirmPasswordField) {
        setupPasswordToggle(toggleConfirmPassword, confirmPasswordField);
    }

    // Form validation functions
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

    function validatePhone() {
        const phoneInput = document.getElementById('phone');
        const phoneValue = phoneInput.value.trim();
        
        if (!phoneValue) {
            showError('phone', 'Phone number is required');
            return false;
        } else if (!/^[0-9]{9,12}$/.test(phoneValue)) {
            showError('phone', 'Please enter a valid phone number');
            return false;
        } else {
            clearError('phone');
            return true;
        }
    }

    function validatePassword() {
        const passwordInput = document.getElementById('password');
        const passwordValue = passwordInput.value;
        
        if (!passwordValue) {
            showError('password', 'Password is required');
            return false;
        } else if (passwordValue.length < 4) {
            showError('password', 'Password must be at least 4 characters');
            return false;
        } else {
            clearError('password');
            return true;
        }
    }

    function validateConfirmPassword() {
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirmpassword');
        const confirmPasswordValue = confirmPasswordInput.value;
        
        if (!confirmPasswordValue) {
            showError('confirmpassword', 'Please confirm your password');
            return false;
        } else if (passwordInput.value !== confirmPasswordValue) {
            showError('confirmpassword', 'Passwords do not match');
            return false;
        } else {
            clearError('confirmpassword');
            return true;
        }
    }

    // Register form validation
    const registerForm = document.getElementById('registerFormSubmit');
    if (registerForm) {
        // Field validation on blur
        document.getElementById('phone').addEventListener('blur', validatePhone);
        document.getElementById('password').addEventListener('blur', validatePassword);
        document.getElementById('confirmpassword').addEventListener('blur', validateConfirmPassword);

        registerForm.addEventListener('submit', function(e) {
            const isPhoneValid = validatePhone();
            const isPasswordValid = validatePassword();
            const isConfirmPasswordValid = validateConfirmPassword();
            const isTermsChecked = document.getElementById('termsCheck').checked;
            
            if (!isTermsChecked) {
                showToastError('Please agree to the Terms and Conditions');
                e.preventDefault();
                return;
            }

            if (!isPhoneValid || !isPasswordValid || !isConfirmPasswordValid) {
                e.preventDefault();
            } else {
                // Show loading state
                const registerBtn = document.getElementById('registerBtn');
                if (registerBtn) {
                    registerBtn.classList.add('btn-loading');
                    const spinner = registerBtn.querySelector('.spinner');
                    const btnText = registerBtn.querySelector('.btn-text');
                    
                    if (spinner) spinner.classList.remove('hidden');
                    if (btnText) btnText.textContent = 'Creating account...';
                }
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