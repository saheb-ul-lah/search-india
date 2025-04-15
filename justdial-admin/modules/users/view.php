<?php
$pageTitle = 'Users';
require_once '../../includes/header.php';

// Check permissions
if (!hasPermission('view_users')) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('modules/dashboard/index.php');
}

// Get user ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    setFlashMessage('error', 'Invalid user ID');
    redirect('modules/users/index.php');
}

// Get user details
$db = db();
$db->query("SELECT * FROM users WHERE id = :id");
$db->bind(':id', $id);
$user = $db->single();

if (!$user) {
    setFlashMessage('error', 'User not found');
    redirect('modules/users/index.php');
}

// Get user's businesses (if they are a business owner)
$businesses = [];
if ($user['role'] === 'business_owner') {
    $db->query("SELECT id, name, status FROM businesses WHERE user_id = :user_id ORDER BY name ASC");
    $db->bind(':user_id', $id);
    $businesses = $db->resultSet();
}

// Get user's reviews
$db->query("SELECT r.*, b.name as business_name 
           FROM reviews r 
           LEFT JOIN businesses b ON r.business_id = b.id 
           WHERE r.user_id = :user_id 
           ORDER BY r.created_at DESC 
           LIMIT 5");
$db->bind(':user_id', $id);
$reviews = $db->resultSet();

// Get user's recent activity
$db->query("SELECT * FROM activity_logs 
           WHERE user_id = :user_id 
           ORDER BY created_at DESC 
           LIMIT 10");
$db->bind(':user_id', $id);
$activities = $db->resultSet();

$pageTitle = 'View User: ' . htmlspecialchars($user['name']);
require_once '../../includes/header.php';
?>

<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center my-6">
        <h2 class="text-2xl font-semibold text-gray-800">User Profile</h2>
        <div class="flex space-x-2">
            <?php if (hasPermission('edit_users')): ?>
                <a href="edit.php?id=<?= $id ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-[1.02] shadow-md">
                    <i class="fa-solid fa-edit mr-2"></i> Edit User
                </a>
            <?php endif; ?>
            <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-[1.02] shadow-md">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back to Users
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php include_once '../../config/functions.php'; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- User Profile Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6 text-center">
                <?php if ($user['profile_image']): ?>
                    <img class="w-32 h-32 rounded-full mx-auto mb-4 object-cover" src="<?= UPLOADS_URL ?>/users/<?= $user['profile_image'] ?>" alt="<?= htmlspecialchars($user['name']) ?>">
                <?php else: ?>
                    <div class="w-32 h-32 rounded-full bg-gradient-to-r from-blue-400 to-indigo-500 flex items-center justify-center mx-auto mb-4">
                        <span class="text-white font-medium text-2xl"><?= getInitials($user['name']) ?></span>
                    </div>
                <?php endif; ?>
                
                <h3 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($user['name']) ?></h3>
                <p class="text-gray-600 mb-2"><?= htmlspecialchars($user['email']) ?></p>
                
                <div class="flex justify-center space-x-2 mb-4">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= getRoleBadgeClass($user['role']) ?>">
                        <?= ucfirst($user['role']) ?>
                    </span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= getStatusBadgeClass($user['status']) ?>">
                        <?= ucfirst($user['status']) ?>
                    </span>
                </div>
                
                <?php if (hasPermission('delete_users') && $user['id'] != $_SESSION['user_id']): ?>
                    <button onclick="confirmDelete(<?= $id ?>, '<?= htmlspecialchars(addslashes($user['name'])) ?>')" class="text-red-600 hover:text-red-800 text-sm font-medium">
                        <i class="fa-solid fa-trash mr-1"></i> Delete User
                    </button>
                <?php endif; ?>
            </div>
            
            <div class="border-t border-gray-200 px-6 py-4">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Username</h4>
                        <p class="text-sm text-gray-900"><?= $user['username'] ? htmlspecialchars($user['username']) : 'Not set' ?></p>
                    </div>
                    
                    <div>
                        <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</h4>
                        <p class="text-sm text-gray-900"><?= $user['phone'] ? htmlspecialchars($user['phone']) : 'Not set' ?></p>
                    </div>
                    
                    <div>
                        <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Member Since</h4>
                        <p class="text-sm text-gray-900"><?= formatDate($user['created_at']) ?></p>
                    </div>
                    
                    <div>
                        <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</h4>
                        <p class="text-sm text-gray-900"><?= formatDate($user['updated_at']) ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="md:col-span-2 space-y-6">
            <!-- Contact Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Contact Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php if ($user['address']): ?>
                        <div class="md:col-span-2">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Address</h4>
                            <p class="text-sm text-gray-900"><?= htmlspecialchars($user['address']) ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($user['city'] || $user['state']): ?>
                        <div>
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">City/State</h4>
                            <p class="text-sm text-gray-900">
                                <?= $user['city'] ? htmlspecialchars($user['city']) : '' ?>
                                <?= ($user['city'] && $user['state']) ? ', ' : '' ?>
                                <?= $user['state'] ? htmlspecialchars($user['state']) : '' ?>
                            </p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($user['postal_code']): ?>
                        <div>
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Postal Code</h4>
                            <p class="text-sm text-gray-900"><?= htmlspecialchars($user['postal_code']) ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($user['country']): ?>
                        <div>
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Country</h4>
                            <p class="text-sm text-gray-900"><?= htmlspecialchars($user['country']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($user['bio']): ?>
                    <div class="mt-6">
                        <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Bio</h4>
                        <p class="text-sm text-gray-900 whitespace-pre-line"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Businesses Section -->
            <?php if ($user['role'] === 'business_owner' && !empty($businesses)): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Businesses</h3>
                        <a href="../businesses/index.php?user_id=<?= $id ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View All <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($businesses as $business): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($business['name']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= getStatusBadgeClass($business['status']) ?>">
                                                <?= ucfirst($business['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="../businesses/view.php?id=<?= $business['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Recent Reviews -->
            <?php if (!empty($reviews)): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Reviews</h3>
                        <a href="../reviews/index.php?user_id=<?= $id ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View All <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    
                    <div class="space-y-4">
                        <?php foreach ($reviews as $review): ?>
                            <div class="border-b border-gray-200 pb-4 last:border-b-0 last:pb-0">
                                <div class="flex justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900"><?= htmlspecialchars($review['title']) ?></h4>
                                        <p class="text-xs text-gray-500">For <?= htmlspecialchars($review['business_name']) ?></p>
                                    </div>
                                    <div class="flex">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?= $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300' ?> text-sm"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mt-2 line-clamp-2"><?= htmlspecialchars($review['content']) ?></p>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-xs text-gray-500"><?= formatDate($review['created_at']) ?></span>
                                    <a href="../reviews/view.php?id=<?= $review['id'] ?>" class="text-xs text-blue-600 hover:text-blue-800">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Recent Activity -->
            <?php if (!empty($activities)): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
                    
                    <div class="space-y-4">
                        <?php foreach ($activities as $activity): ?>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-history text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-900"><?= htmlspecialchars($activity['action']) ?></p>
                                    <p class="text-xs text-gray-500"><?= formatDate($activity['created_at'], true) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
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