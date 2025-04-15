<?php
require_once '../../config/config.php';
require_once '../../config/functions.php';
require_once '../../includes/auth.php';

// Verify CSRF token if this was a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Invalid CSRF token');
        redirect('modules/auth/login.php');
    }
}

// Perform logout
$result = logoutUser();

// Set appropriate flash message based on result
if ($result['success']) {
    setFlashMessage('success', 'You have been logged out successfully');
} else {
    setFlashMessage('error', 'There was an error logging out');
}

// Redirect to login page
redirect('modules/auth/login.php');