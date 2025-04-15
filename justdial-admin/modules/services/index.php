<?php
$pageTitle = 'Services';
require_once '../../includes/header.php';

// Check if user has permission
if (!isManager()) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('modules/dashboard/index.php');
}

// Get filter parameters
$categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$businessId = isset($_GET['business_id']) ? (int)$_GET['business_id'] : 0;
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Build query
$query = "SELECT s.*, c.name as category_name, b.name as business_name 
          FROM services s 
          LEFT JOIN categories c ON s.category_id = c.id
          LEFT JOIN businesses b ON s.business_id = b.id";

$countQuery = "SELECT COUNT(*) as total FROM services s";

$params = [];
$whereClause = [];

// Add filters
if ($categoryId) {
    $whereClause[] = "s.category_id = :category_id";
    $params[':category_id'] = $categoryId;
}

if ($businessId) {
    $whereClause[] = "s.business_id = :business_id";
    $params[':business_id'] = $businessId;
}

if ($status) {
    $whereClause[] = "s.status = :status";
    $params[':status'] = $status;
}

if ($search) {
    $whereClause[] = "(s.name LIKE :search OR s.description LIKE :search OR c.name LIKE :search OR b.name LIKE :search)";
    $params[':search'] = "%$search%";
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
$query .= " ORDER BY s.name LIMIT :offset, :limit";
$params[':offset'] = $pagination['offset'];
$params[':limit'] = $pagination['per_page'];

// Get services
$db->query($query);
foreach ($params as $key => $value) {
    $db->bind($key, $value);
}
$services = $db->resultSet();

// Get categories and businesses for filters
$db->query("SELECT id, name FROM categories ORDER BY name");
$categories = $db->resultSet();

$db->query("SELECT id, name FROM businesses ORDER BY name");
$businesses = $db->resultSet();
?>

<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center my-6">
        <h2 class="text-2xl font-semibold text-gray-800">Services</h2>
        <a href="add.php" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-[1.02]">
            <i class="fa-solid fa-plus mr-2"></i> Add Service
        </a>
    </div>

    <!-- Flash Messages -->
    <?php include_once '../../config/functions.php'; ?>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select id="category_id" name="category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $categoryId == $category['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="business_id" class="block text-sm font-medium text-gray-700 mb-1">Business</label>
                <select id="business_id" name="business_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">All Businesses</option>
                    <?php foreach ($businesses as $business): ?>
                        <option value="<?= $business['id'] ?>" <?= $businessId == $business['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($business['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">All Statuses</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="search" name="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" placeholder="Search services..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            
            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg mr-2">
                    <i class="fa-solid fa-filter mr-2"></i> Apply Filters
                </button>
                <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg">
                    <i class="fa-solid fa-times mr-2"></i> Reset
                </a>
            </div>
        </form>
    </div>
    
    <!-- Services Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($services)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No services found</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($services as $service): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= $service['id'] ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <?php if ($service['image']): ?>
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full object-cover" src="<?= UPLOADS_URL ?>/services/<?= $service['image'] ?>" alt="<?= $service['name'] ?>">
                                </div>
                                <?php endif; ?>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($service['name']) ?></div>
                                    <div class="text-sm text-gray-500">₹<?= number_format($service['price'], 2) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= htmlspecialchars($service['category_name'] ?? 'N/A') ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= htmlspecialchars($service['business_name'] ?? 'N/A') ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $service['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                <?= ucfirst($service['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="edit.php?id=<?= $service['id'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            <a href="delete.php?id=<?= $service['id'] ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this service?')">
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
        'category_id' => $categoryId,
        'business_id' => $businessId,
        'status' => $status,
        'search' => $search
    ]))) ?>
</div>

<?php require_once '../../includes/footer.php'; ?>