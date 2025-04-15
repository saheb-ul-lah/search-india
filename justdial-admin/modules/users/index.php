<?php
$pageTitle = 'Users';
require_once '../../includes/header.php';

// Check if user has permission
if (!isManager()) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('modules/dashboard/index.php');
}

// Get filter parameters
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$role = isset($_GET['role']) ? sanitize($_GET['role']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Build query
$query = "SELECT * FROM users WHERE 1=1";
$countQuery = "SELECT COUNT(*) as total FROM users WHERE 1=1";
$params = [];
$whereClause = [];

// Add filters
if ($status) {
    $whereClause[] = "status = :status";
    $params[':status'] = $status;
}

if ($role) {
    $whereClause[] = "role = :role";
    $params[':role'] = $role;
}

if ($search) {
    $whereClause[] = "(name LIKE :search OR email LIKE :search OR phone LIKE :search OR username LIKE :search)";
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
$query .= " ORDER BY created_at DESC LIMIT :offset, :limit";
$params[':offset'] = $pagination['offset'];
$params[':limit'] = $pagination['per_page'];

// Get users
$db->query($query);
foreach ($params as $key => $value) {
    $db->bind($key, $value);
}
$users = $db->resultSet();

// Helper functions for display
function getInitials($name)
{
    $words = explode(' ', $name);
    $initials = '';
    foreach ($words as $word) {
        $initials .= strtoupper(substr($word, 0, 1));
    }
    return substr($initials, 0, 2);
}

function getRoleBadgeClass($role)
{
    switch ($role) {
        case 'admin':
            return 'bg-purple-100 text-purple-800';
        case 'manager':
            return 'bg-blue-100 text-blue-800';
        case 'business_owner':
            return 'bg-green-100 text-green-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

function getStatusBadgeClass($status)
{
    switch ($status) {
        case 'active':
            return 'bg-green-100 text-green-800';
        case 'inactive':
            return 'bg-yellow-100 text-yellow-800';
        case 'banned':
            return 'bg-red-100 text-red-800';
        case 'pending':
            return 'bg-blue-100 text-blue-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}
?>

<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center my-6">
        <h2 class="text-2xl font-semibold text-gray-800">Users</h2>
        <?php if (hasPermission('add_users')): ?>
            <a href="add.php" class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-[1.02] shadow-md">
                <i class="fa-solid fa-plus mr-2"></i> Add User
            </a>
        <?php endif; ?>
    </div>

    <!-- Flash Messages -->
    <?php include_once '../../config/functions.php'; ?>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">All Statuses</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="banned" <?= $status === 'banned' ? 'selected' : '' ?>>Banned</option>
                </select>
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="role" name="role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">All Roles</option>
                    <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="manager" <?= $role === 'manager' ? 'selected' : '' ?>>Manager</option>
                    <option value="user" <?= $role === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="business_owner" <?= $role === 'business_owner' ? 'selected' : '' ?>>Business Owner</option>
                </select>
            </div>

            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="search" name="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" placeholder="Search users..." value="<?= htmlspecialchars($search) ?>">
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

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No users found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <?php if ($user['profile_image']): ?>
                                                <img class="h-10 w-10 rounded-full object-cover" src="<?= UPLOADS_URL ?>/users/<?= $user['profile_image'] ?>" alt="<?= htmlspecialchars($user['name']) ?>">
                                            <?php else: ?>
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-indigo-500 flex items-center justify-center">
                                                    <span class="text-white font-medium text-sm"><?= getInitials($user['name']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <a href="view.php?id=<?= $user['id'] ?>" class="hover:text-blue-600 hover:underline"><?= htmlspecialchars($user['name']) ?></a>
                                            </div>
                                            <div class="text-sm text-gray-500"><?= $user['name'] ? htmlspecialchars($user['name']) : 'No username' ?></div>
                                            <!-- <div class="text-sm text-gray-500">
                                                <?= !empty($user['email']) ? htmlspecialchars($user['email']) : 'No email' ?>
                                            </div> -->
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= htmlspecialchars($user['email']) ?></div>
                                    <div class="text-sm text-gray-500"><?= $user['phone'] ? htmlspecialchars($user['phone']) : 'No phone' ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= getRoleBadgeClass($user['role']) ?>">
                                        <?= ucfirst(htmlspecialchars($user['role'])) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= getStatusBadgeClass($user['status']) ?>">
                                        <?= ucfirst(htmlspecialchars($user['status'])) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= formatDate($user['created_at']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="view.php?id=<?= $user['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3" title="View">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <?php if (hasPermission('edit_users')): ?>
                                        <a href="edit.php?id=<?= $user['id'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Edit">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (hasPermission('delete_users') && $user['id'] != $_SESSION['user_id']): ?>
                                        <a href="#" onclick="confirmDelete(<?= $user['id'] ?>, '<?= htmlspecialchars(addslashes($user['name'])) ?>')" class="text-red-600 hover:text-red-900" title="Delete">
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
        <?= paginationLinks($pagination, 'index.php?' . http_build_query(array_filter([
            'status' => $status,
            'role' => $role,
            'search' => $search
        ]))) ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="fixed inset-0 bg-black opacity-50"></div>
    <div class="relative bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirm Deletion</h3>
            <p class="text-gray-600 mb-5">Are you sure you want to delete the user <span id="deleteUserName" class="font-medium"></span>? This action cannot be undone.</p>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg">Cancel</button>
                <form id="deleteForm" action="delete.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" id="deleteId" name="id" value="">
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, name) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteUserName').textContent = name;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>

<?php require_once '../../includes/footer.php'; ?>