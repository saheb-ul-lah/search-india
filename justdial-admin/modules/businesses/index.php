<?php
$pageTitle = 'Businesses';
require_once '../../includes/header.php';

// Check if user has permission
if (!isManager()) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('modules/dashboard/index.php');
}

// Get filter parameters
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$city = isset($_GET['city']) ? sanitize($_GET['city']) : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Build query
$query = "SELECT b.*, u.name as owner_name, 
          (SELECT COUNT(*) FROM reviews r WHERE r.business_id = b.id AND r.status = 'approved') as review_count,
          (SELECT AVG(rating) FROM reviews r WHERE r.business_id = b.id AND r.status = 'approved') as avg_rating
          FROM businesses b
          LEFT JOIN users u ON b.owner_id = u.id";

$countQuery = "SELECT COUNT(*) as total FROM businesses b";

$params = [];
$whereClause = [];

// Add filters
if ($status) {
    $whereClause[] = "b.status = :status";
    $params[':status'] = $status;
}

if ($search) {
    $whereClause[] = "(b.name LIKE :search OR b.description LIKE :search OR b.city LIKE :search OR b.state LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($city) {
    $whereClause[] = "b.city = :city";
    $params[':city'] = $city;
}

if ($category) {
    $query .= " INNER JOIN business_categories bc ON b.id = bc.business_id";
    $countQuery .= " INNER JOIN business_categories bc ON b.id = bc.business_id";
    $whereClause[] = "bc.category_id = :category_id";
    $params[':category_id'] = $category;
}

// Add where clause to query
if (!empty($whereClause)) {
    $query .= " WHERE " . implode(" AND ", $whereClause);
    $countQuery .= " WHERE " . implode(" AND ", $whereClause);
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
$query .= " ORDER BY b.created_at DESC LIMIT :offset, :limit";
$params[':offset'] = $pagination['offset'];
$params[':limit'] = $pagination['per_page'];

// Get businesses
$db->query($query);
foreach ($params as $key => $value) {
    $db->bind($key, $value);
}
$businesses = $db->resultSet();

// Get categories for filter dropdown
$db->query("SELECT id, name FROM categories ORDER BY name");
$categories = $db->resultSet();

// Get cities for filter dropdown
$db->query("SELECT DISTINCT city FROM businesses WHERE city IS NOT NULL ORDER BY city");
$cities = $db->resultSet();
?>

<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center my-6">
        <h2 class="text-2xl font-semibold text-gray-800">Businesses</h2>
        <a href="add.php" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-[1.02]">
            <i class="fa-solid fa-plus mr-2"></i> Add Business
        </a>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">All Statuses</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>
            
            <div>
                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                <select id="city" name="city" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">All Cities</option>
                    <?php foreach ($cities as $cityItem): ?>
                    <option value="<?= $cityItem['city'] ?>" <?= $city === $cityItem['city'] ? 'selected' : '' ?>><?= $cityItem['city'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select id="category" name="category" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="0">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $category === $cat['id'] ? 'selected' : '' ?>><?= $cat['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="search" name="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" placeholder="Search businesses..." value="<?= $search ?>">
                </div>
            </div>
            
            <div class="md:col-span-4 flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg mr-2">
                    <i class="fa-solid fa-filter mr-2"></i> Filter
                </button>
                <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg">
                    <i class="fa-solid fa-times mr-2"></i> Reset
                </a>
            </div>
        </form>
    </div>
    
    <!-- Businesses Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Featured</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($businesses)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No businesses found</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($businesses as $business): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <?php if ($business['logo']): ?>
                                    <img class="h-10 w-10 rounded-full object-cover" src="<?= UPLOADS_URL ?>/businesses/<?= $business['logo'] ?>" alt="<?= $business['name'] ?>">
                                    <?php else: ?>
                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <i class="fa-solid fa-building text-gray-500"></i>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900"><?= $business['name'] ?></div>
                                    <div class="text-sm text-gray-500"><?= $business['email'] ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= $business['city'] ?>, <?= $business['state'] ?></div>
                            <div class="text-sm text-gray-500"><?= $business['phone'] ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= $business['owner_name'] ?: 'None' ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <?php if ($business['review_count'] > 0): ?>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= round($business['avg_rating'])): ?>
                                            <i class="fas fa-star text-yellow-400"></i>
                                        <?php else: ?>
                                            <i class="far fa-star text-yellow-400"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    <span class="ml-1 text-sm text-gray-500">(<?= $business['review_count'] ?>)</span>
                                <?php else: ?>
                                    <span class="text-sm text-gray-500">No reviews</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?= $business['status'] === 'active' ? 'bg-green-100 text-green-800' : 
                                   ($business['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($business['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) ?>">
                                <?= ucfirst($business['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $business['is_featured'] ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' ?>">
                                <?= $business['is_featured'] ? 'Yes' : 'No' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="view.php?id=<?= $business['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3" title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="edit.php?id=<?= $business['id'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Edit">
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            <a href="delete.php?id=<?= $business['id'] ?>" class="text-red-600 hover:text-red-900" title="Delete" onclick="return confirm('Are you sure you want to delete this business?')">
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
        'status' => $status,
        'search' => $search,
        'city' => $city,
        'category' => $category
    ]))) ?>
</div>

<?php require_once '../../includes/footer.php'; ?>