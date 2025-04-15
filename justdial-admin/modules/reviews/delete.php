<?php
require_once '../../config/config.php';
require_once '../../config/functions.php';
require_once '../../includes/auth.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlashMessage('error', 'You must be logged in to perform this action');
    redirect('modules/auth/login.php');
}

// Check if user has permission
if (!isManager()) {
    setFlashMessage('error', 'You do not have permission to perform this action');
    redirect('modules/dashboard/index.php');
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('error', 'Review ID is required');
    redirect('modules/reviews/index.php');
}

$reviewId = (int)$_GET['id'];

// Get review data
$db = db();
$db->query("SELECT * FROM reviews WHERE id = :id");
$db->bind(':id', $reviewId);
$review = $db->single();

if (!$review) {
    setFlashMessage('error', 'Review not found');
    redirect('modules/reviews/index.php');
}

// Process deletion
try {
    // Delete the review
    $db->query("DELETE FROM reviews WHERE id = :id");
    $db->bind(':id', $reviewId);
    $db->execute();
    
    // Log activity
    logActivity(
        $_SESSION['user_id'], 
        'delete', 
        'reviews', 
        $reviewId, 
        'Deleted review #' . $reviewId . ' for business ID: ' . $review['business_id']
    );
    
    setFlashMessage('success', 'Review deleted successfully');
} catch (Exception $e) {
    setFlashMessage('error', 'Error deleting review: ' . $e->getMessage());
}

// Redirect back to reviews list
redirect('modules/reviews/index.php');