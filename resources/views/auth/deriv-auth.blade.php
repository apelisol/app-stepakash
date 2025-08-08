@extends('layouts.app')

@section('page', 'derivauth')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-background via-primary-50 to-secondary-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl w-full mx-4">
        <!-- Removed card container and shadow for cleaner look -->
        <div class="lg:flex rounded-2xl overflow-hidden">
            <!-- Left Feature Panel - Hidden on mobile -->
            <div class="hidden lg:block lg:w-1/2 bg-gradient-to-br from-primary to-secondary text-white p-8 lg:p-12 xl:p-16">
                <div class="max-w-md mx-auto lg:mx-0 h-full flex flex-col justify-center">
                    <div class="animate-fade-in">
                        <h2 class="text-2xl xl:text-3xl 2xl:text-4xl font-bold mb-4 lg:mb-6 leading-tight">
                            Connect Your Deriv Account
                        </h2>
                        <p class="text-lg xl:text-xl mb-6 lg:mb-8 text-primary-100 leading-relaxed">
                            Authorize StepaKash to access your Deriv account to enable seamless transactions.
                        </p>
                        
                        <ul class="space-y-4 lg:space-y-6">
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 bg-white/20 rounded-lg flex items-center justify-center mr-3 lg:mr-4 group-hover:bg-white/30 transition-colors duration-200">
                                    <i class="fas fa-user-plus text-lg lg:text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base lg:text-lg mb-1">Secure OAuth Connection</h3>
                                    <p class="text-primary-100 text-xs lg:text-sm">Industry-standard security protocols</p>
                                </div>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 bg-white/20 rounded-lg flex items-center justify-center mr-3 lg:mr-4 group-hover:bg-white/30 transition-colors duration-200">
                                    <i class="fas fa-shield-alt text-lg lg:text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base lg:text-lg mb-1">Limited Access</h3>
                                    <p class="text-primary-100 text-xs lg:text-sm">Only basic account information is accessed</p>
                                </div>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 bg-white/20 rounded-lg flex items-center justify-center mr-3 lg:mr-4 group-hover:bg-white/30 transition-colors duration-200">
                                    <i class="fas fa-lock text-lg lg:text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base lg:text-lg mb-1">Privacy Protected</h3>
                                    <p class="text-primary-100 text-xs lg:text-sm">Your credentials are never stored</p>
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
                        <h4 class="text-xl sm:text-2xl font-bold text-text mb-1 sm:mb-2">Connect Deriv Account</h4>
                        <p class="text-text-light text-base sm:text-lg">First step to create your StepaKash account</p>
                    </div>
                    
                    <!-- Alert Messages -->
                    @if(session('msg'))
                    <div class="mb-4 sm:mb-6 bg-warning/10 border border-warning/20 text-warning-800 px-4 py-3 rounded-lg relative animate-slide-up" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <span class="flex-1 text-sm">{{ session('msg') }}</span>
                            <button type="button" class="ml-2 text-warning-600 hover:text-warning-800 transition-colors duration-200" onclick="this.parentElement.parentElement.remove()">
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
                    
                    <!-- Authorization Button -->
                    <div class="space-y-4 sm:space-y-6">
                        <button class="w-full bg-gradient-to-r from-primary to-secondary hover:from-primary-600 hover:to-secondary-600 text-white font-semibold py-3 sm:py-4 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg flex items-center justify-center group" 
                                id="derivSignupBtn">
                            <i class="fab fa-connectdevelop mr-2 sm:mr-3 text-lg sm:text-xl group-hover:rotate-12 transition-transform duration-200"></i>
                            <span class="text-sm sm:text-base">Authorize Deriv</span>
                        </button>
                        
                        <p class="text-center text-xs sm:text-sm text-text-light leading-relaxed">
                            By connecting, you authorize StepaKash to access your Deriv account information 
                            for transaction purposes.
                        </p>
                        
                        <!-- Sign In Link -->
                        <div class="text-center pt-3 sm:pt-4 border-t border-gray-200">
                            <a href="{{ route('login') }}" 
                               class="text-primary hover:text-primary-600 font-medium transition-colors duration-200 flex items-center justify-center group text-sm sm:text-base">
                                <i class="fas fa-sign-in-alt mr-2 group-hover:translate-x-1 transition-transform duration-200"></i>
                                Already have an account? Sign In
                            </a>
                        </div>
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
document.addEventListener('DOMContentLoaded', function () {
    const derivSignupBtn = document.getElementById('derivSignupBtn');
    const loadingBars = document.getElementById('loadingBars');

    // Kill loading state when page becomes visible (user returns)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            // Page became visible - user likely returned from Deriv
            resetPageState();
        }
    });

    // Also handle page focus events
    window.addEventListener('focus', function() {
        resetPageState();
    });

    // Handle browser back/forward navigation
    window.addEventListener('pageshow', function(event) {
        // Reset state when page is shown (including back button)
        resetPageState();
    });

    function resetPageState() {
        // Hide loading bars and reset button state
        hideLoadingBars();
        resetButtonState();
        
        // Clear any stored loading state flags
        sessionStorage.removeItem('derivAuthInProgress');
        
        console.log('Page state reset - loading cleared');
    }

    if (derivSignupBtn) {
        derivSignupBtn.addEventListener('click', function () {
            initiateDerivOAuth();
        });
    }

    async function initiateDerivOAuth() {
        try {
            // Mark that auth is in progress
            sessionStorage.setItem('derivAuthInProgress', 'true');
            
            // Show loading state
            setButtonLoadingState('Authorizing...');
            showLoadingBars();

            const response = await fetch('{{ route("auth.deriv.oauth") }}', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.status === 'success') {
                // Update progress and redirect
                updateProgress(100, 'Redirecting to Deriv...');
                setTimeout(() => {
                    window.location.href = data.oauth_url;
                }, 1000);
            } else {
                // Clear auth progress flag on error
                sessionStorage.removeItem('derivAuthInProgress');
                hideLoadingBars();
                resetButtonState();
                showError('Failed to connect to Deriv: ' + (data.message || 'Unknown error'));
            }

        } catch (error) {
            // Clear auth progress flag on error
            sessionStorage.removeItem('derivAuthInProgress');
            hideLoadingBars();
            resetButtonState();
            showError('Network error. Please try again.');
            console.error('Deriv OAuth error:', error);
        }
    }

    function setButtonLoadingState(message) {
        const button = document.getElementById('derivSignupBtn');
        if (button) {
            button.disabled = true;
            button.classList.add('opacity-75', 'cursor-not-allowed');
            button.innerHTML = `
                <div class="animate-spin rounded-full h-4 w-4 sm:h-5 sm:w-5 border-b-2 border-white mr-2 sm:mr-3"></div>
                <span class="text-sm sm:text-base">${message}</span>
            `;
        }
    }

    function resetButtonState() {
        const button = document.getElementById('derivSignupBtn');
        if (button) {
            button.disabled = false;
            button.classList.remove('opacity-75', 'cursor-not-allowed');
            button.innerHTML = `
                <i class="fab fa-connectdevelop mr-2 sm:mr-3 text-lg sm:text-xl group-hover:rotate-12 transition-transform duration-200"></i>
                <span class="text-sm sm:text-base">Authorize Deriv</span>
            `;
        }
    }

    function showLoadingBars() {
        if (loadingBars) {
            loadingBars.classList.remove('hidden');
            loadingBars.classList.add('animate-slide-up');
            
            // Start progress animation
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += Math.random() * 15 + 5;
                if (progress > 90) progress = 90;
                updateProgress(progress, getLoadingMessage(progress));
            }, 300);
            
            // Store interval for cleanup
            loadingBars.dataset.intervalId = progressInterval;
        }
    }

    function hideLoadingBars() {
        if (loadingBars) {
            // Clear any running intervals
            if (loadingBars.dataset.intervalId) {
                clearInterval(parseInt(loadingBars.dataset.intervalId));
                delete loadingBars.dataset.intervalId;
            }
            
            loadingBars.classList.add('hidden');
            loadingBars.classList.remove('animate-slide-up');
            
            // Reset progress
            updateProgress(0, 'Initializing connection...');
        }
    }

    function updateProgress(percentage, status) {
        const mainProgressBar = document.getElementById('mainProgressBar');
        const loadingStatus = document.getElementById('loadingStatus');
        
        if (mainProgressBar) {
            mainProgressBar.style.width = percentage + '%';
        }
        
        if (loadingStatus && status) {
            loadingStatus.textContent = status;
        }
    }

    function getLoadingMessage(progress) {
        if (progress < 20) return 'Initializing connection...';
        if (progress < 40) return 'Establishing secure channel...';
        if (progress < 60) return 'Authenticating with Deriv...';
        if (progress < 80) return 'Verifying credentials...';
        if (progress < 95) return 'Preparing authorization...';
        return 'Almost ready...';
    }

    function showError(message) {
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