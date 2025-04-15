<?php
// api/v1/index.php - Main API Router

// Bootstrap: Load config, database connection, and core functions
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php'; // Connects and sets $api_pdo
require_once __DIR__ . '/includes/functions.php'; // Response, Auth, Rate Limit helpers

// --- CORS Handling ---
// Headers are set in send_json_response, including handling OPTIONS preflight

// --- Authentication & Rate Limiting ---
if (!authenticate_and_rate_limit($api_pdo)) {
    // Error response already sent by the function
    exit;
}

// --- Routing ---
$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];

// Basic Routing: Remove base path and version prefix
$base_path = '/api/' . API_VERSION . '/'; // Adjust if your structure differs
$route = str_replace($base_path, '', parse_url($request_uri, PHP_URL_PATH));
$route_parts = explode('/', trim($route, '/'));
$resource = $route_parts[0] ?? null;
$resource_id = $route_parts[1] ?? null;
$sub_resource = $route_parts[2] ?? null; // For things like /categories/{id}/businesses

// Include handlers
require_once __DIR__ . '/handlers/business_handler.php';
require_once __DIR__ . '/handlers/category_handler.php';
require_once __DIR__ . '/handlers/city_handler.php';
require_once __DIR__ . '/handlers/setting_handler.php';

// Get Query Parameters (for filtering, pagination, searching)
$params = $_GET;

try {
    // Route based on resource and method
    switch ($resource) {
        case 'businesses':
            if ($request_method === 'GET') {
                if ($resource_id) {
                    // GET /businesses/{id}
                    handleGetBusinessById($api_pdo, (int)$resource_id);
                } else {
                    // GET /businesses
                    handleGetBusinesses($api_pdo, $params);
                }
            } else {
                send_error_response(405, 'Method Not Allowed for businesses.');
            }
            break;

        case 'categories':
            if ($request_method === 'GET') {
                if ($resource_id) {
                    if ($sub_resource === 'businesses') {
                         // GET /categories/{id_or_slug}/businesses
                         handleGetBusinessesByCategory($api_pdo, $resource_id, $params);
                    } else if (!$sub_resource) {
                         // GET /categories/{id_or_slug}
                         handleGetCategoryByIdOrSlug($api_pdo, $resource_id);
                    } else {
                         send_error_response(404, 'Not Found.');
                    }
                } else {
                    // GET /categories
                    handleGetCategories($api_pdo, $params);
                }
            } else {
                 send_error_response(405, 'Method Not Allowed for categories.');
            }
            break;

        case 'cities':
             if ($request_method === 'GET') {
                 if ($resource_id && $sub_resource === 'businesses' && isset($route_parts[3])) {
                      // GET /cities/{name}/{state}/businesses (Assuming ID is name, sub_resource is state)
                      $city_name = urldecode($resource_id);
                      $state_name = urldecode($route_parts[3]); // State is the 4th part
                      handleGetBusinessesByCity($api_pdo, $city_name, $state_name, $params);
                 } elseif (!$resource_id) {
                     // GET /cities
                     handleGetCities($api_pdo, $params);
                 } else {
                     send_error_response(404, 'Not Found.');
                 }
             } else {
                  send_error_response(405, 'Method Not Allowed for cities.');
             }
             break;


        case 'settings':
            if ($request_method === 'GET' && !$resource_id) {
                // GET /settings
                handleGetSettings($api_pdo);
            } else {
                 send_error_response(405, 'Method Not Allowed or Invalid Endpoint for settings.');
            }
            break;

        case '': // Base API path /api/v1/
             send_success_response(['message' => 'Welcome to the JustDial Clone API v1']);
             break;

        default:
            send_error_response(404, 'Not Found: Invalid API endpoint.');
            break;
    }

} catch (\Throwable $e) { // Catch generic errors/exceptions
    error_log("API Uncaught Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    send_error_response(500, 'Internal Server Error.');
}

?>