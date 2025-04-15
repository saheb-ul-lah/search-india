<?php
$pageTitle = 'Reviews';
require_once '../../includes/header.php';

// Check if user has permission
if (!isManager()) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('modules/dashboard/index.php');
}

// Get filter parameters
$businessId = isset($_GET['business_id']) ? (int)$_GET['business_id'] : 0;
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$rating = isset($_GET['rating']) ? (int)$_GET['rating'] : 0;
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Build query
$query = "SELECT r.*, b.name as business_name, u.name as user_name 
          FROM reviews r 
          LEFT JOIN businesses b ON r.business_id = b.id 
          LEFT JOIN users u ON r.user_id = u.id 
          WHERE 1=1";

$countQuery = "SELECT COUNT(*) as total FROM reviews r WHERE 1=1";

$params = [];
$whereClause = [];

// Add filters
if ($businessId) {
    $whereClause[] = "r.business_id = :business_id";
    $params[':business_id'] = $businessId;
}

if ($userId) {
    $whereClause[] = "r.user_id = :user_id";
    $params[':user_id'] = $userId;
}

if ($rating) {
    $whereClause[] = "r.rating = :rating";
    $params[':rating'] = $rating;
}

if ($status) {
    $whereClause[] = "r.status = :status";
    $params[':status'] = $status;
}

if ($search) {
    $whereClause[] = "(r.title LIKE :search OR r.comment LIKE :search OR b.name LIKE :search OR u.name LIKE :search)";
    $params[':search'] = "%$search%";
}

// Add where clause to query
if (!empty($whereClause)) {
    $query .= " AND " . implode(" AND ", $whereClause);
    $countQuery .= " AND " . implode(" AND ", $whereClause);
}

// Get total count
$db = db();
$db->query($countQuery);
foreach ($params as $key => $value) {
    $db->bind($key, $value);
}
$result = $db->single();
$totalItems = $result['total'];

// Get pagination
$pagination = getPagination($totalItems, $page);

// Add order by and limit
$query .= " ORDER BY r.created_at DESC LIMIT :offset, :limit";
$params[':offset'] = $pagination['offset'];
$params[':limit'] = $pagination['per_page'];

// Get reviews
$db->query($query);
foreach ($params as $key => $value) {
    $db->bind($key, $value);
}
$reviews = $db->resultSet();

// Get businesses for filter dropdown
$db->query("SELECT id, name FROM businesses ORDER BY name");
$businesses = $db->resultSet();

// Get users for filter dropdown (if needed)
// $db->query("SELECT id, name FROM users ORDER BY name");
// $users = $db->resultSet();
?>

<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center my-6">
        <h2 class="text-2xl font-semibold text-gray-800">Reviews</h2>
        <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300">
            <i class="fa-solid fa-sync-alt mr-2"></i> Reset Filters
        </a>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="business_id" class="block text-sm font-medium text-gray-700 mb-1">Business</label>
                <select id="business_id" name="business_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">All Businesses</option>
                    <?php foreach ($businesses as $business): ?>
                    <option value="<?= $business['id'] ?>" <?= $businessId === $business['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($business['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="rating" class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                <select id="rating" name="rating" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">All Ratings</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>" <?= $rating === $i ? 'selected' : '' ?>>
                        <?= $i ?> Star<?= $i > 1 ? 's' : '' ?>
                    </option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">All Statuses</option>
                    <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>
            
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="search" name="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" placeholder="Search reviews..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            
            <div class="md:col-span-2 lg:col-span-4 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
                    <i class="fa-solid fa-filter mr-2"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>
    
    <!-- Reviews Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($reviews)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No reviews found</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= $review['id'] ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                <a href="<?= BASE_URL ?>/modules/businesses/view.php?id=<?= $review['business_id'] ?>" class="text-blue-600 hover:underline">
                                    <?= htmlspecialchars($review['business_name']) ?>
                                </a>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <a href="<?= BASE_URL ?>/modules/users/view.php?id=<?= $review['user_id'] ?>" class="text-blue-600 hover:underline">
                                    <?= htmlspecialchars($review['user_name']) ?>
                                </a>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fa-solid fa-star <?= $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300' ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <a href="<?= BASE_URL ?>/modules/reviews/view.php?id=<?= $review['id'] ?>" class="text-blue-600 hover:underline">
                                    <?= htmlspecialchars($review['title']) ?>
                                </a>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?= $review['status'] === 'approved' ? 'bg-green-100 text-green-800' : 
                                   ($review['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   'bg-red-100 text-red-800') ?>">
                                <?= ucfirst($review['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= formatDate($review['created_at']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="view.php?id=<?= $review['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3" title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="delete.php?id=<?= $review['id'] ?>" class="text-red-600 hover:text-red-900" title="Delete" onclick="return confirm('Are you sure you want to delete this review?')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    <?= paginationLinks($pagination, 'index.php?' . http_build_query(array_filter([
        'business_id' => $businessId,
        'user_id' => $userId,
        'rating' => $rating,
        'status' => $status,
        'search' => $search
    ]))) ?>
</div>

<?php require_once '../../includes/footer.php'; ?>