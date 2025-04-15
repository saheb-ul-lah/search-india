<?php
require_once '../../config/config.php';
require_once '../../config/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('modules/auth/login.php');
}

// Check if user has permission
if (!isManager()) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('modules/dashboard/index.php');
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('error', 'Category ID is required');
    redirect('modules/categories/index.php');
}

$categoryId = (int)$_GET['id'];

// Get category data
$db = db();
$db->query("SELECT * FROM categories WHERE id = :id");
$db->bind(':id', $categoryId);
$category = $db->single();

if (!$category) {
    setFlashMessage('error', 'Category not found');
    redirect('modules/categories/index.php');
}

// Check if category has businesses
$db->query("SELECT COUNT(*) as count FROM business_categories WHERE category_id = :category_id");
$db->bind(':category_id', $categoryId);
$businessCount = $db->single()['count'];

if ($businessCount > 0) {
    setFlashMessage('error', 'Cannot delete category because it has ' . $businessCount . ' businesses associated with it');
    redirect('modules/categories/index.php');
}

// Check if category has child categories
$db->query("SELECT COUNT(*) as count FROM categories WHERE parent_id = :parent_id");
$db->bind(':parent_id', $categoryId);
$childCount = $db->single()['count'];

if ($childCount > 0) {
    setFlashMessage('error', 'Cannot delete category because it has ' . $childCount . ' child categories');
    redirect('modules/categories/index.php');
}

try {
    // Delete category image if exists
    if ($category['image']) {
        deleteFile('categories/' . $category['image']);
    }
    
    // Delete category
    $db->query("DELETE FROM categories WHERE id = :id");
    $db->bind(':id', $categoryId);
    $db->execute();
    
    // Log activity
    logActivity($_SESSION['user_id'], 'delete', 'categories', $categoryId, 'Deleted category: ' . $category['name']);
    
    setFlashMessage('success', 'Category deleted successfully');
    redirect('modules/categories/index.php');
    
} catch (Exception $e) {
    setFlashMessage('error', 'Error deleting category: ' . $e->getMessage());
    redirect('modules/categories/index.php');
}
?>