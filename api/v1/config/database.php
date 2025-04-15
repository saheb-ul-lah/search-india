<?php
// api/v1/config/database.php
require_once 'config.php';

$api_pdo = null;

try {
    $dsn = "mysql:host=" . API_DB_HOST . ";dbname=" . API_DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false, // Use native prepared statements
    ];
    $api_pdo = new PDO($dsn, API_DB_USER, API_DB_PASS, $options);
} catch (\PDOException $e) {
    // Don't echo detailed errors in production API. Log them.
    error_log("API Database Connection Error: " . $e->getMessage());
    // Send a generic server error response (handled in index.php)
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Internal Server Error - Database Connection Failed']);
    exit; // Stop execution
}

// $api_pdo is now available globally in included files (or return it/use dependency injection)
?>