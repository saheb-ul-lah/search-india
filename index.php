<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindIt - Your Local Business Directory</title>
    <meta name="description" content="Find the best local businesses, services, restaurants, and more in your city. Read reviews, get contact information, and discover new places with FindIt.">
    
    <!-- Favicon -->
    <link rel="icon" href="https://via.placeholder.com/32" type="image/png">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Configure Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        secondary: {
                            50: '#fdf4ff',
                            100: '#fae8ff',
                            200: '#f5d0fe',
                            300: '#f0abfc',
                            400: '#e879f9',
                            500: '#d946ef',
                            600: '#c026d3',
                            700: '#a21caf',
                            800: '#86198f',
                            900: '#701a75',
                        },
                        accent: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        },
                    },
                    fontFamily: {
                        'montserrat': ['Montserrat', 'sans-serif'],
                        'poppins': ['Poppins', 'sans-serif'],
                    },
                    animation: {
                        'bounce-slow': 'bounce 3s infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'float 3s ease-in-out infinite',
                        'slide-up': 'slideUp 0.5s ease-out forwards',
                        'slide-down': 'slideDown 0.5s ease-out forwards',
                        'fade-in': 'fadeIn 0.5s ease-out forwards',
                        'fade-in-slow': 'fadeIn 1s ease-out forwards',
                        'scale-in': 'scaleIn 0.5s ease-out forwards',
                        'spin-slow': 'spin 8s linear infinite',
                        'wiggle': 'wiggle 1s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        slideDown: {
                            '0%': { transform: 'translateY(-20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        scaleIn: {
                            '0%': { transform: 'scale(0.9)', opacity: '0' },
                            '100%': { transform: 'scale(1)', opacity: '1' },
                        },
                        wiggle: {
                            '0%, 100%': { transform: 'rotate(-3deg)' },
                            '50%': { transform: 'rotate(3deg)' },
                        }
                    },
                },
            },
        }
    </script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #0ea5e9 0%, #c026d3 100%);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .category-card:hover .category-icon {
            transform: scale(1.1);
        }
        
        .category-icon {
            transition: transform 0.3s ease;
        }
        
        .search-bar-shadow {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }
        
        .text-gradient {
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            background-image: linear-gradient(to right, #0ea5e9, #c026d3);
        }
        
        .bg-gradient-light {
            background: linear-gradient(135deg, #f0f9ff 0%, #fdf4ff 100%);
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #c026d3 100%);
        }
        
        .custom-shape-divider {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
        }
        
        .custom-shape-divider svg {
            position: relative;
            display: block;
            width: calc(100% + 1.3px);
            height: 70px;
        }
        
        .custom-shape-divider .shape-fill {
            fill: #FFFFFF;
        }
        
        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #0ea5e9;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #0284c7;
        }
        
        /* Parallax effect */
        .parallax {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
        
        /* Floating elements */
        .floating {
            animation: float 3s ease-in-out infinite;
        }
        
        .floating-delay-1 {
            animation-delay: 0.5s;
        }
        
        .floating-delay-2 {
            animation-delay: 1s;
        }
        
        /* Glow effect */
        .glow {
            box-shadow: 0 0 15px rgba(14, 165, 233, 0.5);
            transition: box-shadow 0.3s ease;
        }
        
        .glow:hover {
            box-shadow: 0 0 25px rgba(14, 165, 233, 0.8);
        }
        
        /* Modal styles */
        .modal {
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        
        .modal-content {
            transition: transform 0.3s ease;
            transform: scale(0.9);
        }
        
        .modal.active .modal-content {
            transform: scale(1);
        }
        
        /* Tab styles */
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        /* Counter animation */
        .counter-value {
            transition: all 0.5s ease;
        }
        
        /* Tooltip */
        .tooltip {
            position: relative;
        }
        
        .tooltip-text {
            visibility: hidden;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #333;
            color: white;
            text-align: center;
            padding: 5px 10px;
            border-radius: 6px;
            opacity: 0;
            transition: opacity 0.3s;
            white-space: nowrap;
        }
        
        .tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }
        
        /* Pulse animation for notification */
        .pulse {
            position: relative;
        }
        
        .pulse::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: rgba(14, 165, 233, 0.7);
            animation: pulse 2s infinite;
            z-index: -1;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.7;
            }
            70% {
                transform: scale(1.5);
                opacity: 0;
            }
            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }
        
        /* Animated background */
        .animated-bg {
            background: linear-gradient(-45deg, #0ea5e9, #c026d3, #f97316, #0ea5e9);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        
        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        
        /* Ripple effect */
        .ripple {
            position: relative;
            overflow: hidden;
        }
        
        .ripple::after {
            content: "";
            display: block;
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            background-image: radial-gradient(circle, #fff 10%, transparent 10.01%);
            background-repeat: no-repeat;
            background-position: 50%;
            transform: scale(10, 10);
            opacity: 0;
            transition: transform .5s, opacity 1s;
        }
        
        .ripple:active::after {
            transform: scale(0, 0);
            opacity: .3;
            transition: 0s;
        }
        
        /* Scroll indicator */
        .scroll-indicator {
            height: 3px;
            background: linear-gradient(to right, #0ea5e9, #c026d3);
            width: 0%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999;
            transition: width 0.2s ease;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Scroll Indicator -->
    <div class="scroll-indicator"></div>

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="#" class="flex items-center">
                        <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-primary-500 to-secondary-500 flex items-center justify-center text-white font-bold text-xl">F</div>
                        <span class="ml-2 text-2xl font-bold text-gray-900 font-montserrat">Find<span class="text-primary-600">It</span></span>
                    </a>
                </div>
                
                <!-- Navigation - Desktop -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-gray-900 hover:text-primary-600 font-medium transition-colors duration-200">Home</a>
                    <a href="#categories" class="text-gray-900 hover:text-primary-600 font-medium transition-colors duration-200">Categories</a>
                    <a href="#businesses" class="text-gray-900 hover:text-primary-600 font-medium transition-colors duration-200">Businesses</a>
                    <a href="#how-it-works" class="text-gray-900 hover:text-primary-600 font-medium transition-colors duration-200">How It Works</a>
                    <a href="#testimonials" class="text-gray-900 hover:text-primary-600 font-medium transition-colors duration-200">Testimonials</a>
                </nav>
                
                <!-- Auth Buttons - Desktop -->
                <div class="hidden md:flex items-center space-x-4">
                    <button id="login-button" class="text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200">Login</button>
                    <button id="signup-button" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300">Sign Up</button>
                </div>
                
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="md:hidden text-gray-900 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden pb-4">
                <div class="flex flex-col space-y-3">
                    <a href="#home" class="text-gray-900 hover:text-primary-600 font-medium py-2 transition-colors duration-200">Home</a>
                    <a href="#categories" class="text-gray-900 hover:text-primary-600 font-medium py-2 transition-colors duration-200">Categories</a>
                    <a href="#businesses" class="text-gray-900 hover:text-primary-600 font-medium py-2 transition-colors duration-200">Businesses</a>
                    <a href="#how-it-works" class="text-gray-900 hover:text-primary-600 font-medium py-2 transition-colors duration-200">How It Works</a>
                    <a href="#testimonials" class="text-gray-900 hover:text-primary-600 font-medium py-2 transition-colors duration-200">Testimonials</a>
                    <div class="flex space-x-4 pt-2">
                        <button id="mobile-login-button" class="text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200">Login</button>
                        <button id="mobile-signup-button" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">Sign Up</button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero-gradient text-white relative overflow-hidden min-h-screen flex items-center">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right" data-aos-duration="1000">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold font-montserrat leading-tight mb-6">Discover the Best Local Businesses Near You</h1>
                    <p class="text-lg md:text-xl opacity-90 mb-8">Find top-rated restaurants, services, shops, and more in your area. Read reviews, get directions, and make informed decisions.</p>
                    
                    <!-- Search Bar -->
                    <div class="bg-white rounded-lg p-2 search-bar-shadow mb-8 transform transition-all duration-500 hover:scale-105">
                        <form id="search-form" class="flex flex-col md:flex-row">
                            <div class="flex-1 mb-2 md:mb-0 md:mr-2">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                    <input type="text" id="search-query" placeholder="What are you looking for?" class="w-full pl-10 pr-4 py-3 rounded-lg border-0 focus:ring-2 focus:ring-primary-500 outline-none" required>
                                </div>
                            </div>
                            <div class="flex-1 mb-2 md:mb-0 md:mr-2">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-map-marker-alt text-gray-400"></i>
                                    </div>
                                    <input type="text" id="search-location" placeholder="Location" class="w-full pl-10 pr-4 py-3 rounded-lg border-0 focus:ring-2 focus:ring-primary-500 outline-none">
                                </div>
                            </div>
                            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-300 flex items-center justify-center ripple transform hover:scale-105 shadow-md hover:shadow-lg">
                                <span>Search</span>
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </form>
                    </div>
                    
                    <!-- Popular Searches -->
                    <div class="flex flex-wrap items-center">
                        <span class="text-white opacity-90 mr-3 mb-2">Popular:</span>
                        <a href="#" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-full px-4 py-1 text-sm mr-2 mb-2 transition-all duration-300 hover:transform hover:scale-110">Restaurants</a>
                        <a href="#" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-full px-4 py-1 text-sm mr-2 mb-2 transition-all duration-300 hover:transform hover:scale-110">Hotels</a>
                        <a href="#" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-full px-4 py-1 text-sm mr-2 mb-2 transition-all duration-300 hover:transform hover:scale-110">Plumbers</a>
                        <a href="#" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-full px-4 py-1 text-sm mr-2 mb-2 transition-all duration-300 hover:transform hover:scale-110">Electricians</a>
                        <a href="#" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-full px-4 py-1 text-sm mr-2 mb-2 transition-all duration-300 hover:transform hover:scale-110">Gyms</a>
                    </div>
                </div>
                
                <div class="hidden lg:block relative" data-aos="fade-left" data-aos-duration="1000">
                    <img src="https://via.placeholder.com/600x500" alt="Find local businesses" class="w-full max-w-lg mx-auto rounded-lg shadow-2xl animate-float">
                    
                    <!-- Floating Elements -->
                    <div class="absolute top-0 right-0 bg-white rounded-lg shadow-lg p-4 animate-float floating-delay-1">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 mr-3">
                                <i class="fas fa-star"></i>
                            </div>
                            <div>
                                <div class="text-sm font-semibold">Top Rated</div>
                                <div class="text-xs text-gray-500">4.8/5 Average</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="absolute bottom-20 left-0 bg-white rounded-lg shadow-lg p-4 animate-float floating-delay-2">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 mr-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <div class="text-sm font-semibold">Verified Listings</div>
                                <div class="text-xs text-gray-500">Trusted Businesses</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Animated Shapes -->
        <div class="absolute top-0 right-0 -mt-16 -mr-16 opacity-50 lg:opacity-70 animate-spin-slow">
            <svg width="404" height="404" fill="none" viewBox="0 0 404 404">
                <defs>
                    <pattern id="pattern-circles" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <circle cx="10" cy="10" r="3" fill="white" fill-opacity="0.3" />
                    </pattern>
                </defs>
                <rect width="404" height="404" fill="url(#pattern-circles)" />
            </svg>
        </div>
        
        <div class="absolute bottom-0 left-0 -mb-16 -ml-16 opacity-50 lg:opacity-70">
            <svg width="404" height="404" fill="none" viewBox="0 0 404 404">
                <defs>
                    <pattern id="pattern-circles-2" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <circle cx="10" cy="10" r="3" fill="white" fill-opacity="0.3" />
                    </pattern>
                </defs>
                <rect width="404" height="404" fill="url(#pattern-circles-2)" />
            </svg>
        </div>
        
        <!-- Wave Divider -->
        <div class="custom-shape-divider">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
            </svg>
        </div>
        
        <!-- Scroll Down Indicator -->
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 text-center">
            <a href="#categories" class="text-white flex flex-col items-center animate-bounce-slow">
                <span class="mb-2 text-sm font-medium">Scroll Down</span>
                <i class="fas fa-chevron-down"></i>
            </a>
        </div>
    </section>

    <!-- Popular Categories Section -->
    <section id="categories" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold font-montserrat mb-4">Popular Categories</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">Explore our most popular categories to find exactly what you're looking for.</p>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6 md:gap-8">
                <!-- Category 1 -->
                <a href="#" class="category-card bg-white rounded-xl shadow-md hover:shadow-xl p-6 text-center card-hover transition-all duration-300" data-aos="fade-up" data-aos-delay="0">
                    <div class="bg-primary-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-utensils text-primary-600 text-2xl category-icon"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Restaurants</h3>
                    <p class="text-gray-500 text-sm">1,248 listings</p>
                </a>
                
                <!-- Category 2 -->
                <a href="#" class="category-card bg-white rounded-xl shadow-md hover:shadow-xl p-6 text-center card-hover transition-all duration-300" data-aos="fade-up" data-aos-delay="50">
                    <div class="bg-primary-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-hotel text-primary-600 text-2xl category-icon"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Hotels</h3>
                    <p class="text-gray-500 text-sm">876 listings</p>
                </a>
                
                <!-- Category 3 -->
                <a href="#" class="category-card bg-white rounded-xl shadow-md hover:shadow-xl p-6 text-center card-hover transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="bg-primary-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shopping-bag text-primary-600 text-2xl category-icon"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Shopping</h3>
                    <p class="text-gray-500 text-sm">1,432 listings</p>
                </a>
                
                <!-- Category 4 -->
                <a href="#" class="category-card bg-white rounded-xl shadow-md hover:shadow-xl p-6 text-center card-hover transition-all duration-300" data-aos="fade-up" data-aos-delay="150">
                    <div class="bg-primary-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-wrench text-primary-600 text-2xl category-icon"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Home Services</h3>
                    <p class="text-gray-500 text-sm">954 listings</p>
                </a>
                
                <!-- Category 5 -->
                <a href="#" class="category-card bg-white rounded-xl shadow-md hover:shadow-xl p-6 text-center card-hover transition-all duration-300" data-aos="fade-up" data-aos-delay="200">
                    <div class="bg-primary-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-heartbeat text-primary-600 text-2xl category-icon"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Healthcare</h3>
                    <p class="text-gray-500 text-sm">687 listings</p>
                </a>
                
                <!-- Category 6 -->
                <a href="#" class="category-card bg-white rounded-xl shadow-md hover:shadow-xl p-6 text-center card-hover transition-all duration-300" data-aos="fade-up" data-aos-delay="250">
                    <div class="bg-primary-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-graduation-cap text-primary-600 text-2xl category-icon"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Education</h3>
                    <p class="text-gray-500 text-sm">543 listings</p>
                </a>
                
                <!-- Category 7 -->
                <a href="#" class="category-card bg-white rounded-xl shadow-md hover:shadow-xl p-6 text-center card-hover transition-all duration-300" data-aos="fade-up" data-aos-delay="300">
                    <div class="bg-primary-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-car text-primary-600 text-2xl category-icon"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Automotive</h3>
                    <p class="text-gray-500 text-sm">765 listings</p>
                </a>
                
                <!-- Category 8 -->
                <a href="#" class="category-card bg-white rounded-xl shadow-md hover:shadow-xl p-6 text-center card-hover transition-all duration-300" data-aos="fade-up" data-aos-delay="350">
                    <div class="bg-primary-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-dumbbell text-primary-600 text-2xl category-icon"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Fitness</h3>
                    <p class="text-gray-500 text-sm">432 listings</p>
                </a>
            </div>
            
            <div class="text-center mt-12">
                <a href="#" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200 group">
                    <span>View All Categories</span>
                    <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform duration-300"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Interactive Tabs Section -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold font-montserrat mb-4">Explore Our Services</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">Discover the different ways FindIt can help you connect with local businesses.</p>
            </div>
            
            <!-- Tabs Navigation -->
            <div class="flex flex-wrap justify-center mb-8" data-aos="fade-up">
                <button class="tab-button active px-6 py-3 mx-2 my-2 rounded-full bg-primary-600 text-white font-medium transition-all duration-300 hover:shadow-lg" data-tab="tab1">
                    <i class="fas fa-search mr-2"></i> Find Businesses
                </button>
                <button class="tab-button px-6 py-3 mx-2 my-2 rounded-full bg-gray-200 text-gray-700 font-medium transition-all duration-300 hover:bg-gray-300" data-tab="tab2">
                    <i class="fas fa-star mr-2"></i> Read Reviews
                </button>
                <button class="tab-button px-6 py-3 mx-2 my-2 rounded-full bg-gray-200 text-gray-700 font-medium transition-all duration-300 hover:bg-gray-300" data-tab="tab3">
                    <i class="fas fa-building mr-2"></i> List Your Business
                </button>
                <button class="tab-button px-6 py-3 mx-2 my-2 rounded-full bg-gray-200 text-gray-700 font-medium transition-all duration-300 hover:bg-gray-300" data-tab="tab4">
                    <i class="fas fa-mobile-alt mr-2"></i> Mobile App
                </button>
            </div>
            
            <!-- Tabs Content -->
            <div class="bg-white rounded-xl shadow-lg p-6 md:p-8" data-aos="fade-up">
                <!-- Tab 1 Content -->
                <div id="tab1" class="tab-content active">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                        <div>
                            <h3 class="text-2xl font-bold mb-4">Find the Perfect Local Business</h3>
                            <p class="text-gray-600 mb-6">Our powerful search engine helps you find exactly what you're looking for in your area. Filter results by category, rating, price range, and more.</p>
                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i>
                                    <span class="text-gray-700">Search by category, location, or specific business name</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i>
                                    <span class="text-gray-700">Filter results by rating, price, and special features</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i>
                                    <span class="text-gray-700">View business hours, contact information, and directions</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i>
                                    <span class="text-gray-700">Save your favorite businesses for quick access</span>
                                </li>
                            </ul>
                            <button class="mt-6 bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
                                Start Searching
                            </button>
                        </div>
                        <div class="hidden md:block">
                            <img src="https://via.placeholder.com/600x400" alt="Find businesses" class="rounded-lg shadow-md">
                        </div>
                    </div>
                </div>
                
                <!-- Tab 2 Content -->
                <div id="tab2" class="tab-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                        <div class="hidden md:block">
                            <img src="https://via.placeholder.com/600x400" alt="Read reviews" class="rounded-lg shadow-md">
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold mb-4">Read & Write Authentic Reviews</h3>
                            <p class="text-gray-600 mb-6">Get insights from real customers before making a decision. Share your own experiences to help others in the community.</p>
                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i>
                                    <span class="text-gray-700">Read detailed reviews from verified customers</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i>
                                    <span class="text-gray-700">View photos uploaded by real visitors</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i>
                                    <span class="text-gray-700">Share your own experiences with the community</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i>
                                    <span class="text-gray-700">Earn points and badges for helpful reviews</span>
                                </li>
                            </ul>
                            <button class="mt-6 bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
                                Explore Reviews
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Tab 3 Content -->
                <div id="tab3" class="tab-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                        <div>
                            <h3 class="text-2xl font-bold mb-4">List Your Business on FindIt</h3>
                            <p class="text-gray-600 mb-6">Reach thousands of potential customers by listing your business on our platform. Choose from different plans to suit your needs.</p>
                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i>
                                    <span class="text-gray-700">Create a detailed business profile with photos and services</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i>
                                    <span class="text-gray-700">Respond to customer reviews and messages</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i>
                                    <span class="text-gray-700">Access analytics to track profile views and engagement</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i>
                                    <span class="text-gray-700">Promote special offers and events to attract customers</span>
                                </li>
                            </ul>
                            <button class="mt-6 bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
                                List Your Business
                            </button>
                        </div>
                        <div class="hidden md:block">
                            <img src="https://via.placeholder.com/600x400" alt="List your business" class="rounded-lg shadow-md">
                        </div>
                    </div>
                </div>
                
                <!-- Tab 4 Content -->
                <div id="tab4" class="tab-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                        <div class="hidden md:block">
                            <img src="https://via.placeholder.com/600x400" alt="Mobile app" class="rounded-lg shadow-md">
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold mb-4">Take FindIt With You Everywhere</h3>
                            <p class="text-gray-600 mb-6">Download our mobile app to access FindIt on the go. Available for iOS and Android devices with exclusive mobile features.</p>
                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i>
                                    <span class="text-gray-700">Find businesses near your current location</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i>
                                    <span class="text-gray-700">Get directions and call businesses with one tap</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i>
                                    <span class="text-gray-700">Save businesses for offline viewing</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i>
                                    <span class="text-gray-700">Receive notifications about deals near you</span>
                                </li>
                            </ul>
                            <div class="mt-6 flex flex-wrap gap-4">
                                <button class="flex items-center bg-black text-white rounded-lg px-4 py-3 hover:bg-gray-800 transition-all duration-300 transform hover:scale-105">
                                    <i class="fab fa-apple text-2xl mr-3"></i>
                                    <div>
                                        <div class="text-xs">Download on the</div>
                                        <div class="text-lg font-semibold">App Store</div>
                                    </div>
                                </button>
                                <button class="flex items-center bg-black text-white rounded-lg px-4 py-3 hover:bg-gray-800 transition-all duration-300 transform hover:scale-105">
                                    <i class="fab fa-google-play text-2xl mr-3"></i>
                                    <div>
                                        <div class="text-xs">Get it on</div>
                                        <div class="text-lg font-semibold">Google Play</div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Businesses Section -->
    <section id="businesses" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold font-montserrat mb-4">Featured Businesses</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">Discover top-rated businesses in your area that have been verified for quality and service.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Business 1 -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover" data-aos="fade-up" data-aos-delay="0">
                    <div class="relative">
                        <img src="https://via.placeholder.com/600x400" alt="Coastal Cuisine" class="w-full h-48 object-cover">
                        <div class="absolute top-4 left-4">
                            <span class="bg-primary-600 text-white text-xs font-semibold px-3 py-1 rounded-full">Featured</span>
                        </div>
                        <div class="absolute top-4 right-4">
                            <span class="bg-white text-gray-800 text-xs font-semibold px-3 py-1 rounded-full shadow-md">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                4.8
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-2">
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 rounded-full px-3 py-1">Restaurant</span>
                        </div>
                        <h3 class="text-xl font-bold mb-2 hover:text-primary-600 transition-colors duration-200">
                            <a href="#" class="business-link">Coastal Cuisine</a>
                        </h3>
                        <div class="flex items-center text-gray-500 text-sm mb-4">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span>San Francisco, CA</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-phone text-primary-600 mr-2"></i>
                                <span class="text-gray-700">(415) 555-1234</span>
                            </div>
                            <span class="text-sm text-gray-500">124 reviews</span>
                        </div>
                    </div>
                </div>
                
                <!-- Business 2 -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover" data-aos="fade-up" data-aos-delay="100">
                    <div class="relative">
                        <img src="https://via.placeholder.com/600x400" alt="Luxury Suites Hotel" class="w-full h-48 object-cover">
                        <div class="absolute top-4 left-4">
                            <span class="bg-primary-600 text-white text-xs font-semibold px-3 py-1 rounded-full">Featured</span>
                        </div>
                        <div class="absolute top-4 right-4">
                            <span class="bg-white text-gray-800 text-xs font-semibold px-3 py-1 rounded-full shadow-md">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                4.7
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-2">
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 rounded-full px-3 py-1">Hotel</span>
                        </div>
                        <h3 class="text-xl font-bold mb-2 hover:text-primary-600 transition-colors duration-200">
                            <a href="#" class="business-link">Luxury Suites Hotel</a>
                        </h3>
                        <div class="flex items-center text-gray-500 text-sm mb-4">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span>New York, NY</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-phone text-primary-600 mr-2"></i>
                                <span class="text-gray-700">(212) 555-6789</span>
                            </div>
                            <span class="text-sm text-gray-500">98 reviews</span>
                        </div>
                    </div>
                </div>
                
                <!-- Business 3 -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover" data-aos="fade-up" data-aos-delay="200">
                    <div class="relative">
                        <img src="https://via.placeholder.com/600x400" alt="Elite Fitness Center" class="w-full h-48 object-cover">
                        <div class="absolute top-4 left-4">
                            <span class="bg-primary-600 text-white text-xs font-semibold px-3 py-1 rounded-full">Featured</span>
                        </div>
                        <div class="absolute top-4 right-4">
                            <span class="bg-white text-gray-800 text-xs font-semibold px-3 py-1 rounded-full shadow-md">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                4.9
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-2">
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 rounded-full px-3 py-1">Fitness</span>
                        </div>
                        <h3 class="text-xl font-bold mb-2 hover:text-primary-600 transition-colors duration-200">
                            <a href="#" class="business-link">Elite Fitness Center</a>
                        </h3>
                        <div class="flex items-center text-gray-500 text-sm mb-4">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span>Chicago, IL</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-phone text-primary-600 mr-2"></i>
                                <span class="text-gray-700">(312) 555-9876</span>
                            </div>
                            <span class="text-sm text-gray-500">156 reviews</span>
                        </div>
                    </div>
                </div>
                
                <!-- Business 4 -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover" data-aos="fade-up" data-aos-delay="300">
                    <div class="relative">
                        <img src="https://via.placeholder.com/600x400" alt="Tech Solutions Inc." class="w-full h-48 object-cover">
                        <div class="absolute top-4 left-4">
                            <span class="bg-primary-600 text-white text-xs font-semibold px-3 py-1 rounded-full">Featured</span>
                        </div>
                        <div class="absolute top-4 right-4">
                            <span class="bg-white text-gray-800 text-xs font-semibold px-3 py-1 rounded-full shadow-md">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                4.6
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-2">
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 rounded-full px-3 py-1">Technology</span>
                        </div>
                        <h3 class="text-xl font-bold mb-2 hover:text-primary-600 transition-colors duration-200">
                            <a href="#" class="business-link">Tech Solutions Inc.</a>
                        </h3>
                        <div class="flex items-center text-gray-500 text-sm mb-4">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span>Austin, TX</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-phone text-primary-600 mr-2"></i>
                                <span class="text-gray-700">(512) 555-4321</span>
                            </div>
                            <span class="text-sm text-gray-500">87 reviews</span>
                        </div>
                    </div>
                </div>
                
                <!-- Business 5 -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover" data-aos="fade-up" data-aos-delay="400">
                    <div class="relative">
                        <img src="https://via.placeholder.com/600x400" alt="Green Thumb Landscaping" class="w-full h-48 object-cover">
                        <div class="absolute top-4 left-4">
                            <span class="bg-primary-600 text-white text-xs font-semibold px-3 py-1 rounded-full">Featured</span>
                        </div>
                        <div class="absolute top-4 right-4">
                            <span class="bg-white text-gray-800 text-xs font-semibold px-3 py-1 rounded-full shadow-md">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                4.8
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-2">
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 rounded-full px-3 py-1">Home Services</span>
                        </div>
                        <h3 class="text-xl font-bold mb-2 hover:text-primary-600 transition-colors duration-200">
                            <a href="#" class="business-link">Green Thumb Landscaping</a>
                        </h3>
                        <div class="flex items-center text-gray-500 text-sm mb-4">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span>Denver, CO</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-phone text-primary-600 mr-2"></i>
                                <span class="text-gray-700">(720) 555-8765</span>
                            </div>
                            <span class="text-sm text-gray-500">112 reviews</span>
                        </div>
                    </div>
                </div>
                
                <!-- Business 6 -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover" data-aos="fade-up" data-aos-delay="500">
                    <div class="relative">
                        <img src="https://via.placeholder.com/600x400" alt="Healing Hands Spa" class="w-full h-48 object-cover">
                        <div class="absolute top-4 left-4">
                            <span class="bg-primary-600 text-white text-xs font-semibold px-3 py-1 rounded-full">Featured</span>
                        </div>
                        <div class="absolute top-4 right-4">
                            <span class="bg-white text-gray-800 text-xs font-semibold px-3 py-1 rounded-full shadow-md">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                4.9
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-2">
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 rounded-full px-3 py-1">Beauty & Spa</span>
                        </div>
                        <h3 class="text-xl font-bold mb-2 hover:text-primary-600 transition-colors duration-200">
                            <a href="#" class="business-link">Healing Hands Spa</a>
                        </h3>
                        <div class="flex items-center text-gray-500 text-sm mb-4">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span>Seattle, WA</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-phone text-primary-600 mr-2"></i>
                                <span class="text-gray-700">(206) 555-3456</span>
                            </div>
                            <span class="text-sm text-gray-500">143 reviews</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-12">
                <a href="#" class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-8 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
                    Explore All Businesses
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold font-montserrat mb-4">How It Works</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">Finding the perfect local business has never been easier.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
                <div class="text-center" data-aos="fade-up" data-aos-delay="0">
                    <div class="bg-primary-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 transform transition-all duration-500 hover:scale-110 hover:bg-primary-100">
                        <i class="fas fa-search text-primary-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Search</h3>
                    <p class="text-gray-600">Enter what you're looking for and your location to find the best matches in your area.</p>
                </div>
                
                <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="bg-primary-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 transform transition-all duration-500 hover:scale-110 hover:bg-primary-100">
                        <i class="fas fa-star text-primary-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Compare</h3>
                    <p class="text-gray-600">Read reviews, check ratings, and compare services to find the perfect match for your needs.</p>
                </div>
                
                <div class="text-center" data-aos="fade-up" data-aos-delay="400">
                    <div class="bg-primary-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 transform transition-all duration-500 hover:scale-110 hover:bg-primary-100">
                        <i class="fas fa-check-circle text-primary-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Connect</h3>
                    <p class="text-gray-600">Contact businesses directly, get directions, or book services online with just a few clicks.</p>
                </div>
            </div>
            
            <div class="mt-16 text-center" data-aos="fade-up">
                <button id="learn-more-button" class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-8 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg mr-4 ripple">
                    Sign Up Now
                </button>
                <button id="about-button" class="inline-block bg-white hover:bg-gray-100 text-primary-600 font-medium py-3 px-8 rounded-lg border border-primary-600 transition-all duration-300 transform hover:scale-105 ripple">
                    Learn More
                </button>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-16 md:py-24 bg-gradient-light">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold font-montserrat mb-4">What Our Users Say</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">Read testimonials from people who have found great local businesses through our platform.</p>
            </div>
            
            <!-- Testimonial Slider -->
            <div class="testimonial-slider relative max-w-5xl mx-auto" data-aos="fade-up">
                <div class="swiper-container overflow-hidden">
                    <div class="swiper-wrapper">
                        <!-- Testimonial 1 -->
                        <div class="swiper-slide">
                            <div class="bg-white rounded-xl shadow-md p-8 md:p-10 transform transition-all duration-500 hover:shadow-xl">
                                <div class="flex items-center mb-6">
                                    <div class="text-yellow-400 flex">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <p class="text-gray-700 text-lg italic mb-8">"FindIt helped me discover an amazing local restaurant that has become my family's favorite spot. The reviews were spot on, and I appreciate how easy it was to find exactly what I was looking for."</p>
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-full bg-gray-300 overflow-hidden">
                                        <img src="https://via.placeholder.com/100x100" alt="Sarah Johnson" class="w-full h-full object-cover">
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="font-bold">Sarah Johnson</h4>
                                        <p class="text-gray-500 text-sm">New York, NY</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Testimonial 2 -->
                        <div class="swiper-slide">
                            <div class="bg-white rounded-xl shadow-md p-8 md:p-10 transform transition-all duration-500 hover:shadow-xl">
                                <div class="flex items-center mb-6">
                                    <div class="text-yellow-400 flex">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <p class="text-gray-700 text-lg italic mb-8">"As a small business owner, FindIt has been instrumental in helping me reach new customers. The platform is easy to use, and the support team is always helpful when I have questions."</p>
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-full bg-gray-300 overflow-hidden">
                                        <img src="https://via.placeholder.com/100x100" alt="Michael Rodriguez" class="w-full h-full object-cover">
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="font-bold">Michael Rodriguez</h4>
                                        <p class="text-gray-500 text-sm">Chicago, IL</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Testimonial 3 -->
                        <div class="swiper-slide">
                            <div class="bg-white rounded-xl shadow-md p-8 md:p-10 transform transition-all duration-500 hover:shadow-xl">
                                <div class="flex items-center mb-6">
                                    <div class="text-yellow-400 flex">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                </div>
                                <p class="text-gray-700 text-lg italic mb-8">"When I moved to a new city, FindIt was my go-to resource for finding everything from a reliable plumber to the best coffee shops. It made settling in so much easier!"</p>
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-full bg-gray-300 overflow-hidden">
                                        <img src="https://via.placeholder.com/100x100" alt="Emily Chen" class="w-full h-full object-cover">
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="font-bold">Emily Chen</h4>
                                        <p class="text-gray-500 text-sm">Seattle, WA</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation Buttons -->
                <button class="testimonial-prev absolute top-1/2 -left-4 md:-left-8 transform -translate-y-1/2 bg-white rounded-full w-10 h-10 flex items-center justify-center shadow-md z-10 focus:outline-none hover:bg-gray-100 transition-all duration-300">
                    <i class="fas fa-chevron-left text-primary-600"></i>
                </button>
                <button class="testimonial-next absolute top-1/2 -right-4 md:-right-8 transform -translate-y-1/2 bg-white rounded-full w-10 h-10 flex items-center justify-center shadow-md z-10 focus:outline-none hover:bg-gray-100 transition-all duration-300">
                    <i class="fas fa-chevron-right text-primary-600"></i>
                </button>
            </div>
            
            <!-- Testimonial Pagination -->
            <div class="swiper-pagination mt-8 flex justify-center space-x-2"></div>
        </div>
    </section>

    <!-- Popular Cities Section -->
    <section class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold font-montserrat mb-4">Popular Cities</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">Explore businesses in these popular locations or find services in your own city.</p>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <!-- City 1 -->
                <a href="#" class="bg-gray-50 hover:bg-primary-50 rounded-lg p-4 text-center transition-all duration-300 transform hover:scale-105" data-aos="fade-up" data-aos-delay="0">
                    <h3 class="font-medium text-gray-900">New York</h3>
                    <p class="text-sm text-gray-500">NY</p>
                    <p class="text-xs text-primary-600 mt-1">1,248 businesses</p>
                </a>
                
                <!-- City 2 -->
                <a href="#" class="bg-gray-50 hover:bg-primary-50 rounded-lg p-4 text-center transition-all duration-300 transform hover:scale-105" data-aos="fade-up" data-aos-delay="50">
                    <h3 class="font-medium text-gray-900">Los Angeles</h3>
                    <p class="text-sm text-gray-500">CA</p>
                    <p class="text-xs text-primary-600 mt-1">986 businesses</p>
                </a>
                
                <!-- City 3 -->
                <a href="#" class="bg-gray-50 hover:bg-primary-50 rounded-lg p-4 text-center transition-all duration-300 transform hover:scale-105" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="font-medium text-gray-900">Chicago</h3>
                    <p class="text-sm text-gray-500">IL</p>
                    <p class="text-xs text-primary-600 mt-1">754 businesses</p>
                </a>
                
                <!-- City 4 -->
                <a href="#" class="bg-gray-50 hover:bg-primary-50 rounded-lg p-4 text-center transition-all duration-300 transform hover:scale-105" data-aos="fade-up" data-aos-delay="150">
                    <h3 class="font-medium text-gray-900">Houston</h3>
                    <p class="text-sm text-gray-500">TX</p>
                    <p class="text-xs text-primary-600 mt-1">632 businesses</p>
                </a>
                
                <!-- City 5 -->
                <a href="#" class="bg-gray-50 hover:bg-primary-50 rounded-lg p-4 text-center transition-all duration-300 transform hover:scale-105" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="font-medium text-gray-900">Miami</h3>
                    <p class="text-sm text-gray-500">FL</p>
                    <p class="text-xs text-primary-600 mt-1">587 businesses</p>
                </a>
                
                <!-- City 6 -->
                <a href="#" class="bg-gray-50 hover:bg-primary-50 rounded-lg p-4 text-center transition-all duration-300 transform hover:scale-105" data-aos="fade-up" data-aos-delay="250">
                    <h3 class="font-medium text-gray-900">Seattle</h3>
                    <p class="text-sm text-gray-500">WA</p>
                    <p class="text-xs text-primary-600 mt-1">498 businesses</p>
                </a>
                
                <!-- City 7 -->
                <a href="#" class="bg-gray-50 hover:bg-primary-50 rounded-lg p-4 text-center transition-all duration-300 transform hover:scale-105" data-aos="fade-up" data-aos-delay="300">
                    <h3 class="font-medium text-gray-900">Boston</h3>
                    <p class="text-sm text-gray-500">MA</p>
                    <p class="text-xs text-primary-600 mt-1">465 businesses</p>
                </a>
                
                <!-- City 8 -->
                <a href="#" class="bg-gray-50 hover:bg-primary-50 rounded-lg p-4 text-center transition-all duration-300 transform hover:scale-105" data-aos="fade-up" data-aos-delay="350">
                    <h3 class="font-medium text-gray-900">Denver</h3>
                    <p class="text-sm text-gray-500">CO</p>
                    <p class="text-xs text-primary-600 mt-1">432 businesses</p>
                </a>
                
                <!-- City 9 -->
                <a href="#" class="bg-gray-50 hover:bg-primary-50 rounded-lg p-4 text-center transition-all duration-300 transform hover:scale-105" data-aos="fade-up" data-aos-delay="400">
                    <h3 class="font-medium text-gray-900">Atlanta</h3>
                    <p class="text-sm text-gray-500">GA</p>
                    <p class="text-xs text-primary-600 mt-1">412 businesses</p>
                </a>
                
                <!-- City 10 -->
                <a href="#" class="bg-gray-50 hover:bg-primary-50 rounded-lg p-4 text-center transition-all duration-300 transform hover:scale-105" data-aos="fade-up" data-aos-delay="450">
                    <h3 class="font-medium text-gray-900">San Francisco</h3>
                    <p class="text-sm text-gray-500">CA</p>
                    <p class="text-xs text-primary-600 mt-1">398 businesses</p>
                </a>
                
                <!-- City 11 -->
                <a href="#" class="bg-gray-50 hover:bg-primary-50 rounded-lg p-4 text-center transition-all duration-300 transform hover:scale-105" data-aos="fade-up" data-aos-delay="500">
                    <h3 class="font-medium text-gray-900">Dallas</h3>
                    <p class="text-sm text-gray-500">TX</p>
                    <p class="text-xs text-primary-600 mt-1">376 businesses</p>
                </a>
                
                <!-- City 12 -->
                <a href="#" class="bg-gray-50 hover:bg-primary-50 rounded-lg p-4 text-center transition-all duration-300 transform hover:scale-105" data-aos="fade-up" data-aos-delay="550">
                    <h3 class="font-medium text-gray-900">Phoenix</h3>
                    <p class="text-sm text-gray-500">AZ</p>
                    <p class="text-xs text-primary-600 mt-1">345 businesses</p>
                </a>
            </div>
            
            <div class="text-center mt-12">
                <a href="#" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200 group">
                    <span>View All Cities</span>
                    <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform duration-300"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-16 md:py-24 bg-gradient-primary text-white parallax" style="background-image: url('https://via.placeholder.com/1920x1080')">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold font-montserrat mb-4">Our Growing Community</h2>
                <p class="text-lg opacity-90 max-w-3xl mx-auto">Join thousands of users and businesses who trust FindIt every day.</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center" data-aos="fade-up" data-aos-delay="0">
                    <div class="text-4xl md:text-5xl font-bold mb-2"><span class="counter-value" data-count="15000">0</span>+</div>
                    <p class="text-lg opacity-90">Businesses</p>
                </div>
                
                <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-4xl md:text-5xl font-bold mb-2"><span class="counter-value" data-count="250000">0</span>+</div>
                    <p class="text-lg opacity-90">Users</p>
                </div>
                
                <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-4xl md:text-5xl font-bold mb-2"><span class="counter-value" data-count="120000">0</span>+</div>
                    <p class="text-lg opacity-90">Reviews</p>
                </div>
                
                <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-4xl md:text-5xl font-bold mb-2"><span class="counter-value" data-count="500">0</span>+</div>
                    <p class="text-lg opacity-90">Cities</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold font-montserrat mb-4">Frequently Asked Questions</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">Find answers to common questions about using FindIt.</p>
            </div>
            
            <div class="max-w-3xl mx-auto" data-aos="fade-up">
                <div class="space-y-6">
                    <!-- FAQ Item 1 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden">
                        <button class="faq-question w-full flex items-center justify-between bg-gray-50 p-4 text-left font-medium focus:outline-none">
                            <span>How do I create an account?</span>
                            <i class="fas fa-chevron-down text-primary-600 transition-transform duration-200"></i>
                        </button>
                        <div class="faq-answer bg-white p-4 border-t border-gray-200 hidden">
                            <p class="text-gray-700">Creating an account is easy! Click on the "Sign Up" button in the top right corner of the page, fill out the registration form with your information, and verify your email address. Once verified, you can start using all the features of FindIt.</p>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 2 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden">
                        <button class="faq-question w-full flex items-center justify-between bg-gray-50 p-4 text-left font-medium focus:outline-none">
                            <span>How do I list my business on FindIt?</span>
                            <i class="fas fa-chevron-down text-primary-600 transition-transform duration-200"></i>
                        </button>
                        <div class="faq-answer bg-white p-4 border-t border-gray-200 hidden">
                            <p class="text-gray-700">To list your business, click on "List Your Business" in the navigation menu or footer. You'll need to create a business account, provide details about your business (name, address, contact information, services, etc.), and select a listing plan. Once submitted, our team will review your listing and approve it within 24-48 hours.</p>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 3 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden">
                        <button class="faq-question w-full flex items-center justify-between bg-gray-50 p-4 text-left font-medium focus:outline-none">
                            <span>How do I write a review?</span>
                            <i class="fas fa-chevron-down text-primary-600 transition-transform duration-200"></i>
                        </button>
                        <div class="faq-answer bg-white p-4 border-t border-gray-200 hidden">
                            <p class="text-gray-700">To write a review, you need to have an account and be logged in. Navigate to the business page you want to review, scroll down to the reviews section, and click on "Write a Review." Rate the business on a scale of 1-5 stars and share your experience. Your review will be published after a brief moderation process.</p>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 4 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden">
                        <button class="faq-question w-full flex items-center justify-between bg-gray-50 p-4 text-left font-medium focus:outline-none">
                            <span>Is FindIt available in my city?</span>
                            <i class="fas fa-chevron-down text-primary-600 transition-transform duration-200"></i>
                        </button>
                        <div class="faq-answer bg-white p-4 border-t border-gray-200 hidden">
                            <p class="text-gray-700">FindIt is available in most major cities across the country. You can check if your city is covered by using the search function and entering your location. If your city isn't currently covered, we're expanding rapidly and may be in your area soon!</p>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 5 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden">
                        <button class="faq-question w-full flex items-center justify-between bg-gray-50 p-4 text-left font-medium focus:outline-none">
                            <span>How can I report an incorrect listing or review?</span>
                            <i class="fas fa-chevron-down text-primary-600 transition-transform duration-200"></i>
                        </button>
                        <div class="faq-answer bg-white p-4 border-t border-gray-200 hidden">
                            <p class="text-gray-700">If you find an incorrect listing or inappropriate review, click on the "Report" button located on the business page or next to the review. Fill out the report form with details about the issue, and our moderation team will investigate and take appropriate action within 48 hours.</p>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-10">
                    <a href="#" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200 group">
                        <span>View All FAQs</span>
                        <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform duration-300"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-md overflow-hidden" data-aos="fade-up">
                <div class="grid grid-cols-1 md:grid-cols-2">
                    <div class="p-8 md:p-12">
                        <h2 class="text-2xl md:text-3xl font-bold font-montserrat mb-4">Subscribe to Our Newsletter</h2>
                        <p class="text-gray-600 mb-6">Stay updated with the latest businesses, special offers, and tips for finding the best local services.</p>
                        
                        <form id="newsletter-form" class="space-y-4">
                            <div>
                                <label for="email" class="sr-only">Email Address</label>
                                <input type="email" id="newsletter-email" placeholder="Your email address" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
                            </div>
                            <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg ripple">
                                Subscribe Now
                            </button>
                        </form>
                        
                        <p class="text-xs text-gray-500 mt-4">By subscribing, you agree to our <a href="#" class="text-primary-600 hover:underline">Privacy Policy</a> and consent to receive updates from FindIt.</p>
                    </div>
                    
                    <div class="hidden md:block bg-gradient-primary p-12 text-white flex items-center">
                        <div>
                            <h3 class="text-2xl font-bold mb-4">Why Subscribe?</h3>
                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mt-1 mr-3"></i>
                                    <span>Exclusive deals and promotions</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mt-1 mr-3"></i>
                                    <span>New business alerts in your area</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mt-1 mr-3"></i>
                                    <span>Helpful tips and guides</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mt-1 mr-3"></i>
                                    <span>Monthly newsletter with curated content</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- App Download Section -->
    <section class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right">
                    <h2 class="text-3xl md:text-4xl font-bold font-montserrat mb-4">Take FindIt With You</h2>
                    <p class="text-lg text-gray-600 mb-8">Download our mobile app to find local businesses on the go. Available for iOS and Android devices.</p>
                    
                    <div class="flex flex-wrap gap-4 mb-8">
                        <a href="#" class="flex items-center bg-black text-white rounded-lg px-4 py-3 hover:bg-gray-800 transition-all duration-300 transform hover:scale-105">
                            <i class="fab fa-apple text-2xl mr-3"></i>
                            <div>
                                <div class="text-xs">Download on the</div>
                                <div class="text-lg font-semibold">App Store</div>
                            </div>
                        </a>
                        
                        <a href="#" class="flex items-center bg-black text-white rounded-lg px-4 py-3 hover:bg-gray-800 transition-all duration-300 transform hover:scale-105">
                            <i class="fab fa-google-play text-2xl mr-3"></i>
                            <div>
                                <div class="text-xs">Get it on</div>
                                <div class="text-lg font-semibold">Google Play</div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="flex items-center space-x-6">
                        <div class="flex">
                            <i class="fas fa-star text-yellow-400"></i>
                            <i class="fas fa-star text-yellow-400"></i>
                            <i class="fas fa-star text-yellow-400"></i>
                            <i class="fas fa-star text-yellow-400"></i>
                            <i class="fas fa-star-half-alt text-yellow-400"></i>
                        </div>
                        <div class="text-gray-700">
                            <span class="font-semibold">4.8/5</span> from over 10,000 reviews
                        </div>
                    </div>
                </div>
                
                <div class="relative" data-aos="fade-left">
                    <img src="https://via.placeholder.com/600x400" alt="FindIt Mobile App" class="w-full max-w-md mx-auto rounded-xl shadow-xl">
                    
                    <!-- Floating Features -->
                    <div class="absolute top-1/4 -left-4 md:-left-8 bg-white rounded-lg shadow-lg p-3 animate-float">
                        <div class="flex items-center">
                            <div class="bg-primary-100 rounded-full p-2 mr-3">
                                <i class="fas fa-search text-primary-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-sm">Quick Search</h4>
                                <p class="text-xs text-gray-500">Find what you need fast</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="absolute top-1/2 -right-4 md:-right-8 bg-white rounded-lg shadow-lg p-3 animate-float" style="animation-delay: 0.5s;">
                        <div class="flex items-center">
                            <div class="bg-primary-100 rounded-full p-2 mr-3">
                                <i class="fas fa-map-marker-alt text-primary-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-sm">Nearby Places</h4>
                                <p class="text-xs text-gray-500">Discover local gems</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="absolute bottom-1/4 -left-4 md:-left-8 bg-white rounded-lg shadow-lg p-3 animate-float" style="animation-delay: 1s;">
                        <div class="flex items-center">
                            <div class="bg-primary-100 rounded-full p-2 mr-3">
                                <i class="fas fa-bell text-primary-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-sm">Notifications</h4>
                                <p class="text-xs text-gray-500">Stay updated</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Business Owner CTA Section -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl p-8 md:p-12 shadow-md" data-aos="fade-up">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-bold font-montserrat mb-4">Own a Business?</h2>
                        <p class="text-lg text-gray-600 mb-6">List your business on FindIt to reach thousands of potential customers searching for services like yours every day.</p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i>
                                <span class="text-gray-700">Increase your online visibility and reach more customers</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i>
                                <span class="text-gray-700">Showcase your services, hours, and contact information</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i>
                                <span class="text-gray-700">Collect and respond to customer reviews</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i>
                                <span class="text-gray-700">Access analytics and insights about your listing</span>
                            </li>
                        </ul>
                        <div class="flex flex-wrap gap-4">
                            <button id="list-business-button" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg ripple">
                                List Your Business
                            </button>
                            <button id="view-plans-button" class="bg-white hover:bg-gray-100 text-primary-600 font-medium py-3 px-6 rounded-lg border border-primary-600 transition-all duration-300 transform hover:scale-105 ripple">
                                View Plans
                            </button>
                        </div>
                    </div>
                    <div class="hidden lg:block">
                        <img src="https://via.placeholder.com/600x400" alt="Business owner" class="w-full max-w-md mx-auto rounded-lg shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center mb-6">
                        <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-primary-500 to-secondary-500 flex items-center justify-center text-white font-bold text-xl">F</div>
                        <span class="ml-2 text-2xl font-bold font-montserrat">Find<span class="text-primary-400">It</span></span>
                    </div>
                    <p class="text-gray-400 mb-4">Your trusted source for finding the best local businesses and services in your area.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-6">Quick Links</h3>
                    <ul class="space-y-3">
                        <li><a href="#home" class="text-gray-400 hover:text-white transition-colors duration-200">Home</a></li>
                        <li><a href="#categories" class="text-gray-400 hover:text-white transition-colors duration-200">Categories</a></li>
                        <li><a href="#businesses" class="text-gray-400 hover:text-white transition-colors duration-200">Businesses</a></li>
                        <li><a href="#how-it-works" class="text-gray-400 hover:text-white transition-colors duration-200">How It Works</a></li>
                        <li><a href="#testimonials" class="text-gray-400 hover:text-white transition-colors duration-200">Testimonials</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">FAQ</a></li>
                    </ul>
                </div>
                
                <!-- For Business -->
                <div>
                    <h3 class="text-lg font-semibold mb-6">For Business</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">List Your Business</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Pricing Plans</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Business Dashboard</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Resources</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Success Stories</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-6">Contact Us</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-primary-400"></i>
                            <span class="text-gray-400">123 Business Avenue, Suite 100<br>New York, NY 10001</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-3 text-primary-400"></i>
                            <span class="text-gray-400">(555) 123-4567</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-primary-400"></i>
                            <span class="text-gray-400">info@findit.com</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock mr-3 text-primary-400"></i>
                            <span class="text-gray-400">Mon-Fri: 9AM - 5PM</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="border-gray-800 mb-8">
            
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-500 text-sm mb-4 md:mb-0">
                    &copy; 2023 FindIt. All rights reserved.
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-500 hover:text-white text-sm transition-colors duration-200">Terms of Service</a>
                    <a href="#" class="text-gray-500 hover:text-white text-sm transition-colors duration-200">Privacy Policy</a>
                    <a href="#" class="text-gray-500 hover:text-white text-sm transition-colors duration-200">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-6 right-6 bg-primary-600 text-white rounded-full p-3 shadow-lg hidden hover:bg-primary-700 transition-all duration-300 z-50 transform hover:scale-110">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Modals -->
    <!-- Login Modal -->
    <div id="login-modal" class="modal fixed inset-0 flex items-center justify-center z-50 hidden opacity-0">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="modal-content relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform scale-90">
            <button class="modal-close absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
            <div class="p-8">
                <h2 class="text-2xl font-bold mb-6 text-center">Login to Your Account</h2>
                <form id="login-form" class="space-y-4">
                    <div>
                        <label for="login-email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="login-email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                    <div>
                        <label for="login-password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="login-password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember-me" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-700">Remember me</label>
                        </div>
                        <a href="#" class="text-sm text-primary-600 hover:text-primary-700">Forgot password?</a>
                    </div>
                    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">Login</button>
                </form>
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">Don't have an account? <button id="switch-to-signup" class="text-primary-600 hover:text-primary-700 font-medium">Sign up</button></p>
                </div>
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Or continue with</span>
                        </div>
                    </div>
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <button class="flex justify-center items-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <i class="fab fa-google text-red-500 mr-2"></i> Google
                        </button>
                        <button class="flex justify-center items-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <i class="fab fa-facebook-f text-blue-600 mr-2"></i> Facebook
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Signup Modal -->
    <div id="signup-modal" class="modal fixed inset-0 flex items-center justify-center z-50 hidden opacity-0">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="modal-content relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform scale-90">
            <button class="modal-close absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
            <div class="p-8">
                <h2 class="text-2xl font-bold mb-6 text-center">Create an Account</h2>
                <form id="signup-form" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="first-name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" id="first-name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                        </div>
                        <div>
                            <label for="last-name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" id="last-name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                        </div>
                    </div>
                    <div>
                        <label for="signup-email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="signup-email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                    <div>
                        <label for="signup-password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="signup-password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                    <div>
                        <label for="confirm-password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" id="confirm-password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="terms" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded" required>
                        <label for="terms" class="ml-2 block text-sm text-gray-700">I agree to the <a href="#" class="text-primary-600 hover:text-primary-700">Terms of Service</a> and <a href="#" class="text-primary-600 hover:text-primary-700">Privacy Policy</a></label>
                    </div>
                    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">Sign Up</button>
                </form>
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">Already have an account? <button id="switch-to-login" class="text-primary-600 hover:text-primary-700 font-medium">Login</button></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Details Modal -->
    <div id="business-modal" class="modal fixed inset-0 flex items-center justify-center z-50 hidden opacity-0">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="modal-content relative bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 transform scale-90 max-h-[90vh] overflow-y-auto">
            <button class="modal-close absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-10">
                <i class="fas fa-times"></i>
            </button>
            <div class="p-0">
                <div class="relative">
                    <img src="https://via.placeholder.com/1200x400" alt="Business Cover" class="w-full h-64 object-cover">
                    <div class="absolute bottom-4 left-8 flex items-center">
                        <div class="bg-white rounded-lg p-2 shadow-lg">
                            <img src="https://via.placeholder.com/100x100" alt="Business Logo" class="w-20 h-20 object-cover rounded-lg">
                        </div>
                        <div class="ml-4">
                            <h2 class="text-2xl font-bold text-white drop-shadow-lg">Coastal Cuisine</h2>
                            <div class="flex items-center">
                                <div class="flex text-yellow-400">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <span class="ml-2 text-white drop-shadow-lg">4.8 (124 reviews)</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-8">
                    <div class="flex flex-wrap gap-2 mb-6">
                        <span class="px-3 py-1 bg-primary-100 text-primary-800 rounded-full text-sm">Restaurant</span>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Open Now</span>
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Reservations</span>
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">Outdoor Seating</span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="md:col-span-2">
                            <h3 class="text-xl font-bold mb-4">About</h3>
                            <p class="text-gray-700 mb-6">Coastal Cuisine offers a delightful dining experience with fresh seafood and locally sourced ingredients. Our menu changes seasonally to reflect the best available produce, and our expert chefs create innovative dishes that highlight the natural flavors of each ingredient.</p>
                            
                            <h3 class="text-xl font-bold mb-4">Photos</h3>
                            <div class="grid grid-cols-3 gap-4 mb-6">
                                <img src="https://via.placeholder.com/300x200" alt="Restaurant Photo 1" class="rounded-lg w-full h-32 object-cover">
                                <img src="https://via.placeholder.com/300x200" alt="Restaurant Photo 2" class="rounded-lg w-full h-32 object-cover">
                                <img src="https://via.placeholder.com/300x200" alt="Restaurant Photo 3" class="rounded-lg w-full h-32 object-cover">
                            </div>
                            
                            <h3 class="text-xl font-bold mb-4">Reviews</h3>
                            <div class="space-y-4">
                                <div class="border-b border-gray-200 pb-4">
                                    <div class="flex items-center mb-2">
                                        <img src="https://via.placeholder.com/50x50" alt="Reviewer" class="w-10 h-10 rounded-full mr-3">
                                        <div>
                                            <div class="font-medium">John D.</div>
                                            <div class="text-gray-500 text-sm">2 days ago</div>
                                        </div>
                                        <div class="ml-auto flex text-yellow-400">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                    </div>
                                    <p class="text-gray-700">Amazing food and atmosphere! The seafood pasta was incredible, and the service was top-notch. Will definitely be coming back soon.</p>
                                </div>
                                
                                <div class="border-b border-gray-200 pb-4">
                                    <div class="flex items-center mb-2">
                                        <img src="https://via.placeholder.com/50x50" alt="Reviewer" class="w-10 h-10 rounded-full mr-3">
                                        <div>
                                            <div class="font-medium">Sarah M.</div>
                                            <div class="text-gray-500 text-sm">1 week ago</div>
                                        </div>
                                        <div class="ml-auto flex text-yellow-400">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                    </div>
                                    <p class="text-gray-700">Great place for a date night. The ambiance is perfect, and the food is delicious. Slightly pricey but worth it for a special occasion.</p>
                                </div>
                            </div>
                            
                            <a href="#" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium mt-4">
                                <span>View all 124 reviews</span>
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                        
                        <div>
                            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                                <h3 class="text-lg font-bold mb-4">Contact Information</h3>
                                <ul class="space-y-3">
                                    <li class="flex items-start">
                                        <i class="fas fa-map-marker-alt text-primary-600 mt-1 mr-3"></i>
                                        <span>123 Ocean Drive, San Francisco, CA 94107</span>
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-phone text-primary-600 mr-3"></i>
                                        <span>(415) 555-1234</span>
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-globe text-primary-600 mr-3"></i>
                                        <a href="#" class="text-primary-600 hover:underline">www.coastalcuisine.com</a>
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-envelope text-primary-600 mr-3"></i>
                                        <a href="#" class="text-primary-600 hover:underline">info@coastalcuisine.com</a>
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                                <h3 class="text-lg font-bold mb-4">Business Hours</h3>
                                <ul class="space-y-2">
                                    <li class="flex justify-between">
                                        <span>Monday</span>
                                        <span>11:00 AM - 10:00 PM</span>
                                    </li>
                                    <li class="flex justify-between">
                                        <span>Tuesday</span>
                                        <span>11:00 AM - 10:00 PM</span>
                                    </li>
                                    <li class="flex justify-between">
                                        <span>Wednesday</span>
                                        <span>11:00 AM - 10:00 PM</span>
                                    </li>
                                    <li class="flex justify-between">
                                        <span>Thursday</span>
                                        <span>11:00 AM - 10:00 PM</span>
                                    </li>
                                    <li class="flex justify-between font-medium">
                                        <span>Friday</span>
                                        <span>11:00 AM - 11:00 PM</span>
                                    </li>
                                    <li class="flex justify-between font-medium">
                                        <span>Saturday</span>
                                        <span>11:00 AM - 11:00 PM</span>
                                    </li>
                                    <li class="flex justify-between">
                                        <span>Sunday</span>
                                        <span>12:00 PM - 9:00 PM</span>
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="flex flex-col space-y-3">
                                <button class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300 flex items-center justify-center">
                                    <i class="fas fa-phone mr-2"></i> Call Now
                                </button>
                                <button class="bg-white border border-primary-600 text-primary-600 hover:bg-primary-50 font-medium py-2 px-4 rounded-lg transition-all duration-300 flex items-center justify-center">
                                    <i class="fas fa-directions mr-2"></i> Get Directions
                                </button>
                                <button class="bg-white border border-primary-600 text-primary-600 hover:bg-primary-50 font-medium py-2 px-4 rounded-lg transition-all duration-300 flex items-center justify-center">
                                    <i class="fas fa-bookmark mr-2"></i> Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="success-modal" class="modal fixed inset-0 flex items-center justify-center z-50 hidden opacity-0">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="modal-content relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform scale-90">
            <div class="p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                    <i class="fas fa-check text-2xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Success!</h3>
                <p class="text-gray-600 mb-6" id="success-message">Your action was completed successfully.</p>
                <button class="modal-close bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200 mx-auto">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Initialize AOS animations
        AOS.init({
            once: true,
            duration: 800,
            offset: 100,
        });
        
        // Mobile Menu Toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
        
        // Smooth Scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    if (!mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                    }
                }
            });
        });
        
        // Back to Top Button
        const backToTopButton = document.getElementById('back-to-top');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.remove('hidden');
            } else {
                backToTopButton.classList.add('hidden');
            }
        });
        
        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Scroll Indicator
        const scrollIndicator = document.querySelector('.scroll-indicator');
        
        window.addEventListener('scroll', () => {
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            const scrollTop = window.pageYOffset;
            
            const scrollPercentage = (scrollTop / (documentHeight - windowHeight)) * 100;
            scrollIndicator.style.width = scrollPercentage + '%';
        });
        
        // Testimonial Slider
        const testimonialSlider = new Swiper('.testimonial-slider .swiper-container', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.testimonial-next',
                prevEl: '.testimonial-prev',
            }
        });
        
        // FAQ Accordion
        const faqQuestions = document.querySelectorAll('.faq-question');
        
        faqQuestions.forEach(question => {
            question.addEventListener('click', () => {
                const answer = question.nextElementSibling;
                const icon = question.querySelector('i');
                
                // Toggle current FAQ
                answer.classList.toggle('hidden');
                icon.classList.toggle('rotate-180');
                
                // Close other FAQs
                faqQuestions.forEach(otherQuestion => {
                    if (otherQuestion !== question) {
                        const otherAnswer = otherQuestion.nextElementSibling;
                        const otherIcon = otherQuestion.querySelector('i');
                        
                        otherAnswer.classList.add('hidden');
                        otherIcon.classList.remove('rotate-180');
                    }
                });
            });
        });
        
        // Tabs
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tabId = button.getAttribute('data-tab');
                
                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => {
                    btn.classList.remove('active');
                    btn.classList.remove('bg-primary-600');
                    btn.classList.remove('text-white');
                    btn.classList.add('bg-gray-200');
                    btn.classList.add('text-gray-700');
                });
                
                tabContents.forEach(content => {
                    content.classList.remove('active');
                });
                
                // Add active class to clicked button and corresponding content
                button.classList.add('active');
                button.classList.add('bg-primary-600');
                button.classList.add('text-white');
                button.classList.remove('bg-gray-200');
                button.classList.remove('text-gray-700');
                
                document.getElementById(tabId).classList.add('active');
            });
        });
        
        // Counter Animation
        const counterElements = document.querySelectorAll('.counter-value');
        let countersStarted = false;
        
        function startCounters() {
            if (countersStarted) return;
            
            counterElements.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-count'));
                const duration = 2000; // 2 seconds
                const step = target / (duration / 16); // 16ms is roughly 60fps
                let current = 0;
                
                const updateCounter = () => {
                    current += step;
                    if (current < target) {
                        counter.textContent = Math.floor(current).toLocaleString();
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target.toLocaleString();
                    }
                };
                
                updateCounter();
            });
            
            countersStarted = true;
        }
        
        // Start counters when they come into view
        const statsSection = document.querySelector('.bg-gradient-primary');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    startCounters();
                }
            });
        }, { threshold: 0.1 });
        
        if (statsSection) {
            observer.observe(statsSection);
        }
        
        // Modal Functionality
        const modals = document.querySelectorAll('.modal');
        const modalTriggers = {
            'login-button': 'login-modal',
            'mobile-login-button': 'login-modal',
            'signup-button': 'signup-modal',
            'mobile-signup-button': 'signup-modal',
            'switch-to-signup': 'signup-modal',
            'switch-to-login': 'login-modal',
            'learn-more-button': 'success-modal',
            'about-button': 'success-modal',
            'list-business-button': 'success-modal',
            'view-plans-button': 'success-modal'
        };
        
        // Open modal
        Object.keys(modalTriggers).forEach(triggerId => {
            const trigger = document.getElementById(triggerId);
            const modalId = modalTriggers[triggerId];
            
            if (trigger) {
                trigger.addEventListener('click', () => {
                    const modal = document.getElementById(modalId);
                    
                    // Special case for success modal
                    if (modalId === 'success-modal') {
                        const successMessage = document.getElementById('success-message');
                        
                        if (triggerId === 'learn-more-button') {
                            successMessage.textContent = 'Thank you for your interest! We\'ll be in touch soon.';
                        } else if (triggerId === 'about-button') {
                            successMessage.textContent = 'You\'ll be redirected to our About page shortly.';
                        } else if (triggerId === 'list-business-button') {
                            successMessage.textContent = 'Thank you for your interest in listing your business!';
                        } else if (triggerId === 'view-plans-button') {
                            successMessage.textContent = 'Our pricing plans information has been sent to your email.';
                        }
                    }
                    
                    // Close all other modals
                    modals.forEach(m => {
                        m.classList.add('hidden');
                        m.classList.remove('active');
                    });
                    
                    // Open this modal
                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        modal.classList.add('active');
                        modal.classList.add('opacity-100');
                    }, 10);
                });
            }
        });
        
        // Close modal
        const modalCloseButtons = document.querySelectorAll('.modal-close');
        
        modalCloseButtons.forEach(button => {
            button.addEventListener('click', () => {
                const modal = button.closest('.modal');
                modal.classList.remove('active');
                modal.classList.remove('opacity-100');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            });
        });
        
        // Close modal when clicking outside
        modals.forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                    modal.classList.remove('opacity-100');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                    }, 300);
                }
            });
        });
        
        // Business Modal
        const businessLinks = document.querySelectorAll('.business-link');
        const businessModal = document.getElementById('business-modal');
        
        businessLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Open business modal
                businessModal.classList.remove('hidden');
                setTimeout(() => {
                    businessModal.classList.add('active');
                    businessModal.classList.add('opacity-100');
                }, 10);
            });
        });
        
        // Form Submissions
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                
                // Show success modal
                const successModal = document.getElementById('success-modal');
                const successMessage = document.getElementById('success-message');
                
                if (form.id === 'login-form') {
                    successMessage.textContent = 'You have successfully logged in!';
                } else if (form.id === 'signup-form') {
                    successMessage.textContent = 'Your account has been created successfully!';
                } else if (form.id === 'newsletter-form') {
                    successMessage.textContent = 'Thank you for subscribing to our newsletter!';
                } else if (form.id === 'search-form') {
                    successMessage.textContent = 'Search results are being loaded...';
                }
                
                // Close all other modals
                modals.forEach(modal => {
                    modal.classList.remove('active');
                    modal.classList.remove('opacity-100');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                    }, 300);
                });
                
                // Reset form
                form.reset();
                
                // Open success modal
                successModal.classList.remove('hidden');
                setTimeout(() => {
                    successModal.classList.add('active');
                    successModal.classList.add('opacity-100');
                }, 10);
            });
        });
    </script>
</body>
</html>