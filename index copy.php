<?php
// FILE: justdial-public/index.php

// --- Configuration & Functions ---
// Adjust path if your config folder is elsewhere (e.g., one level up)
require_once __DIR__ . '/../justdial-admin/config/config.php';
require_once __DIR__ . '/../justdial-admin/config/functions.php'; // Make sure db(), getSetting(), formatDate() etc. are here

// Ensure session is started if needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Database Interaction & Data Fetching ---
$db = db();
$popularCategories = [];
$featuredBusinesses = [];
$recentReviews = [];
$popularCities = [];
$stats = ['business_count' => 0, 'user_count' => 0, 'review_count' => 0, 'category_count' => 0];
$siteName = getSetting('site_name', 'FindIt'); // Get site name or use default
$uploadsBaseUrl = rtrim(UPLOADS_URL, '/') ?: '/justdial-admin/uploads'; // Define base URL for uploads, adjust if needed

try {
    // Fetch popular categories
    // NOTE: 'business_count' column probably doesn't exist on categories.
    //       Removing it from SELECT for now. You'd need a subquery or maintain the count separately.
    $categoryQuery = "SELECT id, name, icon, slug FROM categories
                     WHERE status = 'active' AND parent_id IS NULL
                     ORDER BY sort_order ASC, name ASC LIMIT 8"; // Removed business_count order, adjusted limit
    $db->query($categoryQuery);
    $popularCategories = $db->resultSet();

    // Fetch featured businesses
    // NOTE: businesses table doesn't have category_id directly. Need JOIN via business_categories.
    // NOTE: businesses table doesn't have average_rating directly. Need subquery.
    $businessQuery = "SELECT b.id, b.name, b.slug, b.logo, b.cover_image, b.address, b.city, b.state, b.phone,
                     (SELECT ROUND(AVG(rating), 1) FROM reviews r WHERE r.business_id = b.id AND r.status = 'approved') as average_rating,
                     (SELECT COUNT(*) FROM reviews r WHERE r.business_id = b.id AND r.status = 'approved') as review_count,
                     (SELECT c.name FROM categories c JOIN business_categories bc ON c.id = bc.category_id WHERE bc.business_id = b.id ORDER BY c.name LIMIT 1) as category_name,
                     (SELECT c.slug FROM categories c JOIN business_categories bc ON c.id = bc.category_id WHERE bc.business_id = b.id ORDER BY c.name LIMIT 1) as category_slug
                     FROM businesses b
                     WHERE b.status = 'active' AND b.is_featured = 1
                     GROUP BY b.id -- Grouping needed due to potential multiple categories if not limited in subquery
                     ORDER BY average_rating DESC, review_count DESC
                     LIMIT 6";
    $db->query($businessQuery);
    $featuredBusinesses = $db->resultSet();


    // Fetch recent reviews
    $reviewQuery = "SELECT r.id, r.rating, r.comment, r.created_at,
                   u.name as user_name, u.profile_image as user_image,
                   b.name as business_name, b.slug as business_slug
                   FROM reviews r
                   JOIN users u ON r.user_id = u.id
                   JOIN businesses b ON r.business_id = b.id
                   WHERE r.status = 'approved'
                   ORDER BY r.created_at DESC
                   LIMIT 5";
    $db->query($reviewQuery);
    $recentReviews = $db->resultSet();


    // Fetch popular cities
    // NOTE: businesses has 'city' (varchar), cities has 'name'. Joining on name and state.
    //       It's much better if businesses had a city_id FK.
    //       Also adding ci.id to GROUP BY
    $cityQuery = "SELECT c.id, c.name, c.state, COUNT(b.id) as business_count
                 FROM cities c
                 LEFT JOIN businesses b ON c.name = b.city -- Joining by name, ensure consistency
                 WHERE c.status = 'active'
                 GROUP BY c.id, c.name, c.state -- Group by all selected non-aggregate columns
                 ORDER BY business_count DESC, c.name ASC
                 LIMIT 12";
    $db->query($cityQuery);
    $popularCities = $db->resultSet();


    // Get statistics
    $statsQuery = "SELECT
                  (SELECT COUNT(*) FROM businesses WHERE status = 'active') as business_count,
                  (SELECT COUNT(*) FROM users WHERE status = 'active') as user_count,
                  (SELECT COUNT(*) FROM reviews WHERE status = 'approved') as review_count,
                  (SELECT COUNT(*) FROM categories WHERE status = 'active') as category_count";
    $db->query($statsQuery);
    $statsResult = $db->single();
    $stats = $statsResult ?: $stats; // Use defaults if query fails

} catch (Exception $e) {
    error_log("Landing Page DB Error: " . $e->getMessage());
    // Avoid breaking the page, show default empty data
}

// --- Helper Functions (Ideally move to functions.php or includes/helpers.php) ---
if (!function_exists('formatPhoneNumber')) {
    function formatPhoneNumber($phoneNumber) {
        if(empty($phoneNumber)) return 'N/A';
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);
        // Basic India format check (10 digits) - adjust if needed
        if(strlen($cleaned) == 10) {
           // Format as desired, e.g., xxxxx xxxxx or add +91 etc.
           return htmlspecialchars($phoneNumber); // Keep original for now
        }
        return htmlspecialchars($phoneNumber);
    }
}
if (!function_exists('getInitials')) {
    function getInitials($name) {
        $words = explode(" ", trim($name));
        $initials = "";
        if (!empty($words[0]) && isset($words[0][0])) $initials .= strtoupper($words[0][0]);
        if (count($words) > 1 && !empty($words[count($words)-1]) && isset($words[count($words)-1][0])) $initials .= strtoupper($words[count($words)-1][0]);
        return $initials ?: '?';
    }
}

// --- Set Page Title Before Header ---
$pageTitle = 'Find Local Businesses, Shops & Services Near You | ' . htmlspecialchars($siteName);

// --- Include Client Header ---
// Make sure this header file exists in 'justdial-public/includes/'
// And that it doesn't try to include admin-specific things like sidebar.php
require_once __DIR__ . '/includes/header.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindIt - Your Local Business Directory</title>
    <meta name="description" content="Find the best local businesses, services, restaurants, and more in your city. Read reviews, get contact information, and discover new places with FindIt.">
    
    <!-- Favicon -->
    <link rel="icon" href="assets/images/favicon.png" type="image/png">
    
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
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
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
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
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
    
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #0ea5e9 0%, #6d28d9 100%);
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
            background-image: linear-gradient(to right, #0ea5e9, #6d28d9);
        }
        
        .bg-gradient-light {
            background: linear-gradient(135deg, #f0f9ff 0%, #f5f3ff 100%);
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #6d28d9 100%);
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
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fadeInUp {
            animation: fadeInUp 0.5s ease-out;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="index.php" class="flex items-center">
                        <img src="assets/images/logo.png" alt="FindIt Logo" class="h-10 w-auto">
                        <span class="ml-2 text-2xl font-bold text-gray-900 font-montserrat">Find<span class="text-primary-600">It</span></span>
                    </a>
                </div>
                
                <!-- Navigation - Desktop -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-gray-900 hover:text-primary-600 font-medium transition-colors duration-200">Home</a>
                    <a href="categories.php" class="text-gray-900 hover:text-primary-600 font-medium transition-colors duration-200">Categories</a>
                    <a href="businesses.php" class="text-gray-900 hover:text-primary-600 font-medium transition-colors duration-200">Businesses</a>
                    <a href="about.php" class="text-gray-900 hover:text-primary-600 font-medium transition-colors duration-200">About</a>
                    <a href="contact.php" class="text-gray-900 hover:text-primary-600 font-medium transition-colors duration-200">Contact</a>
                </nav>
                
                <!-- Auth Buttons - Desktop -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="login.php" class="text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200">Login</a>
                    <a href="register.php" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">Sign Up</a>
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
                    <a href="index.php" class="text-gray-900 hover:text-primary-600 font-medium py-2 transition-colors duration-200">Home</a>
                    <a href="categories.php" class="text-gray-900 hover:text-primary-600 font-medium py-2 transition-colors duration-200">Categories</a>
                    <a href="businesses.php" class="text-gray-900 hover:text-primary-600 font-medium py-2 transition-colors duration-200">Businesses</a>
                    <a href="about.php" class="text-gray-900 hover:text-primary-600 font-medium py-2 transition-colors duration-200">About</a>
                    <a href="contact.php" class="text-gray-900 hover:text-primary-600 font-medium py-2 transition-colors duration-200">Contact</a>
                    <div class="flex space-x-4 pt-2">
                        <a href="login.php" class="text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200">Login</a>
                        <a href="register.php" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">Sign Up</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-gradient text-white relative overflow-hidden">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right" data-aos-duration="1000">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold font-montserrat leading-tight mb-6">Find the Best Local Businesses Near You</h1>
                    <p class="text-lg md:text-xl opacity-90 mb-8">Discover top-rated restaurants, services, shops, and more in your area. Read reviews, get directions, and make informed decisions.</p>
                    
                    <!-- Search Bar -->
                    <div class="bg-white rounded-lg p-2 search-bar-shadow mb-8">
                        <form action="search.php" method="GET" class="flex flex-col md:flex-row">
                            <div class="flex-1 mb-2 md:mb-0 md:mr-2">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                    <input type="text" name="q" placeholder="What are you looking for?" class="w-full pl-10 pr-4 py-3 rounded-lg border-0 focus:ring-2 focus:ring-primary-500 outline-none" required>
                                </div>
                            </div>
                            <div class="flex-1 mb-2 md:mb-0 md:mr-2">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-map-marker-alt text-gray-400"></i>
                                    </div>
                                    <input type="text" name="location" placeholder="Location" class="w-full pl-10 pr-4 py-3 rounded-lg border-0 focus:ring-2 focus:ring-primary-500 outline-none">
                                </div>
                            </div>
                            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center">
                                <span>Search</span>
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </form>
                    </div>
                    
                    <!-- Popular Searches -->
                    <div class="flex flex-wrap items-center">
                        <span class="text-white opacity-90 mr-3 mb-2">Popular:</span>
                        <a href="search.php?q=restaurants" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-full px-4 py-1 text-sm mr-2 mb-2 transition-colors duration-200">Restaurants</a>
                        <a href="search.php?q=hotels" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-full px-4 py-1 text-sm mr-2 mb-2 transition-colors duration-200">Hotels</a>
                        <a href="search.php?q=plumbers" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-full px-4 py-1 text-sm mr-2 mb-2 transition-colors duration-200">Plumbers</a>
                        <a href="search.php?q=electricians" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-full px-4 py-1 text-sm mr-2 mb-2 transition-colors duration-200">Electricians</a>
                    </div>
                </div>
                
                <div class="hidden lg:block" data-aos="fade-left" data-aos-duration="1000">
                    <img src="assets/images/hero-illustration.svg" alt="Find local businesses" class="w-full max-w-lg mx-auto animate-float">
                </div>
            </div>
        </div>
        
        <!-- Animated Shapes -->
        <div class="absolute top-0 right-0 -mt-16 -mr-16 opacity-50 lg:opacity-70">
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
    </section>

    <!-- Popular Categories Section -->
    <section class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold font-montserrat mb-4">Popular Categories</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">Explore our most popular categories to find exactly what you're looking for.</p>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6 md:gap-8">
                <?php foreach ($popularCategories as $index => $category): ?>
                <a href="category.php?slug=<?= htmlspecialchars($category['slug']) ?>" class="category-card bg-white rounded-xl shadow-md hover:shadow-xl p-6 text-center card-hover transition-all duration-300" data-aos="fade-up" data-aos-delay="<?= $index * 50 ?>">
                    <div class="bg-primary-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="<?= htmlspecialchars($category['icon']) ?> text-primary-600 text-2xl category-icon"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2"><?= htmlspecialchars($category['name']) ?></h3>
                    <p class="text-gray-500 text-sm"><?= number_format($category['business_count']) ?> listings</p>
                </a>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-12">
                <a href="categories.php" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200">
                    <span>View All Categories</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Businesses Section -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold font-montserrat mb-4">Featured Businesses</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">Discover top-rated businesses in your area that have been verified for quality and service.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($featuredBusinesses as $index => $business): ?>
                <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                    <div class="relative">
                        <img src="uploads/businesses/<?= !empty($business['logo']) ? htmlspecialchars($business['logo']) : 'default-business.jpg' ?>" alt="<?= htmlspecialchars($business['name']) ?>" class="w-full h-48 object-cover">
                        <div class="absolute top-4 left-4">
                            <span class="bg-primary-600 text-white text-xs font-semibold px-3 py-1 rounded-full">Featured</span>
                        </div>
                        <div class="absolute top-4 right-4">
                            <span class="bg-white text-gray-800 text-xs font-semibold px-3 py-1 rounded-full shadow-md">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <?= number_format($business['average_rating'], 1) ?>
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-2">
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 rounded-full px-3 py-1"><?= htmlspecialchars($business['category_name']) ?></span>
                        </div>
                        <h3 class="text-xl font-bold mb-2 hover:text-primary-600 transition-colors duration-200">
                            <a href="business.php?slug=<?= htmlspecialchars($business['slug']) ?>"><?= htmlspecialchars($business['name']) ?></a>
                        </h3>
                        <div class="flex items-center text-gray-500 text-sm mb-4">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span><?= htmlspecialchars($business['city']) ?>, <?= htmlspecialchars($business['state']) ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-phone text-primary-600 mr-2"></i>
                                <span class="text-gray-700"><?= formatPhoneNumber($business['phone']) ?></span>
                            </div>
                            <span class="text-sm text-gray-500"><?= $business['review_count'] ?> reviews</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-12">
                <a href="businesses.php" class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-8 rounded-lg transition-colors duration-200">
                    Explore All Businesses
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold font-montserrat mb-4">How It Works</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">Finding the perfect local business has never been easier.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
                <div class="text-center" data-aos="fade-up" data-aos-delay="0">
                    <div class="bg-primary-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-search text-primary-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Search</h3>
                    <p class="text-gray-600">Enter what you're looking for and your location to find the best matches in your area.</p>
                </div>
                
                <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="bg-primary-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-star text-primary-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Compare</h3>
                    <p class="text-gray-600">Read reviews, check ratings, and compare services to find the perfect match for your needs.</p>
                </div>
                
                <div class="text-center" data-aos="fade-up" data-aos-delay="400">
                    <div class="bg-primary-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-check-circle text-primary-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Connect</h3>
                    <p class="text-gray-600">Contact businesses directly, get directions, or book services online with just a few clicks.</p>
                </div>
            </div>
            
            <div class="mt-16 text-center" data-aos="fade-up">
                <a href="register.php" class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-8 rounded-lg transition-colors duration-200 mr-4">
                    Sign Up Now
                </a>
                <a href="about.php" class="inline-block bg-white hover:bg-gray-100 text-primary-600 font-medium py-3 px-8 rounded-lg border border-primary-600 transition-colors duration-200">
                    Learn More
                </a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-16 md:py-24 bg-gradient-light">
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
                            <div class="bg-white rounded-xl shadow-md p-8 md:p-10">
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
                                    <img src="assets/images/testimonials/user1.jpg" alt="Sarah Johnson" class="w-12 h-12 rounded-full object-cover">
                                    <div class="ml-4">
                                        <h4 class="font-bold">Sarah Johnson</h4>
                                        <p class="text-gray-500 text-sm">New York, NY</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Testimonial 2 -->
                        <div class="swiper-slide">
                            <div class="bg-white rounded-xl shadow-md p-8 md:p-10">
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
                                    <img src="assets/images/testimonials/user2.jpg" alt="Michael Rodriguez" class="w-12 h-12 rounded-full object-cover">
                                    <div class="ml-4">
                                        <h4 class="font-bold">Michael Rodriguez</h4>
                                        <p class="text-gray-500 text-sm">Chicago, IL</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Testimonial 3 -->
                        <div class="swiper-slide">
                            <div class="bg-white rounded-xl shadow-md p-8 md:p-10">
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
                                    <img src="assets/images/testimonials/user3.jpg" alt="Emily Chen" class="w-12 h-12 rounded-full object-cover">
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
                <button class="testimonial-prev absolute top-1/2 -left-4 md:-left-8 transform -translate-y-1/2 bg-white rounded-full w-10 h-10 flex items-center justify-center shadow-md z-10 focus:outline-none">
                    <i class="fas fa-chevron-left text-primary-600"></i>
                </button>
                <button class="testimonial-next absolute top-1/2 -right-4 md:-right-8 transform -translate-y-1/2 bg-white rounded-full w-10 h-10 flex items-center justify-center shadow-md z-10 focus:outline-none">
                    <i class="fas fa-chevron-right text-primary-600"></i>
                </button>
            </div>
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
                <?php foreach ($popularCities as $index => $city): ?>
                <a href="search.php?location=<?= urlencode($city['name'] . ', ' . $city['state']) ?>" class="bg-gray-50 hover:bg-primary-50 rounded-lg p-4 text-center transition-colors duration-200" data-aos="fade-up" data-aos-delay="<?= $index * 50 ?>">
                    <h3 class="font-medium text-gray-900"><?= htmlspecialchars($city['name']) ?></h3>
                    <p class="text-sm text-gray-500"><?= htmlspecialchars($city['state']) ?></p>
                    <p class="text-xs text-primary-600 mt-1"><?= number_format($city['business_count']) ?> businesses</p>
                </a>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-12">
                <a href="cities.php" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200">
                    <span>View All Cities</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Recent Reviews Section -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold font-montserrat mb-4">Recent Reviews</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">See what people are saying about local businesses in your area.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($recentReviews as $index => $review): ?>
                <div class="bg-white rounded-xl shadow-md p-6 card-hover" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <?php if (!empty($review['user_image'])): ?>
                            <img src="uploads/users/<?= htmlspecialchars($review['user_image']) ?>" alt="<?= htmlspecialchars($review['user_name']) ?>" class="w-10 h-10 rounded-full object-cover">
                            <?php else: ?>
                            <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                                <span class="text-primary-600 font-medium"><?= getInitials($review['user_name']) ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="ml-3">
                                <h4 class="font-medium"><?= htmlspecialchars($review['user_name']) ?></h4>
                                <p class="text-gray-500 text-xs"><?= formatDate($review['created_at']) ?></p>
                            </div>
                        </div>
                        <div class="flex">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?= $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300' ?>"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">
                        <a href="business.php?slug=<?= htmlspecialchars($review['business_slug']) ?>" class="hover:text-primary-600 transition-colors duration-200"><?= htmlspecialchars($review['business_name']) ?></a>
                    </h3>
                    <p class="text-gray-700 mb-4"><?= nl2br(htmlspecialchars(substr($review['comment'], 0, 150))) ?><?= strlen($review['comment']) > 150 ? '...' : '' ?></p>
                    <a href="business.php?slug=<?= htmlspecialchars($review['business_slug']) ?>#reviews" class="text-primary-600 hover:text-primary-700 text-sm font-medium">Read more</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-16 md:py-24 bg-gradient-primary text-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold font-montserrat mb-4">Our Growing Community</h2>
                <p class="text-lg opacity-90 max-w-3xl mx-auto">Join thousands of users and businesses who trust FindIt every day.</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center" data-aos="fade-up" data-aos-delay="0">
                    <div class="text-4xl md:text-5xl font-bold mb-2"><?= number_format($stats['business_count']) ?>+</div>
                    <p class="text-lg opacity-90">Businesses</p>
                </div>
                
                <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-4xl md:text-5xl font-bold mb-2"><?= number_format($stats['user_count']) ?>+</div>
                    <p class="text-lg opacity-90">Users</p>
                </div>
                
                <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-4xl md:text-5xl font-bold mb-2"><?= number_format($stats['review_count']) ?>+</div>
                    <p class="text-lg opacity-90">Reviews</p>
                </div>
                
                <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-4xl md:text-5xl font-bold mb-2"><?= number_format($stats['category_count']) ?>+</div>
                    <p class="text-lg opacity-90">Categories</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Business Owner CTA Section -->
    <section class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-50 rounded-2xl p-8 md:p-12 shadow-md" data-aos="fade-up">
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
                            <a href="business-signup.php" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200">
                                List Your Business
                            </a>
                            <a href="business-plans.php" class="bg-white hover:bg-gray-100 text-primary-600 font-medium py-3 px-6 rounded-lg border border-primary-600 transition-colors duration-200">
                                View Plans
                            </a>
                        </div>
                    </div>
                    <div class="hidden lg:block">
                        <img src="assets/images/business-owner.svg" alt="Business owner" class="w-full max-w-md mx-auto">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- App Download Section -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right">
                    <h2 class="text-3xl md:text-4xl font-bold font-montserrat mb-4">Take FindIt With You</h2>
                    <p class="text-lg text-gray-600 mb-8">Download our mobile app to find local businesses on the go. Available for iOS and Android devices.</p>
                    
                    <div class="flex flex-wrap gap-4 mb-8">
                        <a href="#" class="flex items-center bg-black text-white rounded-lg px-4 py-3 hover:bg-gray-800 transition-colors duration-200">
                            <i class="fab fa-apple text-2xl mr-3"></i>
                            <div>
                                <div class="text-xs">Download on the</div>
                                <div class="text-lg font-semibold">App Store</div>
                            </div>
                        </a>
                        
                        <a href="#" class="flex items-center bg-black text-white rounded-lg px-4 py-3 hover:bg-gray-800 transition-colors duration-200">
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
                    <img src="assets/images/app-mockup.png" alt="FindIt Mobile App" class="w-full max-w-md mx-auto">
                    
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
                    <a href="faq.php" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200">
                        <span>View All FAQs</span>
                        <i class="fas fa-arrow-right ml-2"></i>
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
                        
                        <form action="subscribe.php" method="POST" class="space-y-4">
                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                            <div>
                                <label for="email" class="sr-only">Email Address</label>
                                <input type="email" id="email" name="email" placeholder="Your email address" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
                            </div>
                            <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200">
                                Subscribe Now
                            </button>
                        </form>
                        
                        <p class="text-xs text-gray-500 mt-4">By subscribing, you agree to our <a href="privacy.php" class="text-primary-600 hover:underline">Privacy Policy</a> and consent to receive updates from FindIt.</p>
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

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center mb-6">
                        <img src="assets/images/logo-white.png" alt="FindIt Logo" class="h-10 w-auto">
                        <span class="ml-2 text-2xl font-  alt="FindIt Logo" class="h-10 w-auto">
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
                        <li><a href="index.php" class="text-gray-400 hover:text-white transition-colors duration-200">Home</a></li>
                        <li><a href="about.php" class="text-gray-400 hover:text-white transition-colors duration-200">About Us</a></li>
                        <li><a href="categories.php" class="text-gray-400 hover:text-white transition-colors duration-200">Categories</a></li>
                        <li><a href="businesses.php" class="text-gray-400 hover:text-white transition-colors duration-200">Businesses</a></li>
                        <li><a href="contact.php" class="text-gray-400 hover:text-white transition-colors duration-200">Contact Us</a></li>
                        <li><a href="faq.php" class="text-gray-400 hover:text-white transition-colors duration-200">FAQ</a></li>
                    </ul>
                </div>
                
                <!-- For Business -->
                <div>
                    <h3 class="text-lg font-semibold mb-6">For Business</h3>
                    <ul class="space-y-3">
                        <li><a href="business-signup.php" class="text-gray-400 hover:text-white transition-colors duration-200">List Your Business</a></li>
                        <li><a href="business-plans.php" class="text-gray-400 hover:text-white transition-colors duration-200">Pricing Plans</a></li>
                        <li><a href="business-dashboard.php" class="text-gray-400 hover:text-white transition-colors duration-200">Business Dashboard</a></li>
                        <li><a href="business-resources.php" class="text-gray-400 hover:text-white transition-colors duration-200">Resources</a></li>
                        <li><a href="business-success.php" class="text-gray-400 hover:text-white transition-colors duration-200">Success Stories</a></li>
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
                    &copy; <?= date('Y') ?> FindIt. All rights reserved.
                </div>
                <div class="flex space-x-6">
                    <a href="terms.php" class="text-gray-500 hover:text-white text-sm transition-colors duration-200">Terms of Service</a>
                    <a href="privacy.php" class="text-gray-500 hover:text-white text-sm transition-colors duration-200">Privacy Policy</a>
                    <a href="cookies.php" class="text-gray-500 hover:text-white text-sm transition-colors duration-200">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-6 right-6 bg-primary-600 text-white rounded-full p-3 shadow-lg hidden hover:bg-primary-700 transition-colors duration-200 z-50">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Swiper JS -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    
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
        
        // Testimonial Slider
        const testimonialSlider = new Swiper('.testimonial-slider .swiper-container', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
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
        
        // Helper function to format phone numbers
        function formatPhoneNumber(phoneNumber) {
            const cleaned = ('' + phoneNumber).replace(/\D/g, '');
            const match = cleaned.match(/^(\d{3})(\d{3})(\d{4})$/);
            if (match) {
                return '(' + match[1] + ') ' + match[2] + '-' + match[3];
            }
            return phoneNumber;
        }
    </script>
</body>
</html>