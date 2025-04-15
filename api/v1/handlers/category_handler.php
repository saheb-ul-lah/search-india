<?php
// api/v1/handlers/category_handler.php
require_once __DIR__ . '/../includes/data_functions.php';

/**
 * Handles GET /categories requests
 */
function handleGetCategories(PDO $pdo, array $params): void {
    $result = api_get_categories($pdo, $params);
    send_success_response($result['categories'], ['pagination' => $result['pagination']]);
}

/**
 * Handles GET /categories/{id_or_slug} requests
 */
function handleGetCategoryByIdOrSlug(PDO $pdo, $id_or_slug): void {
    $category = api_get_category_by_id_or_slug($pdo, $id_or_slug);
    if ($category) {
        send_success_response($category);
    } else {
        send_error_response(404, "Category not found.");
    }
}

/**
 * Handles GET /categories/{id_or_slug}/businesses requests
 */
function handleGetBusinessesByCategory(PDO $pdo, $id_or_slug, array $params): void {
     // First, get the category to ensure it exists and get its ID
     $category = api_get_category_by_id_or_slug($pdo, $id_or_slug);
     if (!$category) {
         send_error_response(404, "Category not found.");
         return;
     }
     // Add category ID to params for filtering in api_get_businesses
     $params['category'] = $category['id']; // Use ID for consistency
     $result = api_get_businesses($pdo, $params);
     send_success_response($result['businesses'], ['pagination' => $result['pagination']]);
}
?>