<?php
require_once 'config.php';
require_once 'database.php';

// Initialize database connection
function db() {
    static $db = null;
    if ($db === null) {
        $db = new Database();
    }
    return $db;
}

// Sanitize input data
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Generate slug from string
function generateSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', ' ', $string);
    $string = preg_replace('/\s/', '-', $string);
    return $string;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Check if user is manager
function isManager() {
    return isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'manager' || $_SESSION['user_role'] === 'admin');
}

// Redirect to a specific page
function redirect($page) {
    header('Location: ' . BASE_URL . '/' . $page);
    exit;
}

// Flash message
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message,
        'time' => time()
    ];
}

// Get flash message
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// Generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    if (!isset($_SESSION[CSRF_TOKEN_NAME]) || $token !== $_SESSION[CSRF_TOKEN_NAME]) {
        return false;
    }
    return true;
}

// Format date
function formatDate($date, $format = DATE_FORMAT) {
    return date($format, strtotime($date));
}

// Format datetime
function formatDateTime($datetime, $format = DATETIME_FORMAT) {
    return date($format, strtotime($datetime));
}

// Get setting value
function getSetting($key, $default = '') {
    $db = db();
    $db->query("SELECT setting_value FROM settings WHERE setting_key = :key");
    $db->bind(':key', $key);
    $result = $db->single();
    
    // Return the setting value or default if not found
    return $result ? $result['setting_value'] : $default;
}
// Update setting value
function updateSetting($key, $value) {
    $db = db();
    $db->query("INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value) 
                ON DUPLICATE KEY UPDATE setting_value = :value");
    $db->bind(':key', $key);
    $db->bind(':value', $value);
    return $db->execute();
}

// Upload file
function uploadFile($file, $directory, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif']) {
    // Check if file was uploaded without errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'message' => 'Error uploading file: ' . $file['error']
        ];
    }
    
    // Get file extension
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Check if file type is allowed
    if (!in_array($fileExt, $allowedTypes)) {
        return [
            'success' => false,
            'message' => 'File type not allowed. Allowed types: ' . implode(', ', $allowedTypes)
        ];
    }
    
    // Create unique filename
    $newFilename = uniqid() . '.' . $fileExt;
    $uploadPath = UPLOADS_PATH . '/' . $directory;
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $uploadPath . '/' . $newFilename)) {
        return [
            'success' => true,
            'filename' => $newFilename,
            'path' => $directory . '/' . $newFilename
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Failed to move uploaded file'
        ];
    }
}

// Delete file
function deleteFile($path) {
    $fullPath = UPLOADS_PATH . '/' . $path;
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    return false;
}

// Log activity
function logActivity($userId, $action, $entityType = null, $entityId = null, $description = '') {
    $db = db();
    $db->query("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, user_agent) 
                VALUES (:user_id, :action, :entity_type, :entity_id, :description, :ip_address, :user_agent)");
    $db->bind(':user_id', $userId);
    $db->bind(':action', $action);
    $db->bind(':entity_type', $entityType);
    $db->bind(':entity_id', $entityId);
    $db->bind(':description', $description);
    $db->bind(':ip_address', $_SERVER['REMOTE_ADDR']);
    $db->bind(':user_agent', $_SERVER['HTTP_USER_AGENT']);
    return $db->execute();
}

// Get pagination
function getPagination($totalItems, $currentPage = 1, $perPage = ITEMS_PER_PAGE) {
    $totalPages = ceil($totalItems / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'current_page' => $currentPage,
        'per_page' => $perPage,
        'total_items' => $totalItems,
        'total_pages' => $totalPages,
        'offset' => $offset
    ];
}

// Generate pagination links
function paginationLinks($pagination, $baseUrl) {
    $links = '';
    $currentPage = $pagination['current_page'];
    $totalPages = $pagination['total_pages'];
    
    if ($totalPages <= 1) {
        return '';
    }
    
    $links .= '<div class="flex items-center justify-between mt-6">';
    $links .= '<div class="flex-1 flex justify-between">';
    
    // Previous button
    if ($currentPage > 1) {
        $links .= '<a href="' . $baseUrl . '?page=' . ($currentPage - 1) . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Previous</a>';
    } else {
        $links .= '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">Previous</span>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $links .= '<a href="' . $baseUrl . '?page=' . ($currentPage + 1) . '" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Next</a>';
    } else {
        $links .= '<span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">Next</span>';
    }
    
    $links .= '</div>';
    $links .= '</div>';
    
    return $links;
}

// Get average rating
function getAverageRating($businessId) {
    $db = db();
    $db->query("SELECT AVG(rating) as avg_rating FROM reviews WHERE business_id = :business_id AND status = 'approved'");
    $db->bind(':business_id', $businessId);
    $result = $db->single();
    
    return $result && $result['avg_rating'] ? round($result['avg_rating'], 1) : 0;
}

// Get rating count
function getRatingCount($businessId) {
    $db = db();
    $db->query("SELECT COUNT(*) as count FROM reviews WHERE business_id = :business_id AND status = 'approved'");
    $db->bind(':business_id', $businessId);
    $result = $db->single();
    
    return $result ? $result['count'] : 0;
}

// Get business categories
function getBusinessCategories($businessId) {
    $db = db();
    $db->query("SELECT c.* FROM categories c 
                INNER JOIN business_categories bc ON c.id = bc.category_id 
                WHERE bc.business_id = :business_id");
    $db->bind(':business_id', $businessId);
    return $db->resultSet();
}

// Get random color
function getRandomColor() {
    $colors = [
        'bg-gradient-to-r from-blue-500 to-indigo-600',
        'bg-gradient-to-r from-green-500 to-teal-600',
        'bg-gradient-to-r from-purple-500 to-pink-600',
        'bg-gradient-to-r from-yellow-500 to-orange-600',
        'bg-gradient-to-r from-red-500 to-pink-600',
        'bg-gradient-to-r from-indigo-500 to-purple-600',
        'bg-gradient-to-r from-teal-500 to-cyan-600',
        'bg-gradient-to-r from-orange-500 to-red-600'
    ];
    
    return $colors[array_rand($colors)];
}

function hasPermission($permission) {
    if (isAdmin()) return true;
    
    // You'll need to implement your actual permission checking logic here
    // This is just a placeholder
    return in_array($permission, $_SESSION['user_permissions'] ?? []);
}

function logAction($action) {
    logActivity(
        $_SESSION['user_id'] ?? 0,
        'action',
        'services',
        null,
        $action
    );
}

function checkLogin() {
    return isLoggedIn();
}
// Check if user has permission
function checkPermission($permission) {
    if (isAdmin()) return true;
    
    // You'll need to implement your actual permission checking logic here
    // This is just a placeholder
    return in_array($permission, $_SESSION['user_permissions'] ?? []);
}

if (!function_exists('renderPagination')) {
    function renderPagination($currentPage, $totalPages, $baseUrl, $separator = '&') {
        if ($totalPages <= 1) return '';

        $output = '<nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">';
        $disabledClass = 'text-gray-300 dark:text-gray-500 cursor-not-allowed';
        $enabledClass = 'text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700';
        $activeClass = 'z-10 bg-blue-50 dark:bg-blue-900 border-blue-500 dark:border-blue-400 text-blue-600 dark:text-blue-300';
        $inactiveClass = 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700';

        // Previous button
        $prevPage = $currentPage - 1;
        $output .= '<a href="' . ($currentPage > 1 ? $baseUrl . $separator . 'page=' . $prevPage : '#') . '"
                       class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium ' . ($currentPage <= 1 ? $disabledClass : $enabledClass) . '">';
        $output .= '<span class="sr-only">Previous</span><i class="fa-solid fa-chevron-left h-5 w-5"></i></a>';

        // Page numbers
        $linksToShow = 5;
        $startPage = max(1, $currentPage - floor($linksToShow / 2));
        $endPage = min($totalPages, $startPage + $linksToShow - 1);
        $startPage = max(1, $endPage - $linksToShow + 1); // Adjust start if end is reached early

        if ($startPage > 1) {
             $output .= '<a href="' . $baseUrl . $separator . 'page=1" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium ' . $inactiveClass . '">1</a>';
             if ($startPage > 2) {
                  $output .= '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-400">...</span>';
             }
        }

        for ($i = $startPage; $i <= $endPage; $i++) {
             $output .= '<a href="' . $baseUrl . $separator . 'page=' . $i . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium ' . ($i === $currentPage ? $activeClass : $inactiveClass) . '">' . $i . '</a>';
        }

        if ($endPage < $totalPages) {
             if ($endPage < $totalPages - 1) {
                  $output .= '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-400">...</span>';
             }
              $output .= '<a href="' . $baseUrl . $separator . 'page=' . $totalPages . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium ' . $inactiveClass . '">' . $totalPages . '</a>';
        }

        // Next button
        $nextPage = $currentPage + 1;
        $output .= '<a href="' . ($currentPage < $totalPages ? $baseUrl . $separator . 'page=' . $nextPage : '#') . '"
                       class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium ' . ($currentPage >= $totalPages ? $disabledClass : $enabledClass) . '">';
        $output .= '<span class="sr-only">Next</span><i class="fa-solid fa-chevron-right h-5 w-5"></i></a>';

        $output .= '</nav>';
        return $output;
    }
}