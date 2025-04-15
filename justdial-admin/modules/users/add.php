<?php
$pageTitle = 'Add User';
require_once '../../includes/header.php';

// Check if user has permission
if (!isManager()) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('modules/dashboard/index.php');
}

// Initialize user data
$user = [
    'name' => '',
    'email' => '',
    'username' => '',
    'phone' => '',
    'role' => 'user',
    'status' => 'active',
    'address' => '',
    'city' => '',
    'state' => '',
    'postal_code' => '',
    'country' => 'India', // Default country
    'bio' => ''
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid CSRF token');
        redirect('modules/users/add.php');
    }

    // Validate and sanitize input
    $user['name'] = sanitize($_POST['name']);
    $user['email'] = sanitize($_POST['email']);
    $user['username'] = sanitize($_POST['username']);
    $user['phone'] = sanitize($_POST['phone']);
    $user['role'] = sanitize($_POST['role']);
    $user['status'] = sanitize($_POST['status']);
    $user['address'] = sanitize($_POST['address']);
    $user['city'] = sanitize($_POST['city']);
    $user['state'] = sanitize($_POST['state']);
    $user['postal_code'] = sanitize($_POST['postal_code']);
    $user['country'] = sanitize($_POST['country']);
    $user['bio'] = sanitize($_POST['bio']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate required fields
    $errors = [];
    if (empty($user['name'])) {
        $errors[] = 'Name is required';
    }
    
    if (empty($user['email'])) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    // Check if email already exists
    $db = db();
    $db->query("SELECT id FROM users WHERE email = :email");
    $db->bind(':email', $user['email']);
    $existingUser = $db->single();
    
    if ($existingUser) {
        $errors[] = 'Email already exists';
    }
    
    // Check if username already exists (if provided)
    if (!empty($user['username'])) {
        $db->query("SELECT id FROM users WHERE username = :username");
        $db->bind(':username', $user['username']);
        $existingUser = $db->single();
        
        if ($existingUser) {
            $errors[] = 'Username already exists';
        }
    }
    
    // Validate password
    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long';
    } elseif ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }

    // Handle profile image upload
    $profileImage = null;
    if (!empty($_FILES['profile_image']['name'])) {
        $uploadResult = uploadFile($_FILES['profile_image'], 'users', ['jpg', 'jpeg', 'png', 'gif']);
        if ($uploadResult['success']) {
            $profileImage = $uploadResult['filename'];
        } else {
            $errors[] = 'Image upload failed: ' . $uploadResult['message'];
        }
    }
    
    // If no errors, insert the user
    if (empty($errors)) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $db->query("INSERT INTO users (
                name, email, username, phone, password, role, status,
                address, city, state, postal_code, country, bio, profile_image,
                created_at, updated_at
            ) VALUES (
                :name, :email, :username, :phone, :password, :role, :status,
                :address, :city, :state, :postal_code, :country, :bio, :profile_image,
                NOW(), NOW()
            )");
            
            $db->bind(':name', $user['name']);
            $db->bind(':email', $user['email']);
            $db->bind(':username', $user['username']);
            $db->bind(':phone', $user['phone']);
            $db->bind(':password', $hashedPassword);
            $db->bind(':role', $user['role']);
            $db->bind(':status', $user['status']);
            $db->bind(':address', $user['address']);
            $db->bind(':city', $user['city']);
            $db->bind(':state', $user['state']);
            $db->bind(':postal_code', $user['postal_code']);
            $db->bind(':country', $user['country']);
            $db->bind(':bio', $user['bio']);
            $db->bind(':profile_image', $profileImage);
            
            $db->execute();
            $userId = $db->lastInsertId();
            
            // Log activity
            logActivity($_SESSION['user_id'], 'create', 'users', $userId, 'Created user: ' . $user['name']);
            
            setFlashMessage('success', 'User added successfully');
            redirect('modules/users/view.php?id=' . $userId);
            
        } catch (Exception $e) {
            setFlashMessage('error', 'Error adding user: ' . $e->getMessage());
            redirect('modules/users/add.php');
        }
    }
}
?>

<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center my-6">
        <h2 class="text-2xl font-semibold text-gray-800">Add User</h2>
        <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300">
            <i class="fa-solid fa-arrow-left mr-2"></i> Back to Users
        </a>
    </div>
    
    <!-- Flash Messages -->
    <?php include_once '../../config/functions.php'; ?>
    
    <!-- Error Messages -->
    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Error!</strong>
            <ul class="mt-1 ml-4 list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <form method="POST" enctype="multipart/form-data" class="p-6">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h2>
                </div>
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-600">*</span></label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-600">*</span></label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>
                
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-600">*</span></label>
                    <input type="password" id="password" name="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-600">*</span></label>
                    <input type="password" id="confirm_password" name="confirm_password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>
                
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select id="role" name="role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                        <option value="business_owner" <?= $user['role'] === 'business_owner' ? 'selected' : '' ?>>Business Owner</option>
                        <option value="manager" <?= $user['role'] === 'manager' ? 'selected' : '' ?>>Manager</option>
                        <?php if (isAdmin()): ?>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <!-- <option value="pending" <?= $user['status'] === 'pending' ? 'selected' : '' ?>>Pending</option> -->
                        <option value="banned" <?= $user['status'] === 'banned' ? 'selected' : '' ?>>Banned</option>
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label for="profile_image" class="block text-sm font-medium text-gray-700 mb-1">Profile Image</label>
                    <input type="file" id="profile_image" name="profile_image" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" accept="image/*">
                    <p class="mt-1 text-xs text-gray-500">PNG, JPG or GIF (MAX. 2MB)</p>
                </div>
                
                <div class="md:col-span-2">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Contact Information</h2>
                </div>
                
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" id="address" name="address" value="<?= htmlspecialchars($user['address']) ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" id="city" name="city" value="<?= htmlspecialchars($user['city']) ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                
                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State/Province</label>
                    <input type="text" id="state" name="state" value="<?= htmlspecialchars($user['state']) ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">ZIP/Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" value="<?= htmlspecialchars($user['postal_code']) ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                    <input type="text" id="country" name="country" value="<?= htmlspecialchars($user['country']) ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                
                <div class="md:col-span-2">
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                    <textarea id="bio" name="bio" rows="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"><?= htmlspecialchars($user['bio']) ?></textarea>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="window.location.href='index.php'" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg mr-2">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
                    Add User
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>