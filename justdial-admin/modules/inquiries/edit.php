<?php
$pageTitle = 'Edit Inquiry';
require_once '../../config/config.php';
require_once '../../config/functions.php';
require_once '../../includes/auth.php'; // Keep for potential auth functions if needed later

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!checkLogin()) {
     setFlashMessage('error', 'You must be logged in to view this page.');
     redirect('modules/auth/login.php'); // Adjust path if needed
     exit;
}

// Check permissions
if (!checkPermission('edit_inquiries')) {
    setFlashMessage('error', 'You do not have permission to access this page.');
    redirect('modules/inquiries/index.php');
    exit;
}

// Check if ID is provided and is numeric
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    setFlashMessage('error', 'Invalid inquiry ID');
    redirect('modules/inquiries/index.php');
    exit; // Stop script execution
}

$inquiryId = (int)$_GET['id'];

// Get database instance
$db = db();

// Get inquiry data using the db() wrapper
$db->query("SELECT * FROM inquiries WHERE id = :id");
$db->bind(':id', $inquiryId);
$inquiry = $db->single();

// Check if inquiry exists
if (!$inquiry) {
    setFlashMessage('error', 'Inquiry not found');
    redirect('modules/inquiries/index.php');
    exit; // Stop script execution
}

// Get businesses for dropdown using the db() wrapper
$db->query("SELECT id, name FROM businesses ORDER BY name ASC");
$businesses = $db->resultSet();

// Process form submission
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid CSRF token. Please try again.';
        // Reload the form with original data on CSRF failure to prevent losing unsaved work easily
    } else {
        // Create a temporary array to hold updated data to avoid modifying original $inquiry if validation fails
        $updatedData = [];
        $updatedData['name'] = sanitize($_POST['name']);
        $updatedData['email'] = sanitize($_POST['email']);
        $updatedData['phone'] = sanitize($_POST['phone']);
        // Handle potentially empty business selection correctly for NULL insertion
        $updatedData['business_id'] = !empty($_POST['business_id']) ? (int)$_POST['business_id'] : null;
        $updatedData['subject'] = sanitize($_POST['subject']);
        $updatedData['message'] = sanitize($_POST['message']);
        $updatedData['status'] = sanitize($_POST['status']);
        $updatedData['admin_notes'] = sanitize($_POST['admin_notes']);

        // Validate required fields
        if (empty($updatedData['name'])) {
            $errors[] = 'Name is required';
        }

        if (empty($updatedData['email'])) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($updatedData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        if (empty($updatedData['message'])) {
            $errors[] = 'Message is required';
        }

        // Allowed statuses (optional but good practice)
        $allowedStatuses = ['new', 'in_progress', 'responded', 'closed', 'spam'];
        if (!in_array($updatedData['status'], $allowedStatuses)) {
             $errors[] = 'Invalid status selected';
             $updatedData['status'] = $inquiry['status']; // Reset to original if invalid
        }

        // If no validation errors, proceed with update
        if (empty($errors)) {
            try {
                // Prepare parameters for binding
                 $params = [
                     ':name' => $updatedData['name'],
                     ':email' => $updatedData['email'],
                     ':phone' => $updatedData['phone'],
                     ':business_id' => $updatedData['business_id'], // NULL will be handled by bind
                     ':subject' => $updatedData['subject'],
                     ':message' => $updatedData['message'],
                     ':status' => $updatedData['status'],
                     ':admin_notes' => $updatedData['admin_notes'],
                     ':id' => $inquiryId
                 ];

                // Use the db() wrapper for the update query
                $db->query("UPDATE inquiries SET
                              name = :name,
                              email = :email,
                              phone = :phone,
                              business_id = :business_id,
                              subject = :subject,
                              message = :message,
                              status = :status,
                              admin_notes = :admin_notes,
                              updated_at = NOW()
                              WHERE id = :id");

                // Bind parameters (assuming your db wrapper handles types correctly)
                foreach ($params as $key => $value) {
                    // Explicitly handle NULL for business_id if needed by your wrapper
                     if ($key === ':business_id' && $value === null) {
                         // Check if your db->bind method requires explicit type for NULL
                         // Example: $db->bind($key, $value, PDO::PARAM_NULL);
                         // If it handles NULL automatically, just the normal bind is fine.
                         $db->bind($key, null); // Adjust if necessary
                     } else {
                         $db->bind($key, $value);
                     }
                }

                // Execute the update
                if ($db->execute()) {
                    // Log the action using standard logActivity
                    logActivity(
                        $_SESSION['user_id'], // Assumes user_id is in session
                        'update',
                        'inquiries',
                        $inquiryId,
                        'Updated inquiry from: ' . $updatedData['name']
                    );

                    setFlashMessage('success', 'Inquiry updated successfully');
                    redirect('modules/inquiries/view.php?id=' . $inquiryId);
                    exit; // Exit after redirect
                } else {
                    $errors[] = 'Database error: Failed to update inquiry.';
                }
            } catch (Exception $e) {
                 // Log detailed error for debugging
                 // error_log("Error updating inquiry: " . $e->getMessage());
                 $errors[] = 'An unexpected error occurred. Please try again.';
            }
        }

         // If validation failed or DB error occurred, merge validated data back to $inquiry for form repopulation
         if (!empty($errors)) {
             $inquiry = array_merge($inquiry, $updatedData);
         }
    }
}

// --- Include Header and Sidebar ---
include_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
?>

<div class="container px-6 mx-auto">
    <div class="p-4 mt-14">
        <div class="mb-6">
            <div class="flex justify-between items-center">
                 <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Edit Inquiry #<?= $inquiryId ?></h2>
                 <a href="view.php?id=<?= $inquiryId ?>" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                     <i class="fa-solid fa-arrow-left mr-1"></i> Back to View
                 </a>
            </div>
            <nav class="flex mt-1" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="<?= BASE_URL ?>/modules/dashboard/index.php" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                            <i class="fa-solid fa-home mr-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fa-solid fa-chevron-right text-gray-400 mx-2"></i>
                            <a href="index.php" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">Inquiries</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fa-solid fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Edit Inquiry</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

         <?php if (!empty($errors)): ?>
            <div class="mb-6 p-4 rounded-lg bg-red-100 border-l-4 border-red-500 text-red-700" role="alert">
                <p class="font-bold mb-2">Please fix the following errors:</p>
                <ul class="list-disc list-inside ml-4">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
             <!-- Pass inquiryId in action URL -->
            <form method="POST" action="edit.php?id=<?= $inquiryId ?>">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name <span class="text-red-600">*</span></label>
                        <input type="text" id="name" name="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="<?= htmlspecialchars($inquiry['name'] ?? '') ?>" required>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-red-600">*</span></label>
                        <input type="email" id="email" name="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="<?= htmlspecialchars($inquiry['email'] ?? '') ?>" required>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                        <input type="tel" id="phone" name="phone" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="<?= htmlspecialchars($inquiry['phone'] ?? '') ?>">
                    </div>

                    <div>
                        <label for="business_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Related Business</label>
                        <select id="business_id" name="business_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">-- None --</option>
                            <?php foreach ($businesses as $business): ?>
                                <option value="<?= $business['id'] ?>" <?= (isset($inquiry['business_id']) && $inquiry['business_id'] == $business['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($business['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                         <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Optional: Link this inquiry to a specific business.</p>
                    </div>

                     <div class="md:col-span-2">
                        <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subject</label>
                        <input type="text" id="subject" name="subject" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="<?= htmlspecialchars($inquiry['subject'] ?? '') ?>">
                    </div>

                    <div class="md:col-span-2">
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Message <span class="text-red-600">*</span></label>
                        <textarea id="message" name="message" rows="5" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required><?= htmlspecialchars($inquiry['message'] ?? '') ?></textarea>
                    </div>

                     <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="new" <?= (isset($inquiry['status']) && $inquiry['status'] === 'new') ? 'selected' : '' ?>>New</option>
                            <option value="in_progress" <?= (isset($inquiry['status']) && $inquiry['status'] === 'in_progress') ? 'selected' : '' ?>>In Progress</option>
                            <option value="responded" <?= (isset($inquiry['status']) && $inquiry['status'] === 'responded') ? 'selected' : '' ?>>Responded</option>
                            <option value="closed" <?= (isset($inquiry['status']) && $inquiry['status'] === 'closed') ? 'selected' : '' ?>>Closed</option>
                            <option value="spam" <?= (isset($inquiry['status']) && $inquiry['status'] === 'spam') ? 'selected' : '' ?>>Spam</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="admin_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Admin Notes</label>
                        <textarea id="admin_notes" name="admin_notes" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Internal notes about this inquiry..."><?= htmlspecialchars($inquiry['admin_notes'] ?? '') ?></textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">These notes are only visible to administrators.</p>
                    </div>

                     <!-- Display Timestamps -->
                    <div class="md:col-span-2 border-t border-gray-200 dark:border-gray-700 pt-4 mt-4 text-sm text-gray-500 dark:text-gray-400">
                         <p><strong class="font-medium text-gray-700 dark:text-gray-300">Created:</strong> <?= isset($inquiry['created_at']) ? formatDateTime($inquiry['created_at']) : 'N/A' ?></p>
                         <p><strong class="font-medium text-gray-700 dark:text-gray-300">Last Updated:</strong> <?= isset($inquiry['updated_at']) ? formatDateTime($inquiry['updated_at']) : 'Never' ?></p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end space-x-3">
                    <a href="view.php?id=<?= $inquiryId ?>" class="py-2 px-4 text-sm font-medium text-gray-700 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800">
                        <i class="fa-solid fa-save mr-2"></i> Update Inquiry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once '../../includes/footer.php'; ?>