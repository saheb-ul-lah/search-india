<?php
// --- TEMPORARY: Enable detailed errors for debugging ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- REMOVE after debugging ---

$pageTitle = 'Add Inquiry';
require_once '../../config/config.php';
require_once '../../config/functions.php';
require_once '../../includes/auth.php';

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!checkLogin()) {
     setFlashMessage('error', 'You must be logged in to perform this action.');
     redirect('modules/auth/login.php');
     exit;
}

// Check permissions
if (!checkPermission('add_inquiries')) {
    setFlashMessage('error', 'You do not have permission to access this page.');
    redirect('modules/inquiries/index.php');
    exit;
}

// Get database instance
$db = db();

// Get businesses for dropdown
$db->query("SELECT id, name FROM businesses WHERE status = 'active' ORDER BY name ASC");
$businesses = $db->resultSet();

// Initialize variables for form repopulation
$formData = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'business_id' => null,
    'subject' => '',
    'message' => '',
    'status' => 'new',
    'admin_notes' => ''
];
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid CSRF token. Please try submitting the form again.');
        redirect('modules/inquiries/add.php');
        exit;
    }

    // Sanitize and store submitted data
    $formData['name'] = sanitize($_POST['name']);
    $formData['email'] = sanitize($_POST['email']);
    $formData['phone'] = sanitize($_POST['phone']);
    $formData['business_id'] = !empty($_POST['business_id']) ? (int)$_POST['business_id'] : null;
    $formData['subject'] = sanitize($_POST['subject']);
    $formData['message'] = sanitize($_POST['message']);
    $formData['status'] = sanitize($_POST['status']);
    $formData['admin_notes'] = sanitize($_POST['admin_notes']);

    // Validation
    if (empty($formData['name'])) { $errors[] = 'Name is required'; }
    if (empty($formData['email'])) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    if (empty($formData['message'])) { $errors[] = 'Message is required'; }

    $allowedStatuses = ['new', 'in_progress', 'responded', 'closed', 'spam'];
    if (!in_array($formData['status'], $allowedStatuses)) {
         $errors[] = 'Invalid status selected';
         $formData['status'] = 'new'; // Reset
    }

    // Proceed if no validation errors
    if (empty($errors)) {
        try {
             $params = [
                 ':name' => $formData['name'],
                 ':email' => $formData['email'],
                 ':phone' => $formData['phone'],
                 ':business_id' => $formData['business_id'],
                 ':subject' => $formData['subject'],
                 ':message' => $formData['message'],
                 ':status' => $formData['status'],
                 ':admin_notes' => $formData['admin_notes'],
             ];

            $db->query("INSERT INTO inquiries (
                          name, email, phone, business_id, subject, message, status, admin_notes, created_at, updated_at
                          ) VALUES (
                          :name, :email, :phone, :business_id, :subject, :message, :status, :admin_notes, NOW(), NOW()
                          )");

            foreach ($params as $key => $value) {
                 if ($key === ':business_id' && $value === null) {
                     // Ensure your bind method handles NULL correctly or use explicit type:
                     // $db->bind($key, null, PDO::PARAM_NULL);
                     $db->bind($key, null);
                 } else {
                     $db->bind($key, $value);
                 }
            }

            if ($db->execute()) {
                $newInquiryId = $db->lastInsertId();
                logActivity(
                    $_SESSION['user_id'], 'create', 'inquiries',
                    $newInquiryId, 'Manually added inquiry from: ' . $formData['name']
                );
                setFlashMessage('success', 'Inquiry added successfully');
                redirect('modules/inquiries/view.php?id=' . $newInquiryId);
                exit;
            } else {
                // Attempt to get error info if execute returns false but doesn't throw
                 $errors[] = 'Database error: Failed to add inquiry. (Execute returned false)';
                 // You might need a method in your DB wrapper to get the actual PDO error info here
                 // e.g., $dbError = $db->getErrorInfo(); error_log(print_r($dbError, true));
            }
        } catch (Exception $e) {
             // Log detailed error for debugging
             error_log("Error adding inquiry: " . $e->getMessage() . "\nTrace:\n" . $e->getTraceAsString());
             $errors[] = 'An unexpected error occurred. Please try again.';
             // Optionally expose more details for debugging if display_errors is on:
             // if (ini_get('display_errors')) {
             //     $errors[] = 'Debug Info: ' . $e->getMessage();
             // }
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
                 <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Add New Inquiry</h2>
                 <a href="index.php" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                     <i class="fa-solid fa-arrow-left mr-1"></i> Back to Inquiries List
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
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Add Inquiry</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Display Validation Errors -->
        <?php if (!empty($errors)): ?>
            <div class="mb-6 p-4 rounded-lg bg-red-100 border-l-4 border-red-500 text-red-700 dark:bg-red-900 dark:text-red-300 dark:border-red-700" role="alert">
                <p class="font-bold mb-2">Please fix the following errors:</p>
                <ul class="list-disc list-inside ml-4">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Display Flash Messages (e.g., for CSRF error) -->
        <?php $flash = getFlashMessage(); if ($flash): ?>
            <div class="mb-6 p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' ?>" role="alert">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>


        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <form method="POST" action="add.php">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name <span class="text-red-600">*</span></label>
                        <input type="text" id="name" name="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="<?= htmlspecialchars($formData['name']) ?>" required>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-red-600">*</span></label>
                        <input type="email" id="email" name="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="<?= htmlspecialchars($formData['email']) ?>" required>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                        <input type="tel" id="phone" name="phone" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="<?= htmlspecialchars($formData['phone']) ?>" placeholder="(Optional)">
                    </div>

                    <div>
                        <label for="business_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Related Business</label>
                        <select id="business_id" name="business_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">-- None --</option>
                            <?php foreach ($businesses as $business): ?>
                                <option value="<?= $business['id'] ?>" <?= ($formData['business_id'] == $business['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($business['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                         <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Optional: Link this inquiry to a specific business.</p>
                    </div>

                     <div class="md:col-span-2">
                        <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subject</label>
                        <input type="text" id="subject" name="subject" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="<?= htmlspecialchars($formData['subject']) ?>" placeholder="(Optional)">
                    </div>

                    <div class="md:col-span-2">
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Message <span class="text-red-600">*</span></label>
                        <textarea id="message" name="message" rows="5" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required><?= htmlspecialchars($formData['message']) ?></textarea>
                    </div>

                     <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="new" <?= ($formData['status'] === 'new') ? 'selected' : '' ?>>New</option>
                            <option value="in_progress" <?= ($formData['status'] === 'in_progress') ? 'selected' : '' ?>>In Progress</option>
                            <option value="responded" <?= ($formData['status'] === 'responded') ? 'selected' : '' ?>>Responded</option>
                            <option value="closed" <?= ($formData['status'] === 'closed') ? 'selected' : '' ?>>Closed</option>
                            <option value="spam" <?= ($formData['status'] === 'spam') ? 'selected' : '' ?>>Spam</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="admin_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Admin Notes</label>
                        <textarea id="admin_notes" name="admin_notes" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Internal notes (visible only to admins)..."><?= htmlspecialchars($formData['admin_notes']) ?></textarea>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end space-x-3">
                    <a href="index.php" class="py-2 px-4 text-sm font-medium text-gray-700 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800">
                        <i class="fa-solid fa-plus mr-2"></i> Add Inquiry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once '../../includes/footer.php'; ?>