<?php
// api/v1/handlers/setting_handler.php
require_once __DIR__ . '/../includes/data_functions.php';

/**
 * Handles GET /settings requests
 */
function handleGetSettings(PDO $pdo): void {
    $settings = api_get_public_settings($pdo);
    send_success_response($settings);
}
?>