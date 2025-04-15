<?php
$pageTitle = 'Manage Cities';
require_once '../../config/config.php';
require_once '../../config/functions.php';
require_once '../../includes/auth.php';

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Permission Checks ---
$canView = checkPermission('view_cities', false); // Check if user can view
$canAdd = checkPermission('add_cities', false);
$canEdit = checkPermission('edit_cities', false);
$canDelete = checkPermission('delete_cities', false);

// Redirect if user cannot view the page at all
if (!$canView) {
    setFlashMessage('error', 'You do not have permission to access this page.');
    redirect('modules/dashboard/index.php');
    exit;
}

// --- Database Setup ---
$db = db();
$errors = []; // For form processing errors

// --- FORM SUBMISSION HANDLING (Add, Edit, Delete) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check CSRF token for all POST actions
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid CSRF token. Please try again.';
        // Set flash message and redirect to prevent resubmission
        setFlashMessage('error', 'Invalid security token. Please try again.');
        redirect('modules/cities/index.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    // --- Add City ---
    if ($action === 'add_city' && $canAdd) {
        $formData = [
            'name' => sanitize($_POST['name'] ?? ''),
            'state' => sanitize($_POST['state'] ?? ''),
            'country' => sanitize($_POST['country'] ?? 'India'),
            'status' => sanitize($_POST['status'] ?? 'active'),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'image' => null // Handle image upload below
        ];

        // Validation
        if (empty($formData['name'])) { $errors[] = 'City name is required.'; }
        if (empty($formData['state'])) { $errors[] = 'State/Province is required.'; }
        if (!in_array($formData['status'], ['active', 'inactive'])) { $errors[] = 'Invalid status.'; }

        // Handle image upload
        $imageFilename = null;
        if (!empty($_FILES['image']['name'])) {
             if ($formData['name']) { // Only upload if name is present (basic check)
                $uploadResult = uploadFile($_FILES['image'], 'cities', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                if ($uploadResult['success']) {
                    $imageFilename = $uploadResult['filename'];
                } else {
                    $errors[] = 'Image upload failed: ' . $uploadResult['message'];
                }
            } else {
                 $errors[] = 'Cannot upload image without a city name.';
            }
        }

        if (empty($errors)) {
            try {
                $db->query("INSERT INTO cities (name, state, country, status, is_featured, image, created_at)
                            VALUES (:name, :state, :country, :status, :is_featured, :image, NOW())");
                $db->bind(':name', $formData['name']);
                $db->bind(':state', $formData['state']);
                $db->bind(':country', $formData['country']);
                $db->bind(':status', $formData['status']);
                $db->bind(':is_featured', $formData['is_featured'], PDO::PARAM_INT);
                $db->bind(':image', $imageFilename); // Can be null

                if ($db->execute()) {
                    $newCityId = $db->lastInsertId();
                    logActivity($_SESSION['user_id'], 'create', 'cities', $newCityId, 'Created city: ' . $formData['name']);
                    setFlashMessage('success', 'City added successfully!');
                } else {
                    $errors[] = 'Database error: Could not add city.';
                     // Attempt to delete uploaded image if DB insert failed
                     if ($imageFilename) { deleteFile('cities/' . $imageFilename); }
                }
            } catch (Exception $e) {
                 error_log("Error adding city: " . $e->getMessage());
                 $errors[] = 'An unexpected error occurred.';
                 if ($imageFilename) { deleteFile('cities/' . $imageFilename); }
            }
        }

        if (!empty($errors)) {
            setFlashMessage('error', implode('<br>', $errors));
            // Store form data in session to repopulate if needed (optional, makes page more complex)
             $_SESSION['add_city_form_data'] = $formData;
             $_SESSION['add_city_form_errors'] = $errors; // Store errors too
        }
        // Redirect after POST to prevent resubmission
        redirect('modules/cities/index.php' . (!empty($errors) ? '#addCityModal' : '')); // Optionally open modal on error
        exit;

    } // --- End Add City ---

    // --- Edit City ---
    elseif ($action === 'edit_city' && $canEdit) {
        $cityId = filter_input(INPUT_POST, 'city_id', FILTER_VALIDATE_INT);
        $formData = [
            'name' => sanitize($_POST['name'] ?? ''),
            'state' => sanitize($_POST['state'] ?? ''),
            'country' => sanitize($_POST['country'] ?? 'India'),
            'status' => sanitize($_POST['status'] ?? 'active'),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        ];

        // Validation
        if (!$cityId) { $errors[] = 'Invalid City ID.'; }
        if (empty($formData['name'])) { $errors[] = 'City name is required.'; }
        if (empty($formData['state'])) { $errors[] = 'State/Province is required.'; }
        if (!in_array($formData['status'], ['active', 'inactive'])) { $errors[] = 'Invalid status.'; }

        // Fetch existing city data (needed for image handling)
        $existingCity = null;
        if ($cityId && empty($errors)) {
            $db->query("SELECT image FROM cities WHERE id = :id");
            $db->bind(':id', $cityId);
            $existingCity = $db->single();
            if (!$existingCity) {
                $errors[] = 'City not found.';
            }
        }

         // Handle image upload/replacement
        $imageFilename = $existingCity['image'] ?? null; // Keep old image by default
        $deleteOldImage = false;
        if (!empty($_FILES['image']['name']) && empty($errors)) {
            $uploadResult = uploadFile($_FILES['image'], 'cities', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            if ($uploadResult['success']) {
                $newImageFilename = $uploadResult['filename'];
                // If upload successful and there was an old image, mark old one for deletion
                if ($imageFilename) {
                    $deleteOldImage = true;
                }
                $imageFilename = $newImageFilename; // Set to new filename for DB update
            } else {
                $errors[] = 'Image upload failed: ' . $uploadResult['message'];
            }
        }

        if (empty($errors)) {
             try {
                $db->query("UPDATE cities SET
                                name = :name,
                                state = :state,
                                country = :country,
                                status = :status,
                                is_featured = :is_featured,
                                image = :image
                            WHERE id = :id");
                $db->bind(':name', $formData['name']);
                $db->bind(':state', $formData['state']);
                $db->bind(':country', $formData['country']);
                $db->bind(':status', $formData['status']);
                $db->bind(':is_featured', $formData['is_featured'], PDO::PARAM_INT);
                $db->bind(':image', $imageFilename);
                $db->bind(':id', $cityId, PDO::PARAM_INT);

                if ($db->execute()) {
                    // Delete old image only if DB update was successful and a new image was uploaded
                    if ($deleteOldImage && $existingCity['image']) {
                         deleteFile('cities/' . $existingCity['image']);
                    }
                    logActivity($_SESSION['user_id'], 'update', 'cities', $cityId, 'Updated city: ' . $formData['name']);
                    setFlashMessage('success', 'City updated successfully!');
                } else {
                    $errors[] = 'Database error: Could not update city.';
                    // If update failed, don't delete old image, and delete newly uploaded image if any
                    if ($imageFilename !== ($existingCity['image'] ?? null)) {
                         deleteFile('cities/' . $imageFilename);
                    }
                }
            } catch (Exception $e) {
                error_log("Error updating city: " . $e->getMessage());
                $errors[] = 'An unexpected error occurred.';
                 if ($imageFilename !== ($existingCity['image'] ?? null)) {
                     deleteFile('cities/' . $imageFilename);
                 }
            }
        }

        if (!empty($errors)) {
            setFlashMessage('error', implode('<br>', $errors));
             // Store form data in session to repopulate edit modal if needed (more complex)
             $_SESSION['edit_city_form_data_' . $cityId] = $formData;
             $_SESSION['edit_city_form_errors_' . $cityId] = $errors;
        }
         // Redirect after POST
        redirect('modules/cities/index.php' . (!empty($errors) ? '#editCityModal-' . $cityId : '')); // Try to reopen specific modal on error
        exit;

    } // --- End Edit City ---

    // --- Delete City ---
    elseif ($action === 'delete_city' && $canDelete) {
         $cityId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT); // 'id' from delete form

        if (!$cityId) {
             setFlashMessage('error', 'Invalid City ID for deletion.');
        } else {
            try {
                // Fetch city data (image) before deleting
                $db->query("SELECT name, image FROM cities WHERE id = :id");
                $db->bind(':id', $cityId);
                $cityToDelete = $db->single();

                if (!$cityToDelete) {
                     setFlashMessage('error', 'City not found or already deleted.');
                } else {
                    $cityName = $cityToDelete['name'];
                    $imageToDelete = $cityToDelete['image'];

                    // Delete from DB
                    $db->query("DELETE FROM cities WHERE id = :id");
                    $db->bind(':id', $cityId);

                    if ($db->execute()) {
                        // Delete image file if it exists
                        if ($imageToDelete) {
                            deleteFile('cities/' . $imageToDelete);
                        }
                        logActivity($_SESSION['user_id'], 'delete', 'cities', $cityId, 'Deleted city: ' . $cityName);
                        setFlashMessage('success', 'City deleted successfully!');
                    } else {
                        setFlashMessage('error', 'Database error: Could not delete city.');
                    }
                }
            } catch (Exception $e) {
                error_log("Error deleting city: " . $e->getMessage());
                // Check for foreign key constraint violation (e.g., businesses linked to this city)
                if (strpos($e->getMessage(), 'constraint violation') !== false) {
                     setFlashMessage('error', 'Cannot delete city. It might be linked to existing businesses or other records.');
                } else {
                    setFlashMessage('error', 'An unexpected error occurred while deleting the city.');
                }
            }
        }
         // Redirect after POST
        redirect('modules/cities/index.php');
        exit;

    } // --- End Delete City ---

} // --- END OF POST HANDLING ---


// --- DATA FETCHING FOR DISPLAY ---

// Get filter parameters (needed again for display and pagination links)
$filterState = isset($_GET['state']) ? sanitize($_GET['state']) : '';
$filterCountry = isset($_GET['country']) ? sanitize($_GET['country']) : '';
$filterStatus = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$filterSearch = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = getSetting('items_per_page', 10); // Use setting or default
$offset = ($page - 1) * $limit;

// Build query parts
$baseQuery = "FROM cities WHERE 1=1";
$whereClauses = [];
$queryParams = [];

// Add filters
if ($filterState) {
    $whereClauses[] = "state = :state";
    $queryParams[':state'] = $filterState;
}
if ($filterCountry) {
    $whereClauses[] = "country = :country";
    $queryParams[':country'] = $filterCountry;
}
if ($filterStatus) {
    $whereClauses[] = "status = :status";
    $queryParams[':status'] = $filterStatus;
}
if ($filterSearch) {
    $whereClauses[] = "(name LIKE :search OR state LIKE :search OR country LIKE :search)";
    $queryParams[':search'] = "%$filterSearch%";
}

$whereSql = !empty($whereClauses) ? " AND " . implode(" AND ", $whereClauses) : "";

// Get total count
$countQuery = "SELECT COUNT(*) as total " . $baseQuery . $whereSql;
$db->query($countQuery);
foreach ($queryParams as $key => $value) { $db->bind($key, $value); }
$totalItems = $db->single()['total'] ?? 0;
$totalPages = ceil($totalItems / $limit);
$page = max(1, min($page, $totalPages == 0 ? 1 : $totalPages)); // Ensure page is valid
$offset = ($page - 1) * $limit; // Recalculate offset

// Get cities for current page
$citiesQuery = "SELECT * " . $baseQuery . $whereSql . " ORDER BY name ASC LIMIT :offset, :limit";
$db->query($citiesQuery);
foreach ($queryParams as $key => $value) { $db->bind($key, $value); }
$db->bind(':offset', $offset, PDO::PARAM_INT);
$db->bind(':limit', $limit, PDO::PARAM_INT);
$cities = $db->resultSet();

// Get unique states and countries for filters (using db wrapper)
$db->query("SELECT DISTINCT state FROM cities WHERE state != '' AND state IS NOT NULL ORDER BY state ASC");
$stateRows = $db->resultSet(); // Fetch the full result set
// Use array_column to extract the 'state' values into a flat array
$states = $stateRows ? array_column($stateRows, 'state') : []; // Handle empty results

$db->query("SELECT DISTINCT country FROM cities WHERE country != '' AND country IS NOT NULL ORDER BY country ASC");
$countryRows = $db->resultSet(); // Fetch the full result set
// Use array_column to extract the 'country' values into a flat array
$countries = $countryRows ? array_column($countryRows, 'country') : []; // Handle empty results
// Include header and sidebar BEFORE any HTML output
include_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
?>

<div class="container px-6 mx-auto">
    <div class="p-4 mt-14">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Manage Cities</h2>
            <?php if ($canAdd): ?>
                <button type="button" onclick="openAddModal()" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-medium rounded-lg shadow-md transition-all duration-300 transform hover:scale-[1.02]">
                    <i class="fa-solid fa-plus mr-2"></i> Add New City
                </button>
            <?php endif; ?>
        </div>

        <!-- Flash Messages -->
        <?php $flash = getFlashMessage(); if ($flash): ?>
            <div id="flash-message" class="mb-6 p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' ?>" role="alert">
                <?= htmlspecialchars($flash['message']) ?>
                 <button type="button" class="float-right font-bold text-lg leading-none -mt-1" onclick="document.getElementById('flash-message').style.display='none'">×</button>
            </div>
        <?php endif; ?>


        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <form method="GET" action="index.php" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                 <div>
                    <label for="filter_search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fa-solid fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="filter_search" name="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="City, state, country..." value="<?= htmlspecialchars($filterSearch) ?>">
                    </div>
                </div>
                <div>
                    <label for="filter_state" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">State/Province</label>
                    <select id="filter_state" name="state" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">All States</option>
                        <?php foreach ($states as $stateOption): ?>
                            <option value="<?= htmlspecialchars($stateOption) ?>" <?= $filterState === $stateOption ? 'selected' : '' ?>>
                                <?= htmlspecialchars($stateOption) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="filter_country" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Country</label>
                    <select id="filter_country" name="country" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">All Countries</option>
                        <?php foreach ($countries as $countryOption): ?>
                            <option value="<?= htmlspecialchars($countryOption) ?>" <?= $filterCountry === $countryOption ? 'selected' : '' ?>>
                                <?= htmlspecialchars($countryOption) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="filter_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select id="filter_status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="active" <?= $filterStatus === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $filterStatus === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="flex items-end md:col-span-2 lg:col-span-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg mr-2">
                        <i class="fa-solid fa-filter mr-2"></i> Filter
                    </button>
                    <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg">
                        <i class="fa-solid fa-times mr-2"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Cities Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Image</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">City</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">State/Province</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Country</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Featured</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php if (empty($cities)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No cities found matching your criteria.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($cities as $city): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($city['image']): ?>
                                    <img src="<?= UPLOADS_URL ?>/cities/<?= htmlspecialchars($city['image']) ?>" alt="<?= htmlspecialchars($city['name']) ?>" class="h-10 w-16 object-cover rounded">
                                <?php else: ?>
                                    <div class="h-10 w-16 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center text-gray-400 text-xs">No Image</div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($city['name']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-300"><?= htmlspecialchars($city['state']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-300"><?= htmlspecialchars($city['country']) ?></div>
                            </td>
                             <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                <?= $city['is_featured'] ? '<i class="fa-solid fa-star text-yellow-500"></i>' : '<i class="fa-regular fa-star text-gray-400"></i>' ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $city['status'] === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-300' ?>">
                                    <?= ucfirst(htmlspecialchars($city['status'])) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <?php if ($canEdit): ?>
                                <button type="button" onclick='openEditModal(<?= json_encode($city, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) ?>)' class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3" title="Edit">
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                                <?php endif; ?>
                                <?php if ($canDelete): ?>
                                <button type="button" onclick="confirmDelete(<?= $city['id'] ?>, '<?= htmlspecialchars(addslashes($city['name']), ENT_QUOTES) ?>')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                <?php endif; ?>
                                <?php if (!$canEdit && !$canDelete): ?>
                                    <span class="text-xs text-gray-400 italic">No actions</span>
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
                 <?php
                    // Build base URL for pagination links, preserving existing filters
                    $queryParams = $_GET; // Get current filters
                    unset($queryParams['page']); // Remove page param for base URL
                    $baseUrl = 'index.php?' . http_build_query($queryParams);
                    $separator = empty($queryParams) ? '' : '&';
                 ?>
                 <?= renderPagination($page, $totalPages, $baseUrl, $separator); // Use a helper function if available, or inline the logic ?>
             </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<!-- Add City Modal -->
<div id="addCityModal" tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden overflow-y-auto overflow-x-hidden flex items-center justify-center bg-gray-900 bg-opacity-50 dark:bg-opacity-80">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <form method="POST" action="index.php" enctype="multipart/form-data" id="addCityForm">
                 <input type="hidden" name="action" value="add_city">
                 <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Add New City</h3>
                    <button type="button" onclick="closeModal('addCityModal')" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <i class="fa-solid fa-times"></i>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4">
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="add_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">City Name <span class="text-red-600">*</span></label>
                            <input type="text" name="name" id="add_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="e.g., Mumbai" required>
                        </div>
                        <div>
                            <label for="add_state" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">State/Province <span class="text-red-600">*</span></label>
                            <input type="text" name="state" id="add_state" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="e.g., Maharashtra" required>
                        </div>
                        <div>
                            <label for="add_country" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Country</label>
                            <input type="text" name="country" id="add_country" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="e.g., India" value="India">
                        </div>
                        <div>
                            <label for="add_status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                            <select id="add_status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                         <div class="md:col-span-2">
                            <label for="add_image" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">City Image</label>
                            <input type="file" name="image" id="add_image" accept="image/*" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400">
                             <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF, WEBP (Max size recommended: 1MB)</p>
                         </div>
                         <div class="md:col-span-2 flex items-center">
                            <input id="add_is_featured" name="is_featured" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="add_is_featured" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Mark as Featured</label>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Add city</button>
                    <button type="button" onclick="closeModal('addCityModal')" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit City Modal -->
<div id="editCityModal" tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden overflow-y-auto overflow-x-hidden flex items-center justify-center bg-gray-900 bg-opacity-50 dark:bg-opacity-80">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
             <form method="POST" action="index.php" enctype="multipart/form-data" id="editCityForm">
                 <input type="hidden" name="action" value="edit_city">
                 <input type="hidden" name="city_id" id="edit_city_id">
                 <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit City</h3>
                    <button type="button" onclick="closeModal('editCityModal')" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <i class="fa-solid fa-times"></i>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                 <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4">
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">City Name <span class="text-red-600">*</span></label>
                            <input type="text" name="name" id="edit_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                        </div>
                        <div>
                            <label for="edit_state" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">State/Province <span class="text-red-600">*</span></label>
                            <input type="text" name="state" id="edit_state" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                        </div>
                        <div>
                            <label for="edit_country" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Country</label>
                            <input type="text" name="country" id="edit_country" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        </div>
                        <div>
                            <label for="edit_status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                            <select id="edit_status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Current Image</label>
                            <img id="edit_current_image_display" src="" alt="Current City Image" class="h-16 w-auto object-cover rounded mb-2 border dark:border-gray-500 hidden">
                             <div id="edit_no_image_text" class="text-sm text-gray-500 dark:text-gray-400 hidden">No current image.</div>

                            <label for="edit_image" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Upload New Image (Optional)</label>
                            <input type="file" name="image" id="edit_image" accept="image/*" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400">
                             <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank to keep the current image. Uploading a new file will replace it.</p>
                         </div>
                         <div class="md:col-span-2 flex items-center">
                            <input id="edit_is_featured" name="is_featured" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="edit_is_featured" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Mark as Featured</label>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update city</button>
                    <button type="button" onclick="closeModal('editCityModal')" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Delete Confirmation Modal -->
<div id="deleteModal" tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden overflow-y-auto overflow-x-hidden flex items-center justify-center bg-gray-900 bg-opacity-50 dark:bg-opacity-80">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
             <!-- Use index.php as action, differentiate with hidden input -->
            <form id="deleteForm" action="index.php" method="POST">
                 <input type="hidden" name="action" value="delete_city">
                 <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                 <input type="hidden" id="deleteId" name="id" value=""> <!-- Name is 'id' -->
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirm Deletion</h3>
                    <button type="button" onclick="closeModal('deleteModal')" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="deleteModal">
                         <i class="fa-solid fa-times"></i><span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    <p class="text-gray-600 dark:text-gray-400 mb-5">Are you sure you want to delete the city <strong id="deleteCityName" class="font-medium text-gray-800 dark:text-gray-200"></strong>? This might fail if businesses are linked to it. This action cannot be undone.</p>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('deleteModal')" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-900">Yes, Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Include Footer -->
<?php include_once '../../includes/footer.php'; ?>

<!-- JavaScript for Modals -->
<script>
    // Function to open a modal
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex'); // Use flex to center it
            modal.setAttribute('aria-hidden', 'false');
            // Optional: Focus the first input field
             const firstInput = modal.querySelector('input:not([type=hidden]), select, textarea');
             if (firstInput) {
                 firstInput.focus();
             }
        }
    }

    // Function to close a modal
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');
            // Reset forms within the modal when closing (optional but good practice)
            const form = modal.querySelector('form');
            if (form) {
                form.reset();
                 // Reset image previews if needed
                 if (modalId === 'editCityModal') {
                     document.getElementById('edit_current_image_display').src = '#';
                     document.getElementById('edit_current_image_display').classList.add('hidden');
                     document.getElementById('edit_no_image_text').classList.add('hidden');
                 }
            }
        }
    }

    // Specific function to open Add modal
    function openAddModal() {
        openModal('addCityModal');
    }

    // Specific function to open Edit modal and populate data
    function openEditModal(cityData) {
        if (!cityData) return;
        // Populate form fields
        document.getElementById('edit_city_id').value = cityData.id || '';
        document.getElementById('edit_name').value = cityData.name || '';
        document.getElementById('edit_state').value = cityData.state || '';
        document.getElementById('edit_country').value = cityData.country || 'India';
        document.getElementById('edit_status').value = cityData.status || 'active';
        document.getElementById('edit_is_featured').checked = cityData.is_featured == 1;

         // Handle image display
        const imgDisplay = document.getElementById('edit_current_image_display');
        const noImgText = document.getElementById('edit_no_image_text');
        if (cityData.image) {
            imgDisplay.src = `<?= UPLOADS_URL ?>/cities/${cityData.image}`;
            imgDisplay.classList.remove('hidden');
            noImgText.classList.add('hidden');
        } else {
             imgDisplay.classList.add('hidden');
             noImgText.classList.remove('hidden');
        }

        // Clear the file input (important)
        document.getElementById('edit_image').value = '';

        openModal('editCityModal');
    }

     // Function to handle delete confirmation
    function confirmDelete(id, name) {
        document.getElementById('deleteId').value = id;
        // Decode HTML entities for display
        const tempElem = document.createElement('textarea');
        tempElem.innerHTML = name;
        document.getElementById('deleteCityName').textContent = tempElem.value;
        openModal('deleteModal');
    }

    // Close modal on Escape key press
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modals = document.querySelectorAll('.fixed.z-50:not(.hidden)');
            modals.forEach(modal => closeModal(modal.id));
        }
    });

     // Close modal if clicking outside the content area
     document.addEventListener('click', function(event) {
         const modals = document.querySelectorAll('.fixed.z-50:not(.hidden)');
         modals.forEach(modal => {
             const modalContent = modal.querySelector('.relative.bg-white');
             if (modalContent && !modalContent.contains(event.target)) {
                 // Check if the click was directly on the backdrop (the modal element itself)
                 if (modal === event.target) {
                    closeModal(modal.id);
                 }
             }
         });
     });

     // --- Optional: Re-open modal on validation error ---
     // This requires PHP to add a specific class or data attribute to the body
     // or use the hash in the URL as done in the redirect.
     document.addEventListener('DOMContentLoaded', function() {
         if (window.location.hash) {
             const modalId = window.location.hash.substring(1); // Remove #
             const modalElement = document.getElementById(modalId);
             // You might need more specific checks here, e.g., #addCityModal or #editCityModal-<id>
             if (modalElement && (modalId === 'addCityModal' || modalId.startsWith('editCityModal'))) {
                 // Potentially repopulate form data from session if PHP stored it
                 openModal(modalId);
                 // Clear the hash to prevent reopening on refresh without error
                 // history.replaceState(null, null, window.location.pathname + window.location.search);
             }
         }
     });

</script>

<?php
// --- Helper function for Pagination ---
// (Move this to functions.php if you haven't already)

?>
</body>
</html>