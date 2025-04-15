<?php
$pageTitle = 'Categories';
require_once '../../includes/header.php';

// Check if user has permission
if (!isManager()) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('modules/dashboard/index.php');
}

// Get filter parameters
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Build query
$query = "SELECT c.*, p.name as parent_name, 
          (SELECT COUNT(*) FROM business_categories bc WHERE bc.category_id = c.id) as business_count 
          FROM categories c 
          LEFT JOIN categories p ON c.parent_id = p.id";

$countQuery = "SELECT COUNT(*) as total FROM categories c";

$params = [];
$whereClause = [];

// Add filters
if ($status) {
    $whereClause[] = "c.status = :status";
    $params[':status'] = $status;
}

if ($search) {
    $whereClause[] = "(c.name LIKE :search OR c.description LIKE :search)";
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
$query .= " ORDER BY c.sort_order, c.name LIMIT :offset, :limit";
$params[':offset'] = $pagination['offset'];
$params[':limit'] = $pagination['per_page'];

// Get categories
$db->query($query);
foreach ($params as $key => $value) {
    $db->bind($key, $value);
}
$categories = $db->resultSet();
?>

<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center my-6">
        <h2 class="text-2xl font-semibold text-gray-800">Categories</h2>
        <a href="add.php" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-[1.02]">
            <i class="fa-solid fa-plus mr-2"></i> Add Category
        </a>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    <input type="text" id="search" name="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" placeholder="Search categories..." value="<?= $search ?>">
                </div>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg mr-2">
                    <i class="fa-solid fa-filter mr-2"></i> Filter
                </button>
                <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg">
                    <i class="fa-solid fa-times mr-2"></i> Reset
                </a>
            </div>
        </form>
    </div>
    
    <!-- Categories Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Businesses</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Featured</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sort Order</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No categories found</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($categories as $category): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <?php if ($category['image']): ?>
                                    <img class="h-10 w-10 rounded-full object-cover" src="<?= UPLOADS_URL ?>/categories/<?= $category['image'] ?>" alt="<?= $category['name'] ?>">
                                    <?php else: ?>
                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <i class="fa-solid fa-<?= $category['icon'] ?: 'tag' ?> text-gray-500"></i>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900"><?= $category['name'] ?></div>
                                    <div class="text-sm text-gray-500"><?= $category['slug'] ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= $category['parent_name'] ?: 'None' ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= $category['business_count'] ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $category['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                <?= ucfirst($category['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $category['featured'] ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' ?>">
                                <?= $category['featured'] ? 'Yes' : 'No' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= $category['sort_order'] ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="edit.php?id=<?= $category['id'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            <a href="delete.php?id=<?= $category['id'] ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this category?')">
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
        'search' => $search
    ]))) ?>
</div>

<?php require_once '../../includes/footer.php'; ?>