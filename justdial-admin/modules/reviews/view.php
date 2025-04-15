<?php
$pageTitle = 'View Review';
require_once '../../includes/header.php';

// Check if user has permission
if (!isManager()) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('modules/dashboard/index.php');
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('error', 'Review ID is required');
    redirect('modules/reviews/index.php');
}

$reviewId = (int)$_GET['id'];

// Get review data
$db = db();
$db->query("SELECT r.*, b.name as business_name, u.name as user_name, u.email as user_email 
            FROM reviews r 
            LEFT JOIN businesses b ON r.business_id = b.id 
            LEFT JOIN users u ON r.user_id = u.id 
            WHERE r.id = :id");
$db->bind(':id', $reviewId);
$review = $db->single();

if (!$review) {
    setFlashMessage('error', 'Review not found');
    redirect('modules/reviews/index.php');
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid CSRF token');
        redirect('modules/reviews/view.php?id=' . $reviewId);
    }
    
    // Validate input
    $status = sanitize($_POST['status']);
    
    try {
        // Update review status
        $db->query("UPDATE reviews SET status = :status, updated_at = NOW() WHERE id = :id");
        $db->bind(':status', $status);
        $db->bind(':id', $reviewId);
        $db->execute();
        
        // Update the review object to reflect the change
        $review['status'] = $status;
        
        // Log activity
        logActivity($_SESSION['user_id'], 'update', 'reviews', $reviewId, 'Updated review status to ' . $status);
        
        setFlashMessage('success', 'Review status updated successfully');
        redirect('modules/reviews/view.php?id=' . $reviewId);
        
    } catch (Exception $e) {
        setFlashMessage('error', 'Error updating review: ' . $e->getMessage());
        redirect('modules/reviews/view.php?id=' . $reviewId);
    }
}
?>

<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center my-6">
        <h2 class="text-2xl font-semibold text-gray-800">View Review</h2>
        <div class="flex space-x-2">
            <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back to Reviews
            </a>
            <a href="delete.php?id=<?= $reviewId ?>" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300" onclick="return confirm('Are you sure you want to delete this review?')">
                <i class="fa-solid fa-trash mr-2"></i> Delete
            </a>
        </div>
    </div>
    
    <!-- Flash Message -->
    <?php $flashMessage = getFlashMessage(); ?>
    <?php if ($flashMessage): ?>
    <div class="bg-<?= $flashMessage['type'] === 'error' ? 'red' : 'green' ?>-100 border border-<?= $flashMessage['type'] === 'error' ? 'red' : 'green' ?>-400 text-<?= $flashMessage['type'] === 'error' ? 'red' : 'green' ?>-700 px-4 py-3 rounded relative mb-6" role="alert">
        <span class="block sm:inline"><?= $flashMessage['message'] ?></span>
    </div>
    <?php endif; ?>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Review Details -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($review['title']) ?></h3>
                    <div class="flex items-center">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fa-solid fa-star <?= $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300' ?>"></i>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="mb-6">
                    <p class="text-gray-700 whitespace-pre-line"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Business</label>
                        <p class="text-gray-900">
                            <a href="<?= BASE_URL ?>/modules/businesses/view.php?id=<?= $review['business_id'] ?>" class="text-blue-600 hover:underline">
                                <?= htmlspecialchars($review['business_name']) ?>
                            </a>
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">User</label>
                        <p class="text-gray-900">
                            <a href="<?= BASE_URL ?>/modules/users/view.php?id=<?= $review['user_id'] ?>" class="text-blue-600 hover:underline">
                                <?= htmlspecialchars($review['user_name']) ?>
                            </a>
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Created</label>
                        <p class="text-gray-900"><?= formatDate($review['created_at']) ?></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                        <p class="text-gray-900"><?= formatDate($review['updated_at']) ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Status Management -->
        <div class="space-y-4">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Review Status</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Current Status</label>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        <?= $review['status'] === 'approved' ? 'bg-green-100 text-green-800' : 
                           ($review['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                           'bg-red-100 text-red-800') ?>">
                        <?= ucfirst($review['status']) ?>
                    </span>
                </div>
                
                <?php if (isManager()): ?>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Update Status</label>
                        <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="approved" <?= $review['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="pending" <?= $review['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="rejected" <?= $review['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
                        Update Status
                    </button>
                </form>
                <?php endif; ?>
            </div>
            
            <!-- User Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">User Information</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                    <p class="text-gray-900"><?= htmlspecialchars($review['user_name']) ?></p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                    <p class="text-gray-900"><?= htmlspecialchars($review['user_email']) ?></p>
                </div>
                
                <a href="<?= BASE_URL ?>/modules/users/view.php?id=<?= $review['user_id'] ?>" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg inline-flex items-center justify-center">
                    <i class="fa-solid fa-user mr-2"></i> View User Profile
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>