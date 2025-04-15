<?php
$pageTitle = 'View Inquiry';
require_once '../../config/config.php';
require_once '../../config/functions.php';
require_once '../../includes/auth.php'; // Keep for potential auth functions

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!checkLogin()) {
     setFlashMessage('error', 'You must be logged in to view this page.');
     redirect('modules/auth/login.php');
     exit;
}

// Check permissions to view
if (!checkPermission('view_inquiries')) {
    setFlashMessage('error', 'You do not have permission to access this page.');
    redirect('modules/dashboard/index.php');
    exit;
}

// Check if ID is provided and is numeric
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    setFlashMessage('error', 'Invalid inquiry ID');
    redirect('modules/inquiries/index.php');
    exit;
}

$inquiryId = (int)$_GET['id'];

// Get database instance
$db = db();

// Get inquiry data with business name using db() wrapper
$db->query("SELECT i.*, b.name as business_name
            FROM inquiries i
            LEFT JOIN businesses b ON i.business_id = b.id
            WHERE i.id = :id");
$db->bind(':id', $inquiryId);
$inquiry = $db->single();

// Check if inquiry exists
if (!$inquiry) {
    setFlashMessage('error', 'Inquiry not found');
    redirect('modules/inquiries/index.php');
    exit;
}

// --- Process Form Submissions ---
$errors = [];
$responseMessage = ''; // Keep track of response message for form repopulation on error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // A. Process Response Form Submission
    if (isset($_POST['action']) && $_POST['action'] === 'respond') {
        // Check permission to respond
        if (!checkPermission('respond_to_inquiries')) { // Assuming this permission exists
             $errors[] = 'You do not have permission to respond to inquiries.';
        } elseif (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
             $errors[] = 'Invalid CSRF token. Please try again.';
        } else {
            $responseMessage = sanitize($_POST['response_message']); // Sanitize and store for repopulation

            if (empty($responseMessage)) {
                $errors[] = 'Response message cannot be empty.';
            }

            if (empty($errors)) {
                try {
                    // In a real application, add email sending logic here
                    // mail($inquiry['email'], "RE: " . $inquiry['subject'], $responseMessage, "From: " . getSetting('admin_email'));

                    // Prepare admin notes update
                    $adminNotes = $inquiry['admin_notes'] ?? '';
                    $notePrefix = empty($adminNotes) ? '' : "\n\n"; // Add separator if notes exist
                    $adminNotes .= $notePrefix . formatDateTime(date('Y-m-d H:i:s')) . " - Response sent by " . ($_SESSION['user_name'] ?? 'Admin') . ":\n" . $responseMessage;

                    // Update inquiry status and notes using db() wrapper
                    $db->query("UPDATE inquiries SET
                                  status = :status,
                                  admin_notes = :admin_notes,
                                  updated_at = NOW()
                                  WHERE id = :id");
                    $db->bind(':status', 'responded'); // Set status to responded
                    $db->bind(':admin_notes', $adminNotes);
                    $db->bind(':id', $inquiryId);

                    if ($db->execute()) {
                        // Log the action
                        logActivity(
                            $_SESSION['user_id'], 'respond', 'inquiries',
                            $inquiryId, 'Responded to inquiry from: ' . $inquiry['name']
                        );
                        setFlashMessage('success', 'Response marked as sent successfully.');
                        redirect('modules/inquiries/view.php?id=' . $inquiryId);
                        exit;
                    } else {
                        $errors[] = 'Database error: Failed to update inquiry after sending response.';
                    }
                } catch (Exception $e) {
                    error_log("Error responding to inquiry: " . $e->getMessage());
                    $errors[] = 'An unexpected error occurred while updating the inquiry.';
                }
            }
            // If errors occurred, the script continues to display the page with errors
        }
    } // End Respond Action

    // B. Process Status Update Form Submission
    elseif (isset($_POST['action']) && $_POST['action'] === 'update_status') {
         // Check permission to edit (used for status update)
        if (!checkPermission('edit_inquiries')) {
             $errors[] = 'You do not have permission to update the inquiry status.';
        } elseif (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
             $errors[] = 'Invalid CSRF token. Please try again.';
        } else {
            $newStatus = sanitize($_POST['status']);
            $allowedStatuses = ['new', 'in_progress', 'responded', 'closed', 'spam']; // Use the correct statuses

            if (!in_array($newStatus, $allowedStatuses)) {
                 $errors[] = 'Invalid status selected.';
            }

            if (empty($errors)) {
                try {
                    // Update status using db() wrapper
                    $db->query("UPDATE inquiries SET status = :status, updated_at = NOW() WHERE id = :id");
                    $db->bind(':status', $newStatus);
                    $db->bind(':id', $inquiryId);

                    if ($db->execute()) {
                         // Log the action
                        logActivity(
                            $_SESSION['user_id'], 'update_status', 'inquiries',
                            $inquiryId, 'Updated inquiry status to: ' . $newStatus
                        );
                        setFlashMessage('success', 'Inquiry status updated successfully.');
                        redirect('modules/inquiries/view.php?id=' . $inquiryId);
                        exit;
                    } else {
                        $errors[] = 'Database error: Failed to update inquiry status.';
                    }
                } catch (Exception $e) {
                     error_log("Error updating inquiry status: " . $e->getMessage());
                     $errors[] = 'An unexpected error occurred while updating the status.';
                }
            }
             // If errors occurred, the script continues to display the page with errors
        }
    } // End Update Status Action

} // End POST handling

// --- Include Header and Sidebar ---
include_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
?>

<div class="container px-6 mx-auto">
    <div class="p-4 mt-14">
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                 <div>
                     <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">View Inquiry #<?= $inquiryId ?></h2>
                     <nav class="flex mt-1" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                             <li class="inline-flex items-center">
                                 <a href="<?= BASE_URL ?>/modules/dashboard/index.php" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                                     <i class="fa-solid fa-home mr-2"></i>Dashboard
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
                                     <span class="text-sm font-medium text-gray-500 dark:text-gray-400">View Inquiry</span>
                                 </div>
                             </li>
                         </ol>
                     </nav>
                 </div>
                 <div class="flex-shrink-0 flex flex-wrap gap-2 justify-start md:justify-end">
                     <?php if (hasPermission('edit_inquiries')): ?>
                         <a href="edit.php?id=<?= $inquiryId ?>" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm">
                             <i class="fa-solid fa-edit mr-2"></i> Edit
                         </a>
                     <?php endif; ?>
                     <?php if (hasPermission('delete_inquiries')): ?>
                         <button onclick="confirmDelete(<?= $inquiryId ?>, '<?= htmlspecialchars(addslashes($inquiry['name']), ENT_QUOTES) ?>')" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg shadow-sm">
                             <i class="fa-solid fa-trash mr-2"></i> Delete
                         </button>
                     <?php endif; ?>
                     <a href="index.php" class="inline-flex items-center justify-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg shadow-sm">
                         <i class="fa-solid fa-arrow-left mr-2"></i> Back to List
                     </a>
                 </div>
             </div>
        </div>


        <!-- Display Errors -->
        <?php if (!empty($errors)): ?>
            <div class="mb-6 p-4 rounded-lg bg-red-100 border-l-4 border-red-500 text-red-700 dark:bg-red-900 dark:text-red-300 dark:border-red-700" role="alert">
                <p class="font-bold mb-2">Error encountered:</p>
                <ul class="list-disc list-inside ml-4">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Display Flash Messages -->
        <?php $flash = getFlashMessage(); if ($flash): ?>
            <div class="mb-6 p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' ?>" role="alert">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Inquiry Details Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Inquiry Details</h3>
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= getInquiryStatusBadgeClass($inquiry['status']) ?>">
                            <?= getInquiryStatusLabel($inquiry['status']) ?>
                        </span>
                    </div>
                    <div class="p-6">
                        <?php if (!empty($inquiry['subject'])): ?>
                            <h4 class="text-xl font-medium text-gray-800 dark:text-gray-100 mb-4"><?= htmlspecialchars($inquiry['subject']) ?></h4>
                        <?php endif; ?>

                        <div class="mb-6">
                             <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line"><?= nl2br(htmlspecialchars($inquiry['message'])) ?></p>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">From</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200"><?= htmlspecialchars($inquiry['name']) ?></dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200"><?= htmlspecialchars($inquiry['email']) ?></dd>
                                </div>
                                <?php if ($inquiry['phone']): ?>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200"><?= htmlspecialchars($inquiry['phone']) ?></dd>
                                </div>
                                <?php endif; ?>
                                <?php if ($inquiry['business_name']): ?>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Related Business</dt>
                                     <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">
                                         <?php if ($inquiry['business_id']): // Make it a link if ID exists ?>
                                             <a href="../businesses/view.php?id=<?= $inquiry['business_id'] ?>" class="text-blue-600 hover:underline dark:text-blue-400">
                                                 <?= htmlspecialchars($inquiry['business_name']) ?>
                                             </a>
                                         <?php else: ?>
                                             <?= htmlspecialchars($inquiry['business_name']) ?>
                                         <?php endif; ?>
                                     </dd>
                                </div>
                                <?php endif; ?>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date Received</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200"><?= formatDateTime($inquiry['created_at']) ?></dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200"><?= formatDateTime($inquiry['updated_at']) ?></dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Admin Notes Card -->
                <?php if (!empty($inquiry['admin_notes'])): ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Admin Notes</h3>
                    </div>
                    <div class="p-6">
                        <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg max-h-60 overflow-y-auto">
                            <pre class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap font-sans text-sm"><?= htmlspecialchars($inquiry['admin_notes']) ?></pre>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Response Form Card -->
                <?php if (hasPermission('respond_to_inquiries') && $inquiry['status'] !== 'spam'): // Added permission check ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Respond to Inquiry</h3>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="view.php?id=<?= $inquiryId ?>">
                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                            <input type="hidden" name="action" value="respond">

                            <div class="mb-4">
                                <label for="response_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Response Message <span class="text-red-600">*</span></label>
                                <textarea id="response_message" name="response_message" rows="5" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required><?= htmlspecialchars($responseMessage) ?></textarea>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">This message will be added to admin notes. Email sending needs to be implemented separately.</p>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg focus:ring-4 focus:outline-none focus:ring-green-300 dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-800">
                                    <i class="fa-solid fa-paper-plane mr-2"></i> Mark as Responded & Save Note
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar Column -->
            <div class="space-y-6">
                <!-- Status Update Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Update Status</h3>
                    </div>
                    <div class="p-6">
                        <?php if (hasPermission('edit_inquiries')): // Use edit permission for status update ?>
                        <form method="POST" action="view.php?id=<?= $inquiryId ?>">
                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                            <input type="hidden" name="action" value="update_status">

                            <div class="mb-4">
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sr-only">Status</label>
                                <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="new" <?= $inquiry['status'] === 'new' ? 'selected' : '' ?>>New</option>
                                    <option value="in_progress" <?= $inquiry['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="responded" <?= $inquiry['status'] === 'responded' ? 'selected' : '' ?>>Responded</option>
                                    <option value="closed" <?= $inquiry['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                                    <option value="spam" <?= $inquiry['status'] === 'spam' ? 'selected' : '' ?>>Spam</option>
                                </select>
                            </div>

                            <div class="flex justify-end">
                                 <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800">
                                    <i class="fa-solid fa-save mr-2"></i> Update Status
                                </button>
                            </div>
                        </form>
                        <?php else: ?>
                        <div class="flex items-center justify-center p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Status updates disabled.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Quick Actions</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <?php if ($inquiry['email']): ?>
                            <li>
                                <a href="mailto:<?= htmlspecialchars($inquiry['email']) ?>" class="group flex items-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                    <i class="fa-solid fa-envelope mr-2 fa-fw text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400"></i> Email <?= htmlspecialchars($inquiry['name']) ?>
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if ($inquiry['phone']): ?>
                             <li>
                                <a href="tel:<?= htmlspecialchars($inquiry['phone']) ?>" class="group flex items-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                    <i class="fa-solid fa-phone mr-2 fa-fw text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400"></i> Call <?= htmlspecialchars($inquiry['name']) ?>
                                </a>
                            </li>
                            <?php endif; ?>
                             <?php if ($inquiry['business_id']): ?>
                             <li>
                                <a href="../businesses/view.php?id=<?= $inquiry['business_id'] ?>" class="group flex items-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                    <i class="fa-solid fa-building mr-2 fa-fw text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400"></i> View Business Profile
                                </a>
                            </li>
                            <?php endif; ?>

                             <li><hr class="border-gray-200 dark:border-gray-600 my-2"></li>

                             <li>
                                <a href="index.php" class="group flex items-center text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 font-medium">
                                    <i class="fa-solid fa-list mr-2 fa-fw text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-400"></i> All Inquiries
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div> <!-- End Grid -->
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden overflow-y-auto overflow-x-hidden flex items-center justify-center bg-gray-900 bg-opacity-50 dark:bg-opacity-80">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Confirm Deletion
                </h3>
                <button type="button" onclick="closeDeleteModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <i class="fa-solid fa-times"></i>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-5">
                <p class="text-gray-600 dark:text-gray-400 mb-5">Are you sure you want to delete the inquiry from <strong id="deleteInquiryName" class="font-medium text-gray-800 dark:text-gray-200"></strong>? This action cannot be undone.</p>
                <form id="deleteForm" action="delete.php" method="POST" class="flex justify-end space-x-3">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" id="deleteId" name="id" value="">
                    <button type="button" onclick="closeDeleteModal()" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-900">
                        Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    function confirmDelete(id, name) {
        document.getElementById('deleteId').value = id;
        // Decode HTML entities potentially encoded by addslashes/htmlspecialchars
        const tempElem = document.createElement('textarea');
        tempElem.innerHTML = name;
        document.getElementById('deleteInquiryName').textContent = tempElem.value;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').setAttribute('aria-hidden', 'false');
        // Optionally focus the cancel button
        document.querySelector('#deleteModal button[onclick="closeDeleteModal()"]').focus();
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
         document.getElementById('deleteModal').setAttribute('aria-hidden', 'true');
    }

    // Close modal if escape key is pressed
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !document.getElementById('deleteModal').classList.contains('hidden')) {
            closeDeleteModal();
        }
    });
</script>

<?php
// Helper function to get inquiry status badge class (Add dark mode classes)
function getInquiryStatusBadgeClass($status) {
    switch ($status) {
        case 'new':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
        case 'in_progress':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        case 'responded':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'closed':
            return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
        case 'spam':
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    }
}

// Helper function to get inquiry status label
function getInquiryStatusLabel($status) {
     // Use the same statuses as defined in the ALTER TABLE command
    switch ($status) {
        case 'new': return 'New';
        case 'in_progress': return 'In Progress';
        case 'responded': return 'Responded';
        case 'closed': return 'Closed';
        case 'spam': return 'Spam';
        default: return ucfirst(str_replace('_', ' ', $status));
    }
}
?>

<?php include_once '../../includes/footer.php'; ?>