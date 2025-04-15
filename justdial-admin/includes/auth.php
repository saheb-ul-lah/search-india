<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/functions.php';

// Register a new user
function registerUser($name, $email, $password, $confirmPassword) {
    // Validate input
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        return ['success' => false, 'message' => 'All fields are required'];
    }

    if ($password !== $confirmPassword) {
        return ['success' => false, 'message' => 'Passwords do not match'];
    }

    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        return ['success' => false, 'message' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email format'];
    }

    // Check if email exists
    $db = db();
    $db->query("SELECT id FROM users WHERE email = :email");
    $db->bind(':email', $email);
    $existingUser = $db->single();

    if ($existingUser) {
        return ['success' => false, 'message' => 'Email already exists'];
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if email verification is enabled
    $emailVerificationEnabled = getSetting('enable_email_verification', '1') === '1';
    
    // Generate verification token if needed
    $verificationToken = $emailVerificationEnabled ? bin2hex(random_bytes(32)) : null;

    // Insert user with proper parameter binding
    $db->query("INSERT INTO users (name, email, password, verification_token, email_verified) 
                VALUES (:name, :email, :password, :verification_token, :email_verified)");
    
    $db->bind(':name', $name);
    $db->bind(':email', $email);
    $db->bind(':password', $hashedPassword);
    $db->bind(':verification_token', $verificationToken);
    $db->bind(':email_verified', $emailVerificationEnabled ? 0 : 1);

    if ($db->execute()) {
        $userId = $db->lastInsertId();
        
        logActivity($userId, 'register', 'users', $userId, 'User registered');

        if ($emailVerificationEnabled) {
            // TODO: Implement email sending
            // sendVerificationEmail($email, $verificationToken);
        }

        return [
            'success' => true,
            'message' => $emailVerificationEnabled ? 
                'Registration successful! Please check your email to verify your account.' : 
                'Registration successful! You can now login.',
            'user_id' => $userId,
            'email_verified' => !$emailVerificationEnabled
        ];
    } else {
        return ['success' => false, 'message' => 'Registration failed. Please try again.'];
    }
}

// Login user
function loginUser($email, $password, $rememberMe = false)
{
    // Validate input
    if (empty($email) || empty($password)) {
        return [
            'success' => false,
            'message' => 'Email and password are required'
        ];
    }

    // Get user by email
    $db = db();
    $db->query("SELECT * FROM users WHERE email = :email");
    $db->bind(':email', $email);
    $user = $db->single();

    if (!$user) {
        return [
            'success' => false,
            'message' => 'Invalid email or password'
        ];
    }

    // Verify password
    if (!password_verify($password, $user['password'])) {
        return [
            'success' => false,
            'message' => 'Invalid email or password'
        ];
    }

    // Check if user is active
    if ($user['status'] !== 'active') {
        return [
            'success' => false,
            'message' => 'Your account is ' . $user['status'] . '. Please contact the administrator.'
        ];
    }

    // Check if email is verified
    if (getSetting('enable_email_verification', '1') === '1' && !$user['email_verified']) {
        return [
            'success' => false,
            'message' => 'Please verify your email before logging in.'
        ];
    }

    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];

    // Update last login time
    $db->query("UPDATE users SET last_login = NOW() WHERE id = :id");
    $db->bind(':id', $user['id']);
    $db->execute();

    // Log activity
    logActivity($user['id'], 'login', 'users', $user['id'], 'User logged in');

    // Handle remember me
    if ($rememberMe) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 30 * 24 * 60 * 60); // 30 days

        // Store token in database
        $db->query("INSERT INTO user_tokens (user_id, token, expires) VALUES (:user_id, :token, :expires)");
        $db->bind(':user_id', $user['id']);
        $db->bind(':token', $token);
        $db->bind(':expires', $expires);
        $db->execute();

        // Set cookie
        setcookie('remember_token', $token, time() + 30 * 24 * 60 * 60, '/', '', false, true);
    }

    return [
        'success' => true,
        'message' => 'Login successful',
        'user' => $user
    ];
}

// Logout user
function logoutUser()
{
    // Log activity if user is logged in
    if (isset($_SESSION['user_id'])) {
        logActivity($_SESSION['user_id'], 'logout', 'users', $_SESSION['user_id'], 'User logged out');
    }

    // Clear session
    session_unset();
    session_destroy();

    // Clear remember me cookie
    if (isset($_COOKIE['remember_token'])) {
        // Delete token from database
        $db = db();
        $db->query("DELETE FROM user_tokens WHERE token = :token");
        $db->bind(':token', $_COOKIE['remember_token']);
        $db->execute();

        // Delete cookie
        setcookie('remember_token', '', time() - 3600, '/', '', false, true);
    }

    return [
        'success' => true,
        'message' => 'Logout successful'
    ];
}

// Request password reset
function requestPasswordReset($email)
{
    if (empty($email)) {
        return [
            'success' => false,
            'message' => 'Email is required'
        ];
    }

    // Check if email exists
    $db = db();
    $db->query("SELECT * FROM users WHERE email = :email");
    $db->bind(':email', $email);
    $user = $db->single();

    if (!$user) {
        return [
            'success' => false,
            'message' => 'Email not found'
        ];
    }

    // Generate reset token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', time() + 1 * 60 * 60); // 1 hour

    // Store token in database
    $db->query("UPDATE users SET reset_token = :token, reset_token_expires = :expires WHERE id = :id");
    $db->bind(':token', $token);
    $db->bind(':expires', $expires);
    $db->bind(':id', $user['id']);
    $db->execute();

    // Log activity
    logActivity($user['id'], 'password_reset_request', 'users', $user['id'], 'Password reset requested');

    // TODO: Send password reset email
    // sendPasswordResetEmail($email, $token);

    return [
        'success' => true,
        'message' => 'Password reset instructions have been sent to your email'
    ];
}

// Reset password
function resetPassword($token, $password, $confirmPassword)
{
    if (empty($token) || empty($password) || empty($confirmPassword)) {
        return [
            'success' => false,
            'message' => 'All fields are required'
        ];
    }

    if ($password !== $confirmPassword) {
        return [
            'success' => false,
            'message' => 'Passwords do not match'
        ];
    }

    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        return [
            'success' => false,
            'message' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'
        ];
    }

    // Check if token exists and is valid
    $db = db();
    $db->query("SELECT * FROM users WHERE reset_token = :token AND reset_token_expires > NOW()");
    $db->bind(':token', $token);
    $user = $db->single();

    if (!$user) {
        return [
            'success' => false,
            'message' => 'Invalid or expired token'
        ];
    }

    // Hash new password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Update password and clear token
    $db->query("UPDATE users SET password = :password, reset_token = NULL, reset_token_expires = NULL WHERE id = :id");
    $db->bind(':password', $hashedPassword);
    $db->bind(':id', $user['id']);
    $db->execute();

    // Log activity
    logActivity($user['id'], 'password_reset', 'users', $user['id'], 'Password reset completed');

    return [
        'success' => true,
        'message' => 'Password has been reset successfully'
    ];
}

// Verify email
function verifyEmail($token)
{
    if (empty($token)) {
        return [
            'success' => false,
            'message' => 'Invalid token'
        ];
    }

    // Check if token exists
    $db = db();
    $db->query("SELECT * FROM users WHERE verification_token = :token");
    $db->bind(':token', $token);
    $user = $db->single();

    if (!$user) {
        return [
            'success' => false,
            'message' => 'Invalid token'
        ];
    }

    // Update user
    $db->query("UPDATE users SET email_verified = 1, verification_token = NULL WHERE id = :id");
    $db->bind(':id', $user['id']);
    $db->execute();

    // Log activity
    logActivity($user['id'], 'email_verified', 'users', $user['id'], 'Email verified');

    return [
        'success' => true,
        'message' => 'Email verified successfully'
    ];
}

// Check if user is authenticated via remember me token
function checkRememberMe()
{
    if (isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];

        $db = db();
        $db->query("SELECT u.* FROM users u 
                    INNER JOIN user_tokens t ON u.id = t.user_id 
                    WHERE t.token = :token AND t.expires > NOW()");
        $db->bind(':token', $token);
        $user = $db->single();

        if ($user) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            // Log activity
            logActivity($user['id'], 'auto_login', 'users', $user['id'], 'User automatically logged in via remember me');

            return true;
        }
    }

    return false;
}