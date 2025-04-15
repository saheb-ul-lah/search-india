<?php
$pageTitle = 'Inquiries';
require_once '../../config/config.php';
require_once '../../config/functions.php';
require_once '../../includes/auth.php'; // Though not strictly needed for this page if checkLogin/checkPermission are in functions.php

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in - This function MUST exist in included files (now in functions.php)
if (!checkLogin()) {
     // Redirect to login page if not logged in
     setFlashMessage('error', 'You must be logged in to view this page.');
     redirect('modules/auth/login.php'); // Adjust path if needed
     exit; // Stop script execution
}


// Check permissions - This function MUST exist in included files (now in functions.php)
if (!checkPermission('view_inquiries')) {
    setFlashMessage('error', 'You do not have permission to access this page.');
    redirect('modules/dashboard/index.php'); // Redirect to dashboard or appropriate page
    exit; // Stop script execution
}


// Get database instance
$db = db();

// Get filter parameters
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$businessId = isset($_GET['business_id']) ? (int)$_GET['business_id'] : 0;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$startDate = isset($_GET['start_date']) ? sanitize($_GET['start_date']) : '';
$endDate = isset($_GET['end_date']) ? sanitize($_GET['end_date']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Items per page
$offset = ($page - 1) * $limit;

// --- Build Count Query ---
$countQuery = "SELECT COUNT(i.id) as total
               FROM inquiries i
               LEFT JOIN businesses b ON i.business_id = b.id
               WHERE 1=1";
$countParams = [];

if ($status) {
    $countQuery .= " AND i.status = :status";
    $countParams[':status'] = $status;
}
if ($businessId) {
    $countQuery .= " AND i.business_id = :business_id";
    $countParams[':business_id'] = $businessId;
}
if ($search) {
    $countQuery .= " AND (i.name LIKE :search OR i.email LIKE :search OR i.phone LIKE :search OR i.message LIKE :search OR b.name LIKE :search)";
    $countParams[':search'] = "%$search%";
}
if ($startDate) {
    $countQuery .= " AND DATE(i.created_at) >= :start_date";
    $countParams[':start_date'] = $startDate;
}
if ($endDate) {
    $countQuery .= " AND DATE(i.created_at) <= :end_date";
    $countParams[':end_date'] = $endDate;
}

// Execute count query
$db->query($countQuery);
foreach ($countParams as $key => $value) {
    $db->bind($key, $value);
}
$totalItems = $db->single()['total'];
$totalPages = ceil($totalItems / $limit);
// Ensure page is within bounds
$page = max(1, min($page, $totalPages));
$offset = ($page - 1) * $limit; // Recalculate offset if page was adjusted

// --- Build Main Data Query ---
$query = "SELECT i.*, b.name as business_name
          FROM inquiries i
          LEFT JOIN businesses b ON i.business_id = b.id
          WHERE 1=1";
$params = []; // Use the same parameters as the count query

if ($status) {
    $query .= " AND i.status = :status";
    $params[':status'] = $status;
}
if ($businessId) {
    $query .= " AND i.business_id = :business_id";
    $params[':business_id'] = $businessId;
}
if ($search) {
    $query .= " AND (i.name LIKE :search OR i.email LIKE :search OR i.phone LIKE :search OR i.message LIKE :search OR b.name LIKE :search)";
    $params[':search'] = "%$search%";
}
if ($startDate) {
    $query .= " AND DATE(i.created_at) >= :start_date";
    $params[':start_date'] = $startDate;
}
if ($endDate) {
    $query .= " AND DATE(i.created_at) <= :end_date";
    $params[':end_date'] = $endDate;
}

// Add order by and limit
$query .= " ORDER BY i.created_at DESC LIMIT :offset, :limit";
$params[':offset'] = $offset;
$params[':limit'] = $limit;

// Execute main data query
$db->query($query);
foreach ($params as $key => $value) {
     // Bind LIMIT parameters as integers explicitly if your DB class requires it
     if ($key === ':offset' || $key === ':limit') {
         // Assuming your Database class bind method handles types like PDO::PARAM_INT
         // If not, this might need adjustment based on your class implementation
         $db->bind($key, $value, PDO::PARAM_INT);
     } else {
         $db->bind($key, $value);
     }
}
$inquiries = $db->resultSet();

// Get businesses for filter dropdown
$db->query("SELECT id, name FROM businesses ORDER BY name ASC");
$businesses = $db->resultSet();

// --- Include Header and Sidebar ---
// Make sure header includes necessary CSS/JS and starts the HTML structure
include_once '../../includes/header.php';
// Make sure sidebar includes the navigation structure
include_once '../../includes/sidebar.php';
?>

<div class="container px-6 mx-auto">
    <div class="p-4 mt-14">
        <div class="flex justify-between items-center my-6">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Inquiries</h2>
            <?php if (hasPermission('add_inquiries')): ?>
                <a href="add.php" class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-[1.02] shadow-md">
                    <i class="fa-solid fa-plus mr-2"></i> Add Inquiry
                </a>
            <?php endif; ?>
        </div>

        <?php // displayFlashMessages(); // Make sure this function exists and works ?>
        <?php $flash = getFlashMessage(); if ($flash): ?>
            <div class="mb-4 p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>" role="alert">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>


        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <form method="GET" action="index.php" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="new" <?= $status === 'new' ? 'selected' : '' ?>>New</option>
                        <option value="in_progress" <?= $status === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="responded" <?= $status === 'responded' ? 'selected' : '' ?>>Responded</option>
                        <option value="closed" <?= $status === 'closed' ? 'selected' : '' ?>>Closed</option>
                        <option value="spam" <?= $status === 'spam' ? 'selected' : '' ?>>Spam</option>
                    </select>
                </div>

                <div>
                    <label for="business_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Business</label>
                    <select id="business_id" name="business_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">All Businesses</option>
                        <?php foreach ($businesses as $business): ?>
                            <option value="<?= $business['id'] ?>" <?= $businessId == $business['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($business['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fa-solid fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="search" name="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search inquiries..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>

                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="<?= htmlspecialchars($startDate) ?>">
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="<?= htmlspecialchars($endDate) ?>">
                </div>

                <div class="flex items-end md:col-span-3 lg:col-span-5">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg mr-2">
                        <i class="fa-solid fa-filter mr-2"></i> Filter
                    </button>
                    <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg">
                        <i class="fa-solid fa-times mr-2"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Inquiries Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Inquiry Details</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Business</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php if (empty($inquiries)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No inquiries found matching your criteria.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($inquiries as $inquiry): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            <?= htmlspecialchars($inquiry['name']) ?>
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            <?= htmlspecialchars($inquiry['email']) ?>
                                            <?php if ($inquiry['phone']): ?>
                                                <span class="mx-1">|</span> <?= htmlspecialchars($inquiry['phone']) ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-1" title="<?= htmlspecialchars($inquiry['message']) ?>">
                                            <?= htmlspecialchars(substr($inquiry['message'], 0, 100)) . (strlen($inquiry['message']) > 100 ? '...' : '') ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-300">
                                    <?= $inquiry['business_name'] ? htmlspecialchars($inquiry['business_name']) : '<span class="text-gray-400 italic">N/A</span>' ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= getInquiryStatusBadgeClass($inquiry['status']) ?>">
                                    <?= getInquiryStatusLabel($inquiry['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <?= formatDate($inquiry['created_at']) ?> <!-- Use formatDate function -->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <?php if (hasPermission('view_inquiries')): // Added view permission check ?>
                                <a href="view.php?id=<?= $inquiry['id'] ?>" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 mr-3" title="View">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (hasPermission('edit_inquiries')): ?>
                                <a href="edit.php?id=<?= $inquiry['id'] ?>" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3" title="Edit">
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (hasPermission('delete_inquiries')): ?>
                                <a href="#" onclick="confirmDelete(<?= $inquiry['id'] ?>, '<?= htmlspecialchars(addslashes($inquiry['name']), ENT_QUOTES) ?>')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

             <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="px-6 py-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between flex-wrap">
                    <div class="text-sm text-gray-700 dark:text-gray-400 mb-2 sm:mb-0">
                        Showing <span class="font-medium"><?= $totalItems > 0 ? $offset + 1 : 0 ?></span>
                        to <span class="font-medium"><?= min($offset + $limit, $totalItems) ?></span>
                        of <span class="font-medium"><?= $totalItems ?></span> inquiries
                    </div>
                    <?php if ($totalItems > 0): ?>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <?php
                                // Build base URL for pagination links, preserving existing filters
                                $queryParams = $_GET;
                                unset($queryParams['page']); // Remove existing page param
                                $baseUrl = 'index.php?' . http_build_query($queryParams);
                                $separator = empty($queryParams) ? '' : '&';
                            ?>
                            <!-- Previous Page Link -->
                             <a href="<?= $baseUrl . $separator ?>page=<?= max(1, $page - 1) ?>"
                               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white dark:bg-gray-800 dark:border-gray-600 text-sm font-medium <?= $page <= 1 ? 'text-gray-300 dark:text-gray-500 cursor-not-allowed' : 'text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' ?>">
                                <span class="sr-only">Previous</span>
                                <i class="fa-solid fa-chevron-left h-5 w-5"></i>
                            </a>

                            <?php
                            // Determine Pagination Links to show
                            $linksToShow = 5; // Number of page links to show (adjust as needed)
                            $startPage = max(1, $page - floor($linksToShow / 2));
                            $endPage = min($totalPages, $startPage + $linksToShow - 1);
                             // Adjust startPage if endPage reaches the total limit early
                            $startPage = max(1, $endPage - $linksToShow + 1);

                            if ($startPage > 1) {
                                echo '<a href="' . $baseUrl . $separator . 'page=1" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white dark:bg-gray-800 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">1</a>';
                                if ($startPage > 2) {
                                    echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white dark:bg-gray-800 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-400">...</span>';
                                }
                            }

                            for ($i = $startPage; $i <= $endPage; $i++) {
                                $isCurrent = ($i === $page);
                                echo '<a href="' . $baseUrl . $separator . 'page=' . $i . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium ' . ($isCurrent ? 'z-10 bg-blue-50 dark:bg-blue-900 border-blue-500 dark:border-blue-400 text-blue-600 dark:text-blue-300' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700') . '">' . $i . '</a>';
                            }

                            if ($endPage < $totalPages) {
                                if ($endPage < $totalPages - 1) {
                                     echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white dark:bg-gray-800 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-400">...</span>';
                                }
                                echo '<a href="' . $baseUrl . $separator . 'page=' . $totalPages . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white dark:bg-gray-800 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">' . $totalPages . '</a>';
                            }
                            ?>

                            <!-- Next Page Link -->
                             <a href="<?= $baseUrl . $separator ?>page=<?= min($totalPages, $page + 1) ?>"
                               class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white dark:bg-gray-800 dark:border-gray-600 text-sm font-medium <?= $page >= $totalPages ? 'text-gray-300 dark:text-gray-500 cursor-not-allowed' : 'text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' ?>">
                                <span class="sr-only">Next</span>
                                <i class="fa-solid fa-chevron-right h-5 w-5"></i>
                            </a>
                        </nav>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
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
// Helper function to get inquiry status badge class (ensure this uses correct Tailwind classes)
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
    switch ($status) {
        case 'new': return 'New';
        case 'in_progress': return 'In Progress';
        case 'responded': return 'Responded';
        case 'closed': return 'Closed';
        case 'spam': return 'Spam';
        default: return ucfirst(str_replace('_', ' ', $status)); // Make default more readable
    }
}
?>

<?php include_once '../../includes/footer.php'; ?>