<?php
// api/v1/handlers/city_handler.php
require_once __DIR__ . '/../includes/data_functions.php';

/**
 * Handles GET /cities requests
 */
function handleGetCities(PDO $pdo, array $params): void {
    $result = api_get_cities($pdo, $params);
    send_success_response($result['cities'], ['pagination' => $result['pagination']]);
}

/**
 * Handles GET /cities/{name}/{state}/businesses requests
 * Note: Using name/state in URL can be tricky with special characters. Consider using City ID if possible.
 */
function handleGetBusinessesByCity(PDO $pdo, string $city_name, string $state_name, array $params): void {
     // Add city/state to params for filtering in api_get_businesses
     $params['city'] = $city_name;
     $params['state'] = $state_name;
     $result = api_get_businesses($pdo, $params);
     send_success_response($result['businesses'], ['pagination' => $result['pagination']]);
}
?>