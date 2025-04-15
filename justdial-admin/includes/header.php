<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/functions.php';

// Check if user is logged in
if (!isLoggedIn() && !in_array(basename($_SERVER['PHP_SELF']), ['login.php', 'register.php', 'forgot-password.php', 'reset-password.php'])) {
    redirect('modules/auth/login.php');
}

// Get current user data if logged in
$currentUser = null;
if (isLoggedIn()) {
    $db = db();
    $db->query("SELECT * FROM users WHERE id = :id");
    $db->bind(':id', $_SESSION['user_id']);
    $currentUser = $db->single();
}

// Get flash message
$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts: Montserrat and Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/custom.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'montserrat': ['Montserrat', 'sans-serif'],
                        'poppins': ['Poppins', 'sans-serif'],
                    },
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
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        },
                        accent: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-poppins">
    <?php if (isLoggedIn()): ?>
    <!-- Top Navigation Bar -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Mobile menu button -->
                    <div class="flex items-center md:hidden">
                        <button id="mobile-menu-button" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500">
                            <span class="sr-only">Open main menu</span>
                            <i class="fa-solid fa-bars w-6 h-6"></i>
                        </button>
                    </div>
                    
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="<?= BASE_URL ?>" class="flex items-center">
                            <img class="h-8 w-auto" src="<?= ASSETS_PATH ?>/img/logo.png" alt="<?= APP_NAME ?>">
                            <span class="ml-2 text-xl font-bold text-gray-900"><?= APP_NAME ?></span>
                        </a>
                    </div>
                </div>
                
                <!-- Right side navigation items -->
                <div class="flex items-center">
                    <!-- Notifications dropdown -->
                    <div class="relative ml-3">
                        <button type="button" class="p-1 text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-full">
                            <span class="sr-only">View notifications</span>
                            <i class="fa-regular fa-bell w-6 h-6"></i>
                        </button>
                    </div>
                    
                    <!-- Profile dropdown -->
                    <div class="relative ml-3">
                        <div>
                            <button type="button" id="user-menu-button" class="flex items-center max-w-xs rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" aria-expanded="false" aria-haspopup="true">
                                <span class="sr-only">Open user menu</span>
                                <img class="h-8 w-8 rounded-full object-cover" src="<?= UPLOADS_URL ?>/users/<?= $currentUser['profile_image'] ?>" alt="<?= $currentUser['name'] ?>">
                                <span class="ml-2 hidden md:block"><?= $currentUser['name'] ?></span>
                                <i class="fa-solid fa-chevron-down ml-1 text-gray-500"></i>
                            </button>
                        </div>
                        
                        <!-- Profile dropdown menu -->
                        <div id="user-menu" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                            <a href="<?= BASE_URL ?>/modules/users/edit.php?id=<?= $currentUser['id'] ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Your Profile</a>
                            <a href="<?= BASE_URL ?>/modules/settings/index.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Settings</a>
                            <div class="border-t border-gray-100"></div>
                            <a href="<?= BASE_URL ?>/modules/auth/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Sign out</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>
    
    <!-- Flash Messages -->
    <?php if ($flashMessage): ?>
    <div id="flash-message" class="fixed top-4 right-4 z-50 max-w-md <?= $flashMessage['type'] === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700' ?> px-4 py-3 rounded border shadow-md">
        <div class="flex items-center">
            <div class="py-1">
                <?php if ($flashMessage['type'] === 'success'): ?>
                    <i class="fa-solid fa-circle-check mr-2"></i>
                <?php else: ?>
                    <i class="fa-solid fa-circle-exclamation mr-2"></i>
                <?php endif; ?>
            </div>
            <div>
                <p><?= $flashMessage['message'] ?></p>
            </div>
            <div class="ml-auto pl-3">
                <button type="button" class="close-flash -mx-1.5 -my-1.5 bg-transparent inline-flex p-1.5 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <i class="fa-solid fa-xmark w-4 h-4"></i>
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="flex h-screen overflow-hidden bg-gray-100">
        <?php if (isLoggedIn()): ?>
            <?php include_once 'sidebar.php'; ?>
        <?php endif; ?>
        
        <div class="flex flex-col flex-1 w-0 overflow-hidden">
            <main class="relative flex-1 overflow-y-auto focus:outline-none">
                <div class="py-6">