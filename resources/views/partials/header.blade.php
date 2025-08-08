<nav class="relative z-50 px-6 py-4 bg-white bg-opacity-90 backdrop-blur-sm border-b border-gray-100">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <!-- Logo -->
        <a href="{{ route('welcome') }}" class="flex items-center space-x-2">
            <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center">
                <span class="text-white font-bold text-lg">S</span>
            </div>
            <span class="text-2xl font-bold text-primary">StepaKash</span>
        </a>
        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center space-x-8">
            <a href="{{ route('features') }}" class="text-text hover:text-primary transition-colors font-medium">Features</a>
            <a href="{{ route('security') }}" class="text-text hover:text-primary transition-colors font-medium">Security</a>
            <a href="{{ route('about') }}" class="text-text hover:text-primary transition-colors font-medium">About</a>
        </div>
        <!-- Desktop Auth Buttons -->
        <div class="hidden md:flex items-center space-x-4">
            <a href="{{ route('login') }}" class="text-text hover:text-primary font-medium transition-colors">Log In</a>
            <a href="{{ route('auth.deriv') }}">
                <button class="bg-primary hover:bg-primary-600 text-white px-6 py-2 rounded-full transition-all duration-300 hover:scale-105 font-medium">
                    Sign Up
                </button>
            </a>
        </div>
        <!-- Mobile menu button -->
        <div class="md:hidden">
            <button id="mobile-menu-button" class="text-gray-700 hover:text-primary focus:outline-none">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
            </button>
        </div>
    </div>
    <!-- Mobile menu -->
    <div id="mobile-menu" class="md:hidden fixed inset-x-0 top-0 bg-white shadow-lg py-2 px-6 z-50 transform transition-all duration-300 ease-in-out translate-y-0 opacity-0 invisible" style="margin-top: 84px;">
        <div class="flex flex-col space-y-4 py-4">
            <a href="{{ route('features') }}" class="text-text hover:text-primary transition-colors font-medium py-2">Features</a>
            <a href="{{ route('security') }}" class="text-text hover:text-primary transition-colors font-medium py-2">Security</a>
            <a href="{{ route('about') }}" class="text-text hover:text-primary transition-colors font-medium py-2">About</a>
            <div class="border-t border-gray-100 my-2"></div>
            <a href="{{ route('login') }}" class="text-text hover:text-primary font-medium py-2">Log In</a>
            <a href="{{ route('auth.deriv') }}" class="block">
                <button class="w-full bg-primary hover:bg-primary-600 text-white px-6 py-2 rounded-full transition-all duration-300 font-medium">
                    Sign Up
                </button>
            </a>
        </div>
    </div>
    <!-- Mobile menu overlay -->
    <div id="mobile-menu-overlay" class="fixed inset-0 bg-black z-40 transition-opacity duration-300 opacity-0 invisible"></div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hamburgerBtn = document.getElementById('mobile-menu-button');
            const navMenu = document.getElementById('mobile-menu');
            const menuBackdrop = document.getElementById('mobile-menu-overlay');
            let menuVisible = false;
            
            function showHideMenu() {
                menuVisible = !menuVisible;
                
                if (menuVisible) {
                    // Open menu
                    navMenu.classList.remove('opacity-0', 'invisible');
                    navMenu.classList.add('opacity-100', 'visible');
                    
                    menuBackdrop.classList.remove('opacity-0', 'invisible');
                    menuBackdrop.classList.add('opacity-50', 'visible');
                    
                    document.body.style.overflow = 'hidden';
                } else {
                    // Close menu
                    navMenu.classList.remove('opacity-100', 'visible');
                    navMenu.classList.add('opacity-0', 'invisible');
                    
                    menuBackdrop.classList.remove('opacity-50', 'visible');
                    menuBackdrop.classList.add('opacity-0', 'invisible');
                    
                    document.body.style.overflow = '';
                }
            }
            
            hamburgerBtn.onclick = showHideMenu;
            menuBackdrop.onclick = showHideMenu;
            
            // Close menu when clicking on navigation links
            const navLinks = navMenu.querySelectorAll('a');
            navLinks.forEach(function(link) {
                link.onclick = function() {
                    if (menuVisible) {
                        showHideMenu();
                    }
                };
            });
            
            // Close menu with Escape key
            document.onkeydown = function(evt) {
                if (evt.key === 'Escape' && menuVisible) {
                    showHideMenu();
                }
            };
        });
    </script>
    @endpush
</nav>