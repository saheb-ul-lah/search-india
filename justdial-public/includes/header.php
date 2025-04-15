<?php
// justdial-public/includes/header.php
include_once 'db.php'; // Ensure $pdo is available
include_once 'functions.php'; // Ensure helper functions are available

// Fetch settings for use in the header
$site_name = get_setting('site_name', 'FindIt');
$site_tagline = get_setting('site_tagline', 'Your Local Business Directory');
$primary_color = get_setting('primary_color', '#0ea5e9'); // Default primary color
$secondary_color = get_setting('secondary_color', '#c026d3'); // Default secondary color
$accent_color = get_setting('accent_color', '#f97316'); // Default accent color
$site_logo_filename = get_setting('logo'); // Get logo filename from settings
$site_logo_url = $site_logo_filename ? get_image_url('logos', $site_logo_filename, 'assets/img/logo.png') : 'assets/img/logo.png'; // Adjust placeholder path if needed

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php safe_echo($site_name); ?> - <?php safe_echo($site_tagline); ?></title>
    <meta name="description" content="<?php safe_echo($site_tagline); ?>">

    <!-- Favicon -->
    <link rel="icon" href="<?php echo get_setting('site_favicon', 'https://via.placeholder.com/32'); ?>" type="image/png"> <!-- Consider using get_image_url if favicon is uploaded -->

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Configure Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { // Using shades from static config, could dynamically generate if needed
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '<?php echo $primary_color; ?>',
                            600: '<?php echo $primary_color; ?>', // Use setting for 500/600
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        secondary: { // Similar logic for secondary
                            50: '#fdf4ff',
                            100: '#fae8ff',
                            200: '#f5d0fe',
                            300: '#f0abfc',
                            400: '#e879f9',
                            500: '<?php echo $secondary_color; ?>',
                            600: '<?php echo $secondary_color; ?>',
                            700: '#a21caf',
                            800: '#86198f',
                            900: '#701a75',
                        },
                        accent: { // Similar logic for accent
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '<?php echo $accent_color; ?>',
                            600: '<?php echo $accent_color; ?>',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        },
                    },
                    fontFamily: {
                        'montserrat': ['Montserrat', 'sans-serif'],
                        'poppins': ['Poppins', 'sans-serif'],
                    },
                    // Keep animations/keyframes as they are less likely to be dynamic settings
                    animation: {
                        /* ... existing animation config ... */
                    },
                    keyframes: {
                        /* ... existing keyframes config ... */
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
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Optional custom CSS -->
    <style>
        /* Keep existing custom styles here or move to style.css */
        body {
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Montserrat', sans-serif;
        }

        .hero-gradient {
            background: linear-gradient(135deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
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
            background-image: linear-gradient(to right, <?php echo $primary_color; ?>, <?php echo $secondary_color; ?>);
        }

        .bg-gradient-light {
            background: linear-gradient(135deg, #f0f9ff 0%, #fdf4ff 100%);
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
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

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: <?php echo $primary_color; ?>;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: <?php echo $primary_color; ?>;
        }

        /* Adjust hover color if needed */
        .parallax {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .floating {
            animation: float 3s ease-in-out infinite;
        }

        .floating-delay-1 {
            animation-delay: 0.5s;
        }

        .floating-delay-2 {
            animation-delay: 1s;
        }

        .glow {
            box-shadow: 0 0 15px rgba(14, 165, 233, 0.5);
            transition: box-shadow 0.3s ease;
        }

        /* Consider using primary color alpha */
        .glow:hover {
            box-shadow: 0 0 25px rgba(14, 165, 233, 0.8);
        }

        /* Consider using primary color alpha */
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

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s ease-out forwards;
        }

        .counter-value {
            transition: all 0.5s ease;
        }

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

        /* Consider using primary color alpha */
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

        .animated-bg {
            background: linear-gradient(-45deg, <?php echo $primary_color; ?>, <?php echo $secondary_color; ?>, <?php echo $accent_color; ?>, <?php echo $primary_color; ?>);
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

        .scroll-indicator {
            height: 3px;
            background: linear-gradient(to right, <?php echo $primary_color; ?>, <?php echo $secondary_color; ?>);
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
                    <a href="index.php" class="flex items-center">
                        <!-- Dynamic Logo Option 1: Image -->
                        <?php if ($site_logo_filename): ?>
                            <img src="<?php echo $site_logo_url; ?>" alt="<?php safe_echo($site_name); ?> Logo" class="h-10 mr-2">
                        <?php else: ?>
                            <!-- Dynamic Logo Option 2: Initial Box (Fallback) -->
                            <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-primary-500 to-secondary-500 flex items-center justify-center text-white font-bold text-xl mr-2">
                                <?php echo strtoupper(substr($site_name, 0, 1)); ?>
                            </div>
                        <?php endif; ?>

                        <!-- <span class="text-2xl font-bold text-gray-900 font-montserrat"><?php echo preg_replace('/(It)$/i', '<span class="text-primary-600">$1</span>', $site_name);?></span> -->
                        <span class="text-xl font-bold text-gray-900 font-montserrat">Search India</span>
                    </a>
                </div>

                <!-- Navigation - Desktop -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="index.php#home" class="text-gray-900 hover:text-primary-600 font-medium transition-colors duration-200">Home</a>
                    <a href="index.php#categories" class="text-gray-900 hover:text-primary-600 font-medium transition-colors duration-200">Categories</a>
                    <a href="index.php#businesses" class="text-gray-900 hover:text-primary-600 font-medium transition-colors duration-200">Businesses</a>
                    <a href="index.php#how-it-works" class="text-gray-900 hover:text-primary-600 font-medium transition-colors duration-200">How It Works</a>
                    <a href="index.php#testimonials" class="text-gray-900 hover:text-primary-600 font-medium transition-colors duration-200">Testimonials</a>
                    <!-- Add links to other pages like categories.php, cities.php here later -->
                </nav>

                <!-- Auth Buttons - Desktop -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="../justdial-admin/modules/auth/login.php" class="text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200">Login</a>
                    <a href="../justdial-admin/modules/auth/signup.php" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300">Sign Up</a>
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
                    <a href="index.php#home" class="text-gray-900 hover:text-primary-600 font-medium py-2 transition-colors duration-200">Home</a>
                    <a href="index.php#categories" class="text-gray-900 hover:text-primary-600 font-medium py-2 transition-colors duration-200">Categories</a>
                    <a href="index.php#businesses" class="text-gray-900 hover:text-primary-600 font-medium py-2 transition-colors duration-200">Businesses</a>
                    <a href="index.php#how-it-works" class="text-gray-900 hover:text-primary-600 font-medium py-2 transition-colors duration-200">How It Works</a>
                    <a href="index.php#testimonials" class="text-gray-900 hover:text-primary-600 font-medium py-2 transition-colors duration-200">Testimonials</a>
                    <div class="flex space-x-4 pt-2">
                        <button id="mobile-login-button" class="text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200">Login</button>
                        <button id="mobile-signup-button" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">Sign Up</button>
                    </div>
                </div>
            </div>
        </div>
    </header>