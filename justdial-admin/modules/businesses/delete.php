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
    setFlashMessage('error', 'Business ID is required');
    redirect('modules/businesses/index.php');
}

$businessId = (int)$_GET['id'];

// Get business data
$db = db();
$db->query("SELECT * FROM businesses WHERE id = :id");
$db->bind(':id', $businessId);
$business = $db->single();

if (!$business) {
    setFlashMessage('error', 'Business not found');
    redirect('modules/businesses/index.php');
}

try {
    // Begin transaction
    $db->beginTransaction();
    
    // Delete business images
    if ($business['logo']) {
        deleteFile('businesses/' . $business['logo']);
    }
    
    if ($business['cover_image']) {
        deleteFile('businesses/' . $business['cover_image']);
    }
    
    // Get and delete all business gallery images
    $db->query("SELECT image FROM business_images WHERE business_id = :business_id");
    $db->bind(':business_id', $businessId);
    $images = $db->resultSet();
    
    foreach ($images as $image) {
        deleteFile('businesses/' . $image['image']);
    }
    
    // Delete business records
    // The foreign key constraints will automatically delete related records
    $db->query("DELETE FROM businesses WHERE id = :id");
    $db->bind(':id', $businessId);
    $db->execute();
    
    // Commit transaction
    $db->endTransaction();
    
    // Log activity
    logActivity($_SESSION['user_id'], 'delete', 'businesses', $businessId, 'Deleted business: ' . $business['name']);
    
    setFlashMessage('success', 'Business deleted successfully');
    redirect('modules/businesses/index.php');
    
} catch (Exception $e) {
    // Rollback transaction on error
    $db->cancelTransaction();
    setFlashMessage('error', 'Error deleting business: ' . $e->getMessage());
    redirect('modules/businesses/index.php');
}
?>