<?php
// api/v1/handlers/business_handler.php
require_once __DIR__ . '/../includes/data_functions.php';

/**
 * Handles GET /businesses requests
 */
function handleGetBusinesses(PDO $pdo, array $params): void {
    $result = api_get_businesses($pdo, $params);
    send_success_response($result['businesses'], ['pagination' => $result['pagination']]);
}

/**
 * Handles GET /businesses/{id} requests
 */
function handleGetBusinessById(PDO $pdo, int $id): void {
    $business = api_get_business_by_id($pdo, $id);
    if ($business) {
        send_success_response($business);
    } else {
        send_error_response(404, "Business not found.");
    }
}
?>