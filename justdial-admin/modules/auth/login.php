<?php
require_once '../../config/config.php';
require_once '../../config/functions.php';
require_once '../../includes/auth.php';

// Check if user is already logged in
if (isLoggedIn()) {
    redirect('modules/dashboard/index.php');
}

// Check for remember me token
if (checkRememberMe()) {
    redirect('modules/dashboard/index.php');
}

// Process login form
$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        $rememberMe = isset($_POST['remember_me']);
        
        $result = loginUser($email, $password, $rememberMe);
        
        if ($result['success']) {
            setFlashMessage('success', $result['message']);
            redirect('modules/dashboard/index.php');
        } else {
            $errors[] = $result['message'];
        }
    }
}

$pageTitle = 'Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - <?= APP_NAME ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts: Montserrat and Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
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
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .auth-container {
            background-image: url('data:image/svg+xml;charset=utf-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%230ea5e9" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,208C1248,224,1344,192,1392,176L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-repeat: no-repeat;
            background-position: bottom;
            background-size: 100% 50%;
        }
    </style>
</head>
<body class="bg-gray-50 auth-container min-h-screen flex flex-col">
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
        <a href="<?= BASE_URL ?>" class="flex items-center mb-6 text-2xl font-semibold text-gray-900">
            <img class="w-8 h-8 mr-2" src="<?= ASSETS_PATH ?>/img/logo.png" alt="<?= APP_NAME ?>">
            <?= APP_NAME ?>
        </a>
        
        <div class="w-full bg-white rounded-lg shadow-xl md:mt-0 sm:max-w-md xl:p-0">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl">
                    Sign in to your account
                </h1>
                
                <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <form class="space-y-4 md:space-y-6" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="fa-regular fa-envelope text-gray-500"></i>
                            </div>
                            <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full pl-10 p-2.5" placeholder="name@example.com" value="<?= $email ?>" required>
                        </div>
                    </div>
                    
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="fa-solid fa-lock text-gray-500"></i>
                            </div>
                            <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full pl-10 p-2.5" required>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="remember_me" name="remember_me" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="remember_me" class="text-gray-500">Remember me</label>
                            </div>
                        </div>
                        <a href="forgot-password.php" class="text-sm font-medium text-primary-600 hover:underline">Forgot password?</a>
                    </div>
                    
                    <button type="submit" class="w-full text-white bg-gradient-to-r from-primary-500 to-primary-700 hover:from-primary-600 hover:to-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all duration-300 ease-in-out transform hover:scale-[1.02]">
                        Sign in
                    </button>
                    
                    <?php if (getSetting('enable_registration', '1') === '1'): ?>
                    <p class="text-sm font-light text-gray-500">
                        Don't have an account yet? <a href="register.php" class="font-medium text-primary-600 hover:underline">Sign up</a>
                    </p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    
    <footer class="mt-auto py-4">
        <div class="w-full max-w-screen-xl mx-auto p-4 md:py-8">
            <div class="sm:flex sm:items-center sm:justify-between">
                <a href="<?= BASE_URL ?>" class="flex items-center mb-4 sm:mb-0">
                    <img src="<?= ASSETS_PATH ?>/img/logo.png" class="h-8 mr-3" alt="<?= APP_NAME ?> Logo" />
                    <span class="self-center text-2xl font-semibold whitespace-nowrap"><?= APP_NAME ?></span>
                </a>
                <span class="block text-sm text-gray-500 sm:text-center">
                    &copy; <?= date('Y') ?> <a href="<?= BASE_URL ?>" class="hover:underline"><?= APP_NAME ?></a>. All Rights Reserved.
                </span>
            </div>
        </div>
    </footer>
</body>
</html>