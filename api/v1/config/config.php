<?php
// api/v1/config/config.php

// Error Reporting (Development vs Production)
// error_reporting(0); // Production
// ini_set('display_errors', 0); // Production
error_reporting(E_ALL); // Development
ini_set('display_errors', 1); // Development

// Database Credentials (Consider using environment variables in production)
define('API_DB_HOST', 'localhost');
define('API_DB_USER', 'root'); // Use a dedicated API DB user if possible
define('API_DB_PASS', '');
define('API_DB_NAME', 'justdial_admin'); // Your database name

// API Settings
define('API_VERSION', 'v1');
define('API_RATE_LIMIT', 1000); // Max requests per day per API key
define('API_ITEMS_PER_PAGE', 10); // Default items per page for listings

// CORS Allowed Origins (Adjust for production)
// Use '*' for development ONLY. List specific domains for production.
define('API_ALLOWED_ORIGINS', '*');
// define('API_ALLOWED_ORIGINS', 'https://myapp.com, https://partner.com');

?>