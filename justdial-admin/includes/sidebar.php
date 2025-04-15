<!-- Sidebar -->
<div id="sidebar" class="hidden md:flex md:flex-shrink-0 transition-all duration-300">
    <div class="flex flex-col w-64 bg-gradient-to-b from-primary-700 to-primary-900 text-white">
        <!-- Sidebar content -->
        <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
            <!-- Sidebar header -->
            <div class="flex items-center flex-shrink-0 px-4 mb-5">
                <a href="<?= BASE_URL ?>" class="flex items-center">
                    <img class="h-8 w-auto" src="<?= ASSETS_PATH ?>/img/logo.png" alt="<?= APP_NAME ?>">
                    <span class="ml-2 text-xl font-bold text-white"><?= APP_NAME ?></span>
                </a>
            </div>
            
            <!-- Navigation -->
            <nav class="mt-5 flex-1 px-2 space-y-1">
                <!-- Dashboard -->
                <a href="<?= BASE_URL ?>/modules/dashboard/index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' && dirname($_SERVER['PHP_SELF']) === '/modules/dashboard' ? 'bg-primary-800 text-white' : 'text-white hover:bg-primary-600' ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-all">
                    <i class="fa-solid fa-gauge-high mr-3 h-4 w-4"></i>
                    Dashboard
                </a>
                
                <!-- Businesses -->
                <a href="<?= BASE_URL ?>/modules/businesses/index.php" class="<?= strpos($_SERVER['PHP_SELF'], '/modules/businesses/') !== false ? 'bg-primary-800 text-white' : 'text-white hover:bg-primary-600' ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-all">
                    <i class="fa-solid fa-building mr-3 h-4 w-4"></i>
                    Businesses
                </a>
                
                <!-- Categories -->
                <a href="<?= BASE_URL ?>/modules/categories/index.php" class="<?= strpos($_SERVER['PHP_SELF'], '/modules/categories/') !== false ? 'bg-primary-800 text-white' : 'text-white hover:bg-primary-600' ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-all">
                    <i class="fa-solid fa-tags mr-3 h-4 w-4"></i>
                    Categories
                </a>
                
                <!-- Services -->
                <a href="<?= BASE_URL ?>/modules/services/index.php" class="<?= strpos($_SERVER['PHP_SELF'], '/modules/services/') !== false ? 'bg-primary-800 text-white' : 'text-white hover:bg-primary-600' ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-all">
                    <i class="fa-solid fa-briefcase mr-3 h-4 w-4"></i>
                    Services
                </a>
                
                <!-- Users -->
                <?php if (isAdmin()): ?>
                <a href="<?= BASE_URL ?>/modules/users/index.php" class="<?= strpos($_SERVER['PHP_SELF'], '/modules/users/') !== false ? 'bg-primary-800 text-white' : 'text-white hover:bg-primary-600' ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-all">
                    <i class="fa-solid fa-users mr-3 h-4 w-4"></i>
                    Users
                </a>
                <?php endif; ?>
                
                <!-- Reviews -->
                <a href="<?= BASE_URL ?>/modules/reviews/index.php" class="<?= strpos($_SERVER['PHP_SELF'], '/modules/reviews/') !== false ? 'bg-primary-800 text-white' : 'text-white hover:bg-primary-600' ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-all">
                    <i class="fa-solid fa-star mr-3 h-4 w-4"></i>
                    Reviews
                </a>
                
                <!-- Inquiries -->
                <a href="<?= BASE_URL ?>/modules/inquiries/index.php" class="<?= strpos($_SERVER['PHP_SELF'], '/modules/inquiries/') !== false ? 'bg-primary-800 text-white' : 'text-white hover:bg-primary-600' ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-all">
                    <i class="fa-solid fa-envelope mr-3 h-4 w-4"></i>
                    Inquiries
                </a>
                
                <!-- Cities -->
                <a href="<?= BASE_URL ?>/modules/cities/index.php" class="<?= strpos($_SERVER['PHP_SELF'], '/modules/cities/') !== false ? 'bg-primary-800 text-white' : 'text-white hover:bg-primary-600' ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-all">
                    <i class="fa-solid fa-city mr-3 h-4 w-4"></i>
                    Cities
                </a>
                
                <!-- Settings -->
                <?php if (isAdmin()): ?>
                <a href="<?= BASE_URL ?>/modules/settings/index.php" class="<?= strpos($_SERVER['PHP_SELF'], '/modules/settings/') !== false ? 'bg-primary-800 text-white' : 'text-white hover:bg-primary-600' ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-all">
                    <i class="fa-solid fa-gear mr-3 h-4 w-4"></i>
                    Settings
                </a>
                <?php endif; ?>
            </nav>
        </div>
        
        <!-- Sidebar footer -->
        <div class="flex-shrink-0 flex border-t border-primary-800 p-4">
            <div class="flex items-center">
                <div>
                    <img class="inline-block h-9 w-9 rounded-full object-cover" src="<?= UPLOADS_URL ?>/users/<?= $currentUser['profile_image'] ?>" alt="<?= $currentUser['name'] ?>">
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-white"><?= $currentUser['name'] ?></p>
                    <p class="text-xs font-medium text-primary-200"><?= ucfirst($currentUser['role']) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile sidebar -->
<div id="mobile-sidebar" class="fixed inset-0 flex z-40 md:hidden transform -translate-x-full transition-all duration-300">
    <!-- Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
    
    <!-- Sidebar panel -->
    <div class="relative flex-1 flex flex-col max-w-xs w-full bg-gradient-to-b from-primary-700 to-primary-900 text-white">
        <!-- Close button -->
        <div class="absolute top-0 right-0 -mr-12 pt-2">
            <button id="close-sidebar-button" type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                <span class="sr-only">Close sidebar</span>
                <i class="fa-solid fa-xmark text-white w-6 h-6"></i>
            </button>
        </div>
        
        <!-- Sidebar content -->
        <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
            <div class="flex-shrink-0 flex items-center px-4">
                <a href="<?= BASE_URL ?>" class="flex items-center">
                    <img class="h-8 w-auto" src="<?= ASSETS_PATH ?>/img/logo.png" alt="<?= APP_NAME ?>">
                    <span class="ml-2 text-xl font-bold text-white"><?= APP_NAME ?></span>
                </a>
            </div>
            <nav class="mt-5 px-2 space-y-1">
                <!-- Dashboard -->
                <a href="<?= BASE_URL ?>/modules/dashboard/index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' && dirname($_SERVER['PHP_SELF']) === '/modules/dashboard' ? 'bg-primary-800 text-white' : 'text-white hover:bg-primary-600' ?> group flex items-center px-2 py-2 text-base font-medium rounded-md transition-all">
                    <i class="fa-solid fa-gauge-high mr-3 h-5 w-5"></i>
                    Dashboard
                </a>
                
                <!-- Businesses -->
                <a href="<?= BASE_URL ?>/modules/businesses/index.php" class="<?= strpos($_SERVER['PHP_SELF'], '/modules/businesses/') !== false ? 'bg-primary-800 text-white' : 'text-white hover:bg-primary-600' ?> group flex items-center px-2 py-2 text-base font-medium rounded-md transition-all">
                    <i class="fa-solid fa-building mr-3 h-5 w-5"></i>
                    Businesses
                </a>
                
                <!-- Categories -->
                <a href="<?= BASE_URL ?>/modules/categories/index.php" class="<?= strpos($_SERVER['PHP_SELF'], '/modules/categories/') !== false ? 'bg-primary-800 text-white' : 'text-white hover:bg-primary-600' ?> group flex items-center px-2 py-2 text-base font-medium rounded-md transition-all">
                    <i class="fa-solid fa-tags mr-3 h-5 w-5"></i>
                    Categories
                </a>
                
                <!-- Services -->
                <a href="<?= BASE_URL ?>/modules/services/index.php" class="<?= strpos($_SERVER['PHP_SELF'], '/modules/services/') !== false ? 'bg-primary-800 text-white' : 'text-white hover:bg-primary-600' ?> group flex items-center px-2 py-2 text-base font-medium rounded-md transition-all">
                    <i class="fa-solid fa-briefcase mr-3 h-5 w-5"></i>
                    Services
                </a>
                
                <!-- Users -->
                <?php if (isAdmin()): ?>
                <a href="<?= BASE_URL ?>/modules/users/index.php" class="<?= strpos($_SERVER['PHP_SELF'], '/modules/users/') !== false ? 'bg-primary-800 text-white' : 'text-white hover:bg-primary-600' ?> group flex items-center px-2 py-2 text-base font-medium rounded-md transition-all">
                    <i class="fa-solid fa-users mr-3 h-5 w-5"></i>
                    Users
                </a>
                <?php endif; ?>
                
                <!-- Reviews -->
                <a href="<?= BASE_URL ?>/modules/reviews/index.php" class="<?= strpos($_SERVER['PHP_SELF'], '/modules/reviews/') !== false ? 'bg-primary-800 text-white' : 'text-white hover:bg-primary-600' ?> group flex items-center px-2 py-2 text-base font-medium rounded-md transition-all">
                    <i class="fa-solid fa-star mr-3 h-5 w-5"></i>
                    Reviews
                </a>
                
                <!-- Inquiries -->
                <a href="<?= BASE_URL ?>/modules/inquiries/index.php" class="<?= strpos($_SERVER['PHP_SELF'], '/modules/inquiries/') !== false ? 'bg-primary-800 text-white' : 'text-white hover:bg-primary-600' ?> group flex items-center px-2 py-2 text-base font-medium rounded-md transition-all">
                    <i class="fa-solid fa-envelope mr-3 h-5 w-5"></i>
                    Inquiries
                </a>
                
                <!-- Cities -->
                <a href="<?= BASE_URL ?>/modules/cities/index.php" class="<?= strpos($_SERVER['PHP_SELF'], '/modules/cities/') !== false ? 'bg-primary-800 text-white' : 'text-white hover:bg-primary-600' ?> group flex items-center px-2 py-2 text-base font-medium rounded-md transition-all">
                    <i class="fa-solid fa-city mr-3 h-5 w-5"></i>
                    Cities
                </a>
                
                <!-- Settings -->
                <?php if (isAdmin()): ?>
                <a href="<?= BASE_URL ?>/modules/settings/index.php" class="<?= strpos($_SERVER['PHP_SELF'], '/modules/settings/') !== false ? 'bg-primary-800 text-white' : 'text-white hover:bg-primary-600' ?> group flex items-center px-2 py-2 text-base font-medium rounded-md transition-all">
                    <i class="fa-solid fa-gear mr-3 h-5 w-5"></i>
                    Settings
                </a>
                <?php endif; ?>
            </nav>
        </div>
        
        <!-- Mobile sidebar footer -->
        <div class="flex-shrink-0 flex border-t border-primary-800 p-4">
            <a href="<?= BASE_URL ?>/modules/users/edit.php?id=<?= $currentUser['id'] ?>" class="flex-shrink-0 group block">
                <div class="flex items-center">
                    <div>
                        <img class="inline-block h-10 w-10 rounded-full object-cover" src="<?= UPLOADS_URL ?>/users/<?= $currentUser['profile_image'] ?>" alt="<?= $currentUser['name'] ?>">
                    </div>
                    <div class="ml-3">
                        <p class="text-base font-medium text-white"><?= $currentUser['name'] ?></p>
                        <p class="text-sm font-medium text-primary-200"><?= ucfirst($currentUser['role']) ?></p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>