<?php
// api/v1/includes/functions.php
require_once __DIR__ . '/../config/config.php';

// --- Response Helpers ---

/**
 * Sends a JSON response.
 * @param int $status_code HTTP status code.
 * @param array $data Data to encode as JSON.
 */
function send_json_response(int $status_code, array $data): void {
    http_response_code($status_code);
    header('Content-Type: application/json; charset=utf-8');
    // Allow specified origins for CORS
    header('Access-Control-Allow-Origin: ' . API_ALLOWED_ORIGINS);
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); // Add methods as needed
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-API-KEY'); // Add allowed headers

    // Handle preflight OPTIONS request for CORS
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        exit(0);
    }

    echo json_encode($data);
    exit; // Stop script execution after sending response
}

/**
 * Sends a success JSON response.
 * @param mixed $data The payload.
 * @param array $meta Optional metadata (e.g., pagination).
 */
function send_success_response($data, array $meta = []): void {
    $response = ['status' => 'success', 'data' => $data];
    if (!empty($meta)) {
        $response['meta'] = $meta;
    }
    send_json_response(200, $response);
}

/**
 * Sends an error JSON response.
 * @param int $status_code HTTP status code.
 * @param string $message Error message.
 * @param array|null $details Optional additional error details.
 */
function send_error_response(int $status_code, string $message, ?array $details = null): void {
    $response = ['status' => 'error', 'message' => $message];
    if ($details !== null) {
        $response['details'] = $details;
    }
    send_json_response($status_code, $response);
}

// --- Authentication & Rate Limiting ---

/**
 * Validates the API key provided in the X-API-KEY header.
 * Checks rate limit.
 * @param PDO $pdo Database connection.
 * @return bool True if valid and within limit, false otherwise (sends error response on failure).
 */
function authenticate_and_rate_limit(PDO $pdo): bool {
    $api_key = $_SERVER['HTTP_X_API_KEY'] ?? null;

    if (!$api_key) {
        send_error_response(401, 'Unauthorized: API Key missing.');
        return false; // Should not reach here due to exit in send_error_response
    }

    try {
        $stmt = $pdo->prepare("SELECT id, api_key_hash, status, requests_today, last_request_time FROM api_keys WHERE status = 'active'");
        // In a real system with many keys, you'd add a WHERE clause based on key_name or a prefix of the key
        // For simplicity now, we fetch all active keys and verify. This is NOT efficient for many keys.
        $stmt->execute();
        $keys = $stmt->fetchAll();

        $valid_key_record = null;
        foreach ($keys as $key_record) {
            if (password_verify($api_key, $key_record['api_key_hash'])) {
                $valid_key_record = $key_record;
                break;
            }
        }

        if (!$valid_key_record) {
            send_error_response(401, 'Unauthorized: Invalid API Key.');
            return false;
        }

        // --- Rate Limiting Check (Simple Daily Limit) ---
        $today = date('Y-m-d');
        $last_request_date = $valid_key_record['last_request_time'] ? date('Y-m-d', strtotime($valid_key_record['last_request_time'])) : null;

        $requests_today = $valid_key_record['requests_today'];

        // Reset count if it's a new day
        if ($last_request_date !== $today) {
            $requests_today = 0;
        }

        if ($requests_today >= API_RATE_LIMIT) {
            send_error_response(429, 'Too Many Requests: Daily rate limit exceeded.');
            return false;
        }

        // Update request count and timestamp
        $update_stmt = $pdo->prepare("UPDATE api_keys SET requests_today = ?, last_request_time = NOW() WHERE id = ?");
        $update_stmt->execute([$requests_today + 1, $valid_key_record['id']]);

        return true;

    } catch (\PDOException $e) {
        error_log("API Authentication/Rate Limit Error: " . $e->getMessage());
        send_error_response(500, 'Internal Server Error during authentication.');
        return false;
    }
}


// --- Input Helpers ---

/**
 * Gets pagination parameters from the request.
 * @return array ['page', 'limit', 'offset']
 */
function get_pagination_params(): array {
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : API_ITEMS_PER_PAGE; // Limit max items per page
    $offset = ($page - 1) * $limit;
    return ['page' => $page, 'limit' => $limit, 'offset' => $offset];
}

/**
 * Builds pagination metadata for the response.
 * @param int $page Current page.
 * @param int $limit Items per page.
 * @param int $total_items Total items found.
 * @return array Pagination metadata.
 */
function build_pagination_meta(int $page, int $limit, int $total_items): array {
    $total_pages = ($limit > 0) ? ceil($total_items / $limit) : 0;
    return [
        'current_page' => $page,
        'per_page'     => $limit,
        'total_items'  => $total_items,
        'total_pages'  => $total_pages,
    ];
}

?>