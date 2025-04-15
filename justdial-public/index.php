<?php
// justdial-public/index.php

// Include header - this handles db connection, functions, and top HTML
include_once 'includes/header.php';

// Fetch data for the homepage sections
$featured_categories = get_featured_categories($pdo, 8); // Get 8 featured categories
$featured_businesses = get_featured_businesses($pdo, 6); // Get 6 featured businesses
$popular_cities = get_popular_cities($pdo, 12); // Get 12 popular cities

// You might want to fetch counts for the statistics section from the DB later
// For now, the data-count attributes remain static as in the original HTML
$stats_businesses_count = 15000; // Placeholder - Fetch actual count later if desired
$stats_users_count = 250000; // Placeholder
$stats_reviews_count = 120000; // Placeholder
$stats_cities_count = 500; // Placeholder

?>

    <!-- Hero Section -->
    <section id="home" class="hero-gradient text-white relative overflow-hidden min-h-screen flex items-center">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right" data-aos-duration="1000">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold font-montserrat leading-tight mb-6">Discover the Best Local Businesses Near You</h1>
                    <p class="text-lg md:text-xl opacity-90 mb-8"><?php safe_echo(get_setting('site_tagline', 'Find top-rated restaurants, services, shops, and more in your area. Read reviews, get directions, and make informed decisions.')); ?></p>

                    <!-- Search Bar -->
                    <div class="bg-white rounded-lg p-2 search-bar-shadow mb-8 transform transition-all duration-500 hover:scale-105">
                        <!-- TODO: Update form action to point to search results page -->
                        <form id="search-form" class="flex flex-col md:flex-row" action="search.php" method="GET">
                            <div class="flex-1 mb-2 md:mb-0 md:mr-2">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                    <input type="text" name="query" id="search-query" placeholder="What are you looking for?" class="w-full pl-10 pr-4 text-gray-700 py-3 rounded-lg border-0 focus:ring-2 focus:ring-primary-500 outline-none" required>
                                </div>
                            </div>
                            <div class="flex-1 mb-2 md:mb-0 md:mr-2">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-map-marker-alt text-gray-400"></i>
                                    </div>
                                    <input type="text" name="location" id="search-location" placeholder="Location" class="w-full pl-10 pr-4 py-3 rounded-lg text-gray-700 border-0 focus:ring-2 focus:ring-primary-500 outline-none">
                                </div>
                            </div>
                            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-300 flex items-center justify-center ripple transform hover:scale-105 shadow-md hover:shadow-lg">
                                <span>Search</span>
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Popular Searches (Could be made dynamic later based on popular search terms) -->
                    <div class="flex flex-wrap items-center">
                        <span class="text-white opacity-90 mr-3 mb-2">Popular:</span>
                        <a href="search.php?query=Restaurants" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-full px-4 py-1 text-sm mr-2 mb-2 transition-all duration-300 hover:transform hover:scale-110">Restaurants</a>
                        <a href="search.php?query=Hotels" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-full px-4 py-1 text-sm mr-2 mb-2 transition-all duration-300 hover:transform hover:scale-110">Hotels</a>
                        <a href="search.php?query=Plumbers" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-full px-4 py-1 text-sm mr-2 mb-2 transition-all duration-300 hover:transform hover:scale-110">Plumbers</a>
                        <a href="search.php?query=Electricians" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-full px-4 py-1 text-sm mr-2 mb-2 transition-all duration-300 hover:transform hover:scale-110">Electricians</a>
                        <a href="search.php?query=Gyms" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-full px-4 py-1 text-sm mr-2 mb-2 transition-all duration-300 hover:transform hover:scale-110">Gyms</a>
                    </div>
                </div>

                <!-- Right side image and floating elements (Static as per original HTML) -->
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

        <!-- Animated Shapes (Static SVGs as per original HTML) -->
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

        <!-- Wave Divider (Static SVG as per original HTML) -->
        <!-- <div class="custom-shape-divider">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
            </svg>
        </div> -->

        <!-- Scroll Down Indicator (Static as per original HTML) -->
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

            <?php if (!empty($featured_categories)): ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6 md:gap-8">
                <?php foreach ($featured_categories as $index => $category): ?>
                <!-- TODO: Update link to point to category details page -->
                <a href="category.php?slug=<?php safe_echo($category['slug']); ?>"
                   class="category-card bg-white rounded-xl shadow-md hover:shadow-xl p-6 text-center card-hover transition-all duration-300"
                   data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                    <div class="bg-primary-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="<?php echo render_icon($category['icon'], 'fa-tags'); ?> text-primary-600 text-2xl category-icon"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2"><?php safe_echo($category['name']); ?></h3>
                    <p class="text-gray-500 text-sm"><?php echo number_format($category['business_count']); ?> listings</p>
                </a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-center text-gray-500">No popular categories found.</p>
            <?php endif; ?>

            <div class="text-center mt-12">
                 <!-- TODO: Update link to point to all categories page -->
                <a href="categories.php" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200 group">
                    <span>View All Categories</span>
                    <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform duration-300"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Interactive Tabs Section (Static as per original HTML) -->
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

            <?php if (!empty($featured_businesses)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($featured_businesses as $index => $business): ?>
                <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                    <div class="relative">
                        <img src="<?php echo get_image_url('businesses', $business['cover_image'], 'https://via.placeholder.com/600x400'); ?>" alt="<?php safe_echo($business['name']); ?> Cover" class="w-full h-48 object-cover">
                        <div class="absolute top-4 left-4">
                            <span class="bg-primary-600 text-white text-xs font-semibold px-3 py-1 rounded-full">Featured</span>
                        </div>
                         <?php if ($business['avg_rating'] > 0): ?>
                        <div class="absolute top-4 right-4">
                            <span class="bg-white text-gray-800 text-xs font-semibold px-3 py-1 rounded-full shadow-md">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <?php echo number_format($business['avg_rating'], 1); ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                         <?php if ($business['category_names']): ?>
                        <div class="flex items-center mb-2">
                            <?php
                            // Display first category name as a tag
                            $categories = explode(',', $business['category_names']);
                            $first_category = trim($categories[0]);
                            ?>
                             <!-- TODO: Update link to point to category details page -->
                             <a href="category.php?slug=<?php /* echo generate_slug($first_category); */ // Need slug generation or fetch slug ?>"
                                class="text-xs font-medium text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1">
                                 <?php safe_echo($first_category); ?>
                             </a>
                         </div>
                         <?php endif; ?>
                        <h3 class="text-xl font-bold mb-2 hover:text-primary-600 transition-colors duration-200">
                             <!-- TODO: Update link to business details page OR use modal -->
                             <!-- Pass business ID to the modal trigger -->
                            <a href="business.php?id=<?php echo $business['id']; ?>" class="business-link" data-business-id="<?php echo $business['id']; ?>">
                                <?php safe_echo($business['name']); ?>
                            </a>
                        </h3>
                        <div class="flex items-center text-gray-500 text-sm mb-4">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                             <!-- TODO: Update link to city details page -->
                             <a href="city.php?name=<?php echo urlencode($business['city']); ?>&state=<?php echo urlencode($business['state']); ?>" class="hover:underline">
                                <span><?php safe_echo($business['city']); ?>, <?php safe_echo($business['state']); ?></span>
                            </a>
                        </div>
                        <div class="flex items-center justify-between">
                             <!-- Phone could be added here if fetched -->
                             <!-- <div class="flex items-center"><i class="fas fa-phone text-primary-600 mr-2"></i><span class="text-gray-700">...</span></div> -->
                            <span class="text-sm text-gray-500"><?php echo number_format($business['review_count']); ?> reviews</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
             <p class="text-center text-gray-500">No featured businesses found.</p>
            <?php endif; ?>

            <div class="text-center mt-12">
                <!-- TODO: Update link to all businesses page -->
                <a href="businesses.php" class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-8 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
                    Explore All Businesses
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works Section (Static as per original HTML) -->
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

    <!-- Testimonials Section (Static as per original HTML - could fetch reviews later) -->
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
                                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
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
                                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
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
                                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
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

            <?php if (!empty($popular_cities)): ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php foreach ($popular_cities as $index => $city): ?>
                 <!-- TODO: Update link to city details page -->
                <a href="city.php?name=<?php echo urlencode($city['name']); ?>&state=<?php echo urlencode($city['state']); ?>"
                   class="bg-gray-50 hover:bg-primary-50 rounded-lg p-4 text-center transition-all duration-300 transform hover:scale-105"
                   data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                    <h3 class="font-medium text-gray-900"><?php safe_echo($city['name']); ?></h3>
                    <p class="text-sm text-gray-500"><?php safe_echo($city['state']); ?></p>
                    <p class="text-xs text-primary-600 mt-1"><?php echo number_format($city['business_count']); ?> businesses</p>
                </a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
             <p class="text-center text-gray-500">No popular cities found.</p>
            <?php endif; ?>

            <div class="text-center mt-12">
                 <!-- TODO: Update link to all cities page -->
                <a href="cities.php" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200 group">
                    <span>View All Cities</span>
                    <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform duration-300"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-16 md:py-24 bg-gradient-primary text-white parallax" style="background-image: url('https://via.placeholder.com/1920x1080');"> <!-- Add parallax background image -->
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10"> <!-- Add z-10 -->
             <div class="absolute inset-0 bg-black opacity-50"></div> <!-- Overlay for readability -->
             <div class="relative z-20"> <!-- Content above overlay -->
                <div class="text-center mb-12" data-aos="fade-up">
                    <h2 class="text-3xl md:text-4xl font-bold font-montserrat mb-4">Our Growing Community</h2>
                    <p class="text-lg opacity-90 max-w-3xl mx-auto">Join thousands of users and businesses who trust FindIt every day.</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div class="text-center" data-aos="fade-up" data-aos-delay="0">
                        <div class="text-4xl md:text-5xl font-bold mb-2"><span class="counter-value" data-count="<?php echo $stats_businesses_count; ?>">0</span>+</div>
                        <p class="text-lg opacity-90">Businesses</p>
                    </div>
                    <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                        <div class="text-4xl md:text-5xl font-bold mb-2"><span class="counter-value" data-count="<?php echo $stats_users_count; ?>">0</span>+</div>
                        <p class="text-lg opacity-90">Users</p>
                    </div>
                    <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                        <div class="text-4xl md:text-5xl font-bold mb-2"><span class="counter-value" data-count="<?php echo $stats_reviews_count; ?>">0</span>+</div>
                        <p class="text-lg opacity-90">Reviews</p>
                    </div>
                    <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                        <div class="text-4xl md:text-5xl font-bold mb-2"><span class="counter-value" data-count="<?php echo $stats_cities_count; ?>">0</span>+</div>
                        <p class="text-lg opacity-90">Cities</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section (Static as per original HTML) -->
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
                     <!-- TODO: Link to full FAQ page -->
                    <a href="faq.php" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200 group">
                        <span>View All FAQs</span>
                        <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform duration-300"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section (Static form, JS hooks) -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-md overflow-hidden" data-aos="fade-up">
                <div class="grid grid-cols-1 md:grid-cols-2">
                    <div class="p-8 md:p-12">
                        <h2 class="text-2xl md:text-3xl font-bold font-montserrat mb-4">Subscribe to Our Newsletter</h2>
                        <p class="text-gray-600 mb-6">Stay updated with the latest businesses, special offers, and tips for finding the best local services.</p>

                        <form id="newsletter-form" class="space-y-4">
                            <div>
                                <label for="newsletter-email" class="sr-only">Email Address</label>
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
                                <li class="flex items-start"><i class="fas fa-check-circle mt-1 mr-3"></i><span>Exclusive deals and promotions</span></li>
                                <li class="flex items-start"><i class="fas fa-check-circle mt-1 mr-3"></i><span>New business alerts in your area</span></li>
                                <li class="flex items-start"><i class="fas fa-check-circle mt-1 mr-3"></i><span>Helpful tips and guides</span></li>
                                <li class="flex items-start"><i class="fas fa-check-circle mt-1 mr-3"></i><span>Monthly newsletter with curated content</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- App Download Section (Static as per original HTML) -->
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
                            <i class="fas fa-star text-yellow-400"></i><i class="fas fa-star text-yellow-400"></i><i class="fas fa-star text-yellow-400"></i><i class="fas fa-star text-yellow-400"></i><i class="fas fa-star-half-alt text-yellow-400"></i>
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
                            <div class="bg-primary-100 rounded-full p-2 mr-3"><i class="fas fa-search text-primary-600"></i></div>
                            <div><h4 class="font-semibold text-sm">Quick Search</h4><p class="text-xs text-gray-500">Find what you need fast</p></div>
                        </div>
                    </div>
                    <div class="absolute top-1/2 -right-4 md:-right-8 bg-white rounded-lg shadow-lg p-3 animate-float" style="animation-delay: 0.5s;">
                        <div class="flex items-center">
                             <div class="bg-primary-100 rounded-full p-2 mr-3"><i class="fas fa-map-marker-alt text-primary-600"></i></div>
                             <div><h4 class="font-semibold text-sm">Nearby Places</h4><p class="text-xs text-gray-500">Discover local gems</p></div>
                        </div>
                    </div>
                    <div class="absolute bottom-1/4 -left-4 md:-left-8 bg-white rounded-lg shadow-lg p-3 animate-float" style="animation-delay: 1s;">
                        <div class="flex items-center">
                             <div class="bg-primary-100 rounded-full p-2 mr-3"><i class="fas fa-bell text-primary-600"></i></div>
                             <div><h4 class="font-semibold text-sm">Notifications</h4><p class="text-xs text-gray-500">Stay updated</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Business Owner CTA Section (Static as per original HTML) -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl p-8 md:p-12 shadow-md" data-aos="fade-up">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-bold font-montserrat mb-4">Own a Business?</h2>
                        <p class="text-lg text-gray-600 mb-6">List your business on FindIt to reach thousands of potential customers searching for services like yours every day.</p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-start"><i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i><span class="text-gray-700">Increase your online visibility and reach more customers</span></li>
                            <li class="flex items-start"><i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i><span class="text-gray-700">Showcase your services, hours, and contact information</span></li>
                            <li class="flex items-start"><i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i><span class="text-gray-700">Collect and respond to customer reviews</span></li>
                            <li class="flex items-start"><i class="fas fa-check-circle text-primary-600 mt-1 mr-3"></i><span class="text-gray-700">Access analytics and insights about your listing</span></li>
                        </ul>
                        <div class="flex flex-wrap gap-4">
                             <!-- TODO: Link buttons to appropriate pages/modals -->
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

<?php
// Include footer - this handles closing tags and JS includes
include_once 'includes/footer.php';
?>