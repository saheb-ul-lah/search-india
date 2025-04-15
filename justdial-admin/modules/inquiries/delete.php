<?php
require_once '../../config/config.php';
require_once '../../config/functions.php';
require_once '../../includes/auth.php';

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!checkLogin()) {
     // No need for flash message here, checkLogin likely handles redirect or script exit
     // If checkLogin doesn't exit/redirect, add:
     // setFlashMessage('error', 'Authentication required.');
     // redirect('modules/auth/login.php');
     exit; // Make sure script stops if not logged in
}

// Check permissions
if (!checkPermission('delete_inquiries')) {
    setFlashMessage('error', 'You do not have permission to perform this action.');
    redirect('modules/inquiries/index.php');
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('error', 'Invalid request method.');
    redirect('modules/inquiries/index.php');
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    setFlashMessage('error', 'Invalid CSRF token. Please try again.');
    redirect('modules/inquiries/index.php');
    exit;
}

// Check if ID is provided and is numeric
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    setFlashMessage('error', 'Invalid inquiry ID provided.');
    redirect('modules/inquiries/index.php');
    exit;
}

$inquiryId = (int)$_POST['id'];

// Get database instance
$db = db();

// --- Try block for database operations ---
try {
    // Get inquiry data (like name) for logging BEFORE deleting
    $db->query("SELECT name FROM inquiries WHERE id = :id");
    $db->bind(':id', $inquiryId);
    $inquiry = $db->single();

    // Check if inquiry actually exists before trying to delete
    if (!$inquiry) {
        setFlashMessage('error', 'Inquiry not found or already deleted.');
        redirect('modules/inquiries/index.php');
        exit;
    }

    $inquiryName = $inquiry['name']; // Store name for logging

    // Prepare and execute the DELETE statement using db() wrapper
    $db->query("DELETE FROM inquiries WHERE id = :id");
    $db->bind(':id', $inquiryId);

    if ($db->execute()) {
        // Log the action using logActivity
        logActivity(
            $_SESSION['user_id'], // Get user ID from session
            'delete',             // Action type
            'inquiries',          // Entity type
            $inquiryId,           // Entity ID (though it's now deleted)
            'Deleted inquiry from: ' . $inquiryName // Description
        );

        setFlashMessage('success', 'Inquiry deleted successfully.');
    } else {
        // If execute returns false without throwing an exception
        setFlashMessage('error', 'Database error: Failed to delete inquiry.');
        // Log the error if possible (might need a method in your DB class)
        // error_log("Failed to delete inquiry ID: " . $inquiryId);
    }

} catch (Exception $e) {
    // Catch any other exceptions during DB operations
    error_log("Error deleting inquiry ID " . $inquiryId . ": " . $e->getMessage());
    setFlashMessage('error', 'An unexpected error occurred while trying to delete the inquiry.');
}

// Redirect back to the index page regardless of success/failure (flash message indicates outcome)
redirect('modules/inquiries/index.php');
exit; // Ensure script stops after redirect

?>