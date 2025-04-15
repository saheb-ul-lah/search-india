<?php
$pageTitle = 'Edit User';
require_once '../../includes/header.php';

// Check if user has permission
if (!isManager()) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('modules/dashboard/index.php');
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('error', 'User ID is required');
    redirect('modules/users/index.php');
}

$userId = (int)$_GET['id'];

// Get user data
$db = db();
$db->query("SELECT * FROM users WHERE id = :id");
$db->bind(':id', $userId);
$user = $db->single();

if (!$user) {
    setFlashMessage('error', 'User not found');
    redirect('modules/users/index.php');
}

// Check if current user has permission to edit this user
if (!isAdmin() && $user['role'] === 'admin' && $_SESSION['user_id'] != $userId) {
    setFlashMessage('error', 'You do not have permission to edit an admin user');
    redirect('modules/users/index.php');
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid CSRF token');
        redirect('modules/users/edit.php?id=' . $userId);
    }

    // Validate and sanitize input
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $username = sanitize($_POST['username']);
    $phone = sanitize($_POST['phone']);
    $role = sanitize($_POST['role']);
    $status = sanitize($_POST['status']);
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $state = sanitize($_POST['state']);
    $postal_code = sanitize($_POST['postal_code']);
    $country = sanitize($_POST['country']);
    $bio = sanitize($_POST['bio']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate required fields
    $errors = [];
    if (empty($name)) {
        $errors[] = 'Name is required';
    }

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }

    // Check if email already exists (excluding current user)
    $db->query("SELECT id FROM users WHERE email = :email AND id != :id");
    $db->bind(':email', $email);
    $db->bind(':id', $userId);
    $existingUser = $db->single();

    if ($existingUser) {
        $errors[] = 'Email already exists';
    }

    // Check if username already exists (if provided, excluding current user)
    if (!empty($username)) {
        $db->query("SELECT id FROM users WHERE username = :username AND id != :id");
        $db->bind(':username', $username);
        $db->bind(':id', $userId);
        $existingUser = $db->single();

        if ($existingUser) {
            $errors[] = 'Username already exists';
        }
    }

    // Validate password if provided
    if (!empty($password)) {
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        } elseif ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }
    }

    // Handle profile image upload
    $profileImage = $user['profile_image'];
    if (!empty($_FILES['profile_image']['name'])) {
        $uploadResult = uploadFile($_FILES['profile_image'], 'users', ['jpg', 'jpeg', 'png', 'gif']);
        if ($uploadResult['success']) {
            // Delete old profile image if exists
            if ($profileImage) {
                deleteFile('users/' . $profileImage);
            }
            $profileImage = $uploadResult['filename'];
        } else {
            $errors[] = 'Image upload failed: ' . $uploadResult['message'];
        }
    }

    // If no errors, update the user
    if (empty($errors)) {
        try {
            // Start building the query
            $query = "UPDATE users SET 
                     name = :name, 
                     email = :email, 
                     username = :username, 
                     phone = :phone, 
                     role = :role, 
                     status = :status,
                     address = :address, 
                     city = :city, 
                     state = :state, 
                     postal_code = :postal_code, 
                     country = :country, 
                     bio = :bio, 
                     profile_image = :profile_image, 
                     updated_at = NOW()";

            // Add password to query if provided
            if (!empty($password)) {
                $query .= ", password = :password";
            }

            // Add WHERE clause
            $query .= " WHERE id = :id";

            $db->query($query);

            $db->bind(':name', $name);
            $db->bind(':email', $email);
            $db->bind(':username', $username);
            $db->bind(':phone', $phone);
            $db->bind(':role', $role);
            $db->bind(':status', $status);
            $db->bind(':address', $address);
            $db->bind(':city', $city);
            $db->bind(':state', $state);
            $db->bind(':postal_code', $postal_code);
            $db->bind(':country', $country);
            $db->bind(':bio', $bio);
            $db->bind(':profile_image', $profileImage);

            if (!empty($password)) {
                $db->bind(':password', password_hash($password, PASSWORD_DEFAULT));
            }

            $db->bind(':id', $userId);

            $db->execute();

            // Log activity
            logActivity($_SESSION['user_id'], 'update', 'users', $userId, 'Updated user: ' . $name);

            setFlashMessage('success', 'User updated successfully');
            redirect('modules/users/view.php?id=' . $userId);
        } catch (Exception $e) {
            setFlashMessage('error', 'Error updating user: ' . $e->getMessage());
            redirect('modules/users/edit.php?id=' . $userId);
        }
    }
}
?>

<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center my-6">
        <h2 class="text-2xl font-semibold text-gray-800">Edit User</h2>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="view.php?id=<?= $userId ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center">
                <i class="fa-solid fa-eye mr-2"></i> View User
            </a>
            <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back to Users
            </a>
        </div>
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
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-600">*</span></label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-gray-500">(leave blank to keep current)</span></label>
                    <input type="password" id="password" name="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select id="role" name="role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User </option>
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
                        <option value="pending" <?= $user['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="banned" <?= $user['status'] === 'banned' ? 'selected' : '' ?>>Banned</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="profile_image" class="block text-sm font-medium text-gray-700 mb-1">Profile Image</label>
                    <?php if ($user['profile_image']): ?>
                        <div class="mb-3 flex items-center">
                            <img src="<?= UPLOADS_URL ?>/users/<?= $user['profile_image'] ?>" alt="<?= htmlspecialchars($user['name'] ?? '') ?>" class="h-16 w-16 rounded-full object-cover mr-3">
                            <span class="text-sm text-gray-500">Current profile image</span>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="profile_image" name="profile_image" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" accept="image/*">
                    <p class="mt-1 text-xs text-gray-500">PNG, JPG or GIF (MAX. 2MB)</p>
                </div>

                <div class="md:col-span-2">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Contact Information</h2>
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" id="address" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" id="city" name="city" value="<?= htmlspecialchars($user['city'] ?? '') ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State/Province</label>
                    <input type="text" id="state" name="state" value="<?= htmlspecialchars($user['state'] ?? '') ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">ZIP/Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" value="<?= htmlspecialchars($user['postal_code'] ?? '') ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                    <input type="text" id="country" name="country" value="<?= htmlspecialchars($user['country'] ?? '') ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <div class="md:col-span-2">
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                    <textarea id="bio" name="bio" rows="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" onclick="window.location.href='index.php'" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg mr-2">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>