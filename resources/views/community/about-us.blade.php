<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StepaKash - About Us</title>
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
        
        .about-hero {
            background: linear-gradient(rgba(15, 117, 58, 0.9), rgba(44, 138, 83, 0.9)), url('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
        }
        
        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(15, 117, 58, 0.2);
        }
        
        .value-card {
            transition: all 0.3s ease;
            border-left: 4px solid #0f753a;
        }
        
        .value-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(15, 117, 58, 0.1);
        }
        
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-primary-50 to-secondary-50 overflow-x-hidden">
    <!-- Navigation -->
    @include('partials.header')

    <!-- Hero Section -->
    <section class="about-hero text-white py-20">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Simplifying Digital Finance Across Africa</h1>
            <p class="text-xl max-w-3xl mx-auto">StepaKash is revolutionizing how Africans manage, move, and multiply their money through innovative fintech solutions.</p>
        </div>
    </section>

    <!-- Our Story -->
    <section class="py-16 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-primary mb-6">Our Story</h2>
                    <p class="text-lg text-text mb-6">Founded in 2023, StepaKash began with a simple mission: to make digital financial services accessible, affordable, and secure for every African.</p>
                    <p class="text-text-light mb-6">What started as a solution for Deriv traders to easily manage their funds has grown into a comprehensive financial platform serving thousands across the continent.</p>
                    <div class="bg-primary-50 p-6 rounded-xl border border-primary-100">
                        <h3 class="text-xl font-semibold text-primary mb-3">Our Vision</h3>
                        <p class="text-text">To become Africa's most trusted digital financial partner, bridging the gap between traditional banking and the crypto economy.</p>
                    </div>
                </div>
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="StepaKash team" class="rounded-xl shadow-lg w-full">
                    <div class="absolute -bottom-6 -right-6 bg-white p-4 rounded-xl shadow-md">
                        <div class="text-4xl font-bold text-primary">2.5M+</div>
                        <div class="text-text-light">Users Served</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Values -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-primary mb-12">Our Core Values</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="value-card bg-white p-8 rounded-lg hover-lift">
                    <div class="w-14 h-14 bg-primary-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-text">Security First</h3>
                    <p class="text-text-light">We prioritize the safety of your funds and data above all else, employing bank-grade security measures.</p>
                </div>
                
                <div class="value-card bg-white p-8 rounded-lg hover-lift">
                    <div class="w-14 h-14 bg-primary-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-text">Innovation</h3>
                    <p class="text-text-light">We constantly evolve to bring you cutting-edge financial solutions tailored for African markets.</p>
                </div>
                
                <div class="value-card bg-white p-8 rounded-lg hover-lift">
                    <div class="w-14 h-14 bg-primary-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-text">Community Focus</h3>
                    <p class="text-text-light">We build with and for our users, creating solutions that address real African financial challenges.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-16 px-6">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold text-center text-primary mb-12">Meet The Leadership</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="team-card bg-white rounded-xl overflow-hidden shadow-md transition-all duration-300">
                    <div class="h-64 bg-gray-100 relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-1.2.1&auto=format&fit=crop&w=334&q=80" alt="CEO" class="w-full h-full object-cover">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                            <h3 class="text-white font-bold text-lg">Livingstone Apeli</h3>
                            <p class="text-primary-200 text-sm">Founder & CEO</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-text-light text-sm">Fintech entrepreneur with 8+ years experience in digital payments and blockchain solutions.</p>
                    </div>
                </div>
                
                <div class="team-card bg-white rounded-xl overflow-hidden shadow-md transition-all duration-300">
                    <div class="h-64 bg-gray-100 relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?ixlib=rb-1.2.1&auto=format&fit=crop&w=334&q=80" alt="CTO" class="w-full h-full object-cover">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                            <h3 class="text-white font-bold text-lg">Sarah Mwangi</h3>
                            <p class="text-primary-200 text-sm">Chief Technology Officer</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-text-light text-sm">Former Google engineer specializing in secure financial systems architecture.</p>
                    </div>
                </div>
                
                <div class="team-card bg-white rounded-xl overflow-hidden shadow-md transition-all duration-300">
                    <div class="h-64 bg-gray-100 relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-1.2.1&auto=format&fit=crop&w=334&q=80" alt="CFO" class="w-full h-full object-cover">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                            <h3 class="text-white font-bold text-lg">James Omondi</h3>
                            <p class="text-primary-200 text-sm">Chief Financial Officer</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-text-light text-sm">Financial strategist with expertise in African markets and regulatory compliance.</p>
                    </div>
                </div>
                
                <div class="team-card bg-white rounded-xl overflow-hidden shadow-md transition-all duration-300">
                    <div class="h-64 bg-gray-100 relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1573497019706-4e0b6d934d9f?ixlib=rb-1.2.1&auto=format&fit=crop&w=334&q=80" alt="CMO" class="w-full h-full object-cover">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                            <h3 class="text-white font-bold text-lg">Amina Hassan</h3>
                            <p class="text-primary-200 text-sm">Chief Marketing Officer</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-text-light text-sm">Growth marketing specialist with a passion for financial inclusion.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Milestones -->
    <section class="py-16 bg-primary-50">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-primary mb-12">Our Journey</h2>
            <div class="relative">
                <!-- Timeline -->
                <div class="border-l-2 border-primary absolute h-full left-1/2 transform -translate-x-1/2"></div>
                
                <!-- Milestone Items -->
                <div class="space-y-12">
                    <div class="relative pl-8 md:pl-0">
                        <div class="md:w-1/2 md:pr-16 md:text-right">
                            <h3 class="text-xl font-semibold text-primary">2023 - Founded</h3>
                            <p class="text-text">StepaKash launched in Nairobi with Deriv integration as core feature</p>
                        </div>
                        <div class="absolute w-4 h-4 bg-primary rounded-full top-1 left-0 md:left-1/2 md:transform md:-translate-x-1/2"></div>
                    </div>
                    
                    <div class="relative pl-8 md:pl-0">
                        <div class="md:w-1/2 md:ml-auto md:pl-16">
                            <h3 class="text-xl font-semibold text-primary">Q2 2024 - 100K Users</h3>
                            <p class="text-text">Reached milestone of 100,000 active users across East Africa</p>
                        </div>
                        <div class="absolute w-4 h-4 bg-primary rounded-full top-1 left-0 md:left-1/2 md:transform md:-translate-x-1/2"></div>
                    </div>
                    
                    <div class="relative pl-8 md:pl-0">
                        <div class="md:w-1/2 md:pr-16 md:text-right">
                            <h3 class="text-xl font-semibold text-primary">Q4 2024 - $1M Processed</h3>
                            <p class="text-text">Surpassed $1 million in monthly transaction volume</p>
                        </div>
                        <div class="absolute w-4 h-4 bg-primary rounded-full top-1 left-0 md:left-1/2 md:transform md:-translate-x-1/2"></div>
                    </div>
                    
                    <div class="relative pl-8 md:pl-0">
                        <div class="md:w-1/2 md:ml-auto md:pl-16">
                            <h3 class="text-xl font-semibold text-primary">2025 - Regional Expansion</h3>
                            <p class="text-text">Launched services in 5 new African countries</p>
                        </div>
                        <div class="absolute w-4 h-4 bg-primary rounded-full top-1 left-0 md:left-1/2 md:transform md:-translate-x-1/2"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-6">
        <div class="max-w-4xl mx-auto bg-gradient-to-r from-primary to-secondary text-white rounded-2xl p-8 md:p-12 text-center">
            <h2 class="text-3xl font-bold mb-6">Ready to Simplify Your Finances?</h2>
            <p class="text-xl mb-8">Join over 2.5 million users who trust StepaKash for their digital financial needs.</p>
            <a href="{{ route('auth.deriv') }}" class="inline-block bg-white text-primary hover:bg-gray-100 px-8 py-3 rounded-full font-semibold transition-all duration-300 hover:scale-105">
                Get Started Now
            </a>
        </div>
    </section>

    <!-- Footer -->
    @include('partials.footer')
</body>
</html>