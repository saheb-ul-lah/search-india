<?php
// Application configuration

// Base URL - update this to your domain
define('BASE_URL', 'http://localhost/justdial/justdial-admin');

// Application name
define('APP_NAME', 'Search India');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'justdial_admin');

// Directory paths
define('ROOT_PATH', dirname(__DIR__));
define('ASSETS_PATH', BASE_URL . '/assets');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('UPLOADS_URL', BASE_URL . '/uploads');

// Session configuration
define('SESSION_NAME', 'justdial_admin_session');
define('SESSION_LIFETIME', 86400); // 24 hours

// Security
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_MIN_LENGTH', 8);

// Pagination
define('ITEMS_PER_PAGE', 10);

// Date and time format
define('DATE_FORMAT', 'd M Y');
define('TIME_FORMAT', 'h:i A');
define('DATETIME_FORMAT', 'd M Y h:i A');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// Set timezone
date_default_timezone_set('Asia/Kolkata');