<?php
require_once '../../config/config.php';
require_once '../../config/functions.php';
require_once '../../includes/auth.php';

// Check if user is logged in
checkLogin();

// Check permissions
checkPermission('delete_users');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('error', 'Invalid request method');
    redirect('modules/users/index.php');
}

// Verify CSRF token
if (!verifyCSRFToken($_POST['csrf_token'])) {
    setFlashMessage('error', 'Invalid CSRF token');
    redirect('modules/users/index.php');
}

// Get user ID
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if (!$id) {
    setFlashMessage('error', 'Invalid user ID');
    redirect('modules/users/index.php');
}

// Check if user exists
$stmt = $pdo->prepare("SELECT id, name, role, profile_image FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    setFlashMessage('error', 'User not found');
    redirect('modules/users/index.php');
}

// Prevent deleting own account
if ($_SESSION['user_id'] == $id) {
    setFlashMessage('error', 'You cannot delete your own account');
    redirect('modules/users/index.php');
}

// Check if user is an admin (only admins can delete admins)
if ($user['role'] === 'admin' && !isAdmin()) {
    setFlashMessage('error', 'You do not have permission to delete an admin user');
    redirect('modules/users/index.php');
}

// Delete user's profile image if exists
if ($user['profile_image']) {
    $imagePath = '../../uploads/users/' . $user['profile_image'];
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}

// Delete user
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
if ($stmt->execute([$id])) {
    // Log the action
    logAction('Deleted user: ' . $user['name']);
    
    setFlashMessage('success', 'User deleted successfully');
} else {
    setFlashMessage('error', 'Failed to delete user');
}

// Redirect back to users list
redirect('modules/users/index.php');