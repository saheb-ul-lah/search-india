<?php
require_once '../../config/config.php';
require_once '../../config/functions.php';
require_once '../../includes/auth.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlashMessage('error', 'You must be logged in to access this page');
    redirect('modules/auth/login.php');
}

// Check permissions
if (!isAdmin() && !isManager()) {
    setFlashMessage('error', 'You do not have permission to perform this action');
    redirect('modules/dashboard/index.php');
}

// Verify CSRF token
if (!verifyCSRFToken($_POST['csrf_token'])) {
    setFlashMessage('error', 'Invalid CSRF token');
    redirect('modules/services/index.php');
}

// Get service ID
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if (!$id) {
    setFlashMessage('error', 'Invalid service ID');
    redirect('modules/services/index.php');
}

try {
    $db = db();
    
    // First get service name for logging
    $db->query("SELECT name FROM services WHERE id = :id");
    $db->bind(':id', $id);
    $service = $db->single();
    
    if (!$service) {
        setFlashMessage('error', 'Service not found');
        redirect('modules/services/index.php');
    }
    
    // Delete the service
    $db->query("DELETE FROM services WHERE id = :id");
    $db->bind(':id', $id);
    
    if ($db->execute()) {
        logActivity($_SESSION['user_id'], 'delete', 'services', $id, 'Deleted service: ' . $service['name']);
        setFlashMessage('success', 'Service deleted successfully');
    } else {
        setFlashMessage('error', 'Failed to delete service');
    }
} catch (Exception $e) {
    setFlashMessage('error', 'Error: ' . $e->getMessage());
}

redirect('modules/services/index.php');