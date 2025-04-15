<?php
$pageTitle = 'View Business';
require_once '../../includes/header.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('error', 'Business ID is required');
    redirect('modules/businesses/index.php');
}

$businessId = (int)$_GET['id'];

// Get business data
$db = db();
$db->query("SELECT b.*, u.name as owner_name, u.email as owner_email 
            FROM businesses b 
            LEFT JOIN users u ON b.owner_id = u.id 
            WHERE b.id = :id");
$db->bind(':id', $businessId);
$business = $db->single();

if (!$business) {
    setFlashMessage('error', 'Business not found');
    redirect('modules/businesses/index.php');
}

// Get business categories
$businessCategories = getBusinessCategories($businessId);

// Get business images
$db->query("SELECT * FROM business_images WHERE business_id = :business_id ORDER BY sort_order");
$db->bind(':business_id', $businessId);
$businessImages = $db->resultSet();

// Get business services
$db->query("SELECT * FROM services WHERE business_id = :business_id ORDER BY name");
$db->bind(':business_id', $businessId);
$services = $db->resultSet();

// Get business reviews
$db->query("SELECT r.*, u.name as user_name 
            FROM reviews r 
            LEFT JOIN users u ON r.user_id = u.id 
            WHERE r.business_id = :business_id 
            ORDER BY r.created_at DESC 
            LIMIT 5");
$db->bind(':business_id', $businessId);
$reviews = $db->resultSet();

// Get review stats
$averageRating = getAverageRating($businessId);
$reviewCount = getRatingCount($businessId);

// Process status change
if (isset($_POST['change_status']) && isManager()) {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid CSRF token');
        redirect('modules/businesses/view.php?id=' . $businessId);
    }

    $newStatus = sanitize($_POST['status']);

    $db->query("UPDATE businesses SET status = :status, updated_at = NOW() WHERE id = :id");
    $db->bind(':status', $newStatus);
    $db->bind(':id', $businessId);

    if ($db->execute()) {
        // Log activity
        logActivity($_SESSION['user_id'], 'update_status', 'businesses', $businessId, 'Changed business status to: ' . $newStatus);

        setFlashMessage('success', 'Business status updated successfully');
        redirect('modules/businesses/view.php?id=' . $businessId);
    } else {
        setFlashMessage('error', 'Failed to update business status');
        redirect('modules/businesses/view.php?id=' . $businessId);
    }
}
?>

<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center my-6">
        <h2 class="text-2xl font-semibold text-gray-800"><?= $business['name'] ?></h2>
        <div>
            <?php if (isManager()): ?>
                <a href="edit.php?id=<?= $businessId ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg mr-2 transition-all duration-300">
                    <i class="fa-solid fa-edit mr-2"></i> Edit
                </a>
            <?php endif; ?>
            <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back to Businesses
            </a>
        </div>
    </div>

    <!-- Business Header -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="relative h-48 bg-gray-200">
            <?php if ($business['cover_image']): ?>
                <img src="<?= UPLOADS_URL ?>/businesses/<?= $business['cover_image'] ?>" alt="<?= $business['name'] ?>" class="w-full h-full object-cover">
            <?php endif; ?>
            <div class="absolute bottom-4 left-4 flex items-center">
                <div class="h-20 w-20 bg-white rounded-lg shadow-md overflow-hidden">
                    <?php if ($business['logo']): ?>
                        <img src="<?= UPLOADS_URL ?>/businesses/<?= $business['logo'] ?>" alt="<?= $business['name'] ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <i class="fa-solid fa-building text-gray-500 text-3xl"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-white drop-shadow-lg"><?= $business['name'] ?></h1>
                    <div class="flex items-center mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $business['status'] === 'active' ? 'bg-green-100 text-green-800' : ($business['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($business['status'] === 'inactive' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800')) ?>">
                            <?= ucfirst($business['status']) ?>
                        </span>
                        <?php if ($business['is_verified']): ?>
                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                <i class="fa-solid fa-check-circle mr-1 my-auto"></i> Verified
                            </span>
                        <?php endif; ?>
                        <?php if ($business['is_featured']): ?>
                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                <i class="fa-solid fa-star mr-1 my-auto"></i> Featured
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">About</h3>
                    <p class="text-gray-700 mb-4"><?= nl2br($business['description'] ?: 'No description available.') ?></p>

                    <?php if (!empty($businessCategories)): ?>
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Categories</h4>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($businessCategories as $category): ?>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                        <?= $category['name'] ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Contact Information</h3>
                    <ul class="space-y-2">
                        <?php if ($business['phone']): ?>
                            <li class="flex items-center">
                                <i class="fa-solid fa-phone text-gray-500 w-5"></i>
                                <span class="ml-2 text-gray-700"><?= $business['phone'] ?></span>
                            </li>
                        <?php endif; ?>

                        <?php if ($business['email']): ?>
                            <li class="flex items-center">
                                <i class="fa-solid fa-envelope text-gray-500 w-5"></i>
                                <span class="ml-2 text-gray-700"><?= $business['email'] ?></span>
                            </li>
                        <?php endif; ?>

                        <?php if ($business['website']): ?>
                            <li class="flex items-center">
                                <i class="fa-solid fa-globe text-gray-500 w-5"></i>
                                <a href="<?= $business['website'] ?>" target="_blank" class="ml-2 text-blue-600 hover:underline"><?= $business['website'] ?></a>
                            </li>
                        <?php endif; ?>

                        <?php if ($business['address']): ?>
                            <li class="flex items-start">
                                <i class="fa-solid fa-location-dot text-gray-500 w-5 mt-1"></i>
                                <span class="ml-2 text-gray-700">
                                    <?= $business['address'] ?><br>
                                    <?= $business['city'] ?>, <?= $business['state'] ?> <?= $business['postal_code'] ?><br>
                                    <?= $business['country'] ?>
                                </span>
                            </li>
                        <?php else: ?>
                            <li class="flex items-center">
                                <i class="fa-solid fa-location-dot text-gray-500 w-5"></i>
                                <span class="ml-2 text-gray-700">
                                    <?= $business['city'] ?>, <?= $business['state'] ?> <?= $business['postal_code'] ?><br>
                                    <?= $business['country'] ?>
                                </span>
                            </li>
                        <?php endif; ?>

                        <?php if ($business['founded_year']): ?>
                            <li class="flex items-center">
                                <i class="fa-solid fa-calendar text-gray-500 w-5"></i>
                                <span class="ml-2 text-gray-700">Founded in <?= $business['founded_year'] ?></span>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <?php if ($business['owner_name']): ?>
                        <h3 class="text-lg font-medium text-gray-900 mt-4 mb-2">Owner</h3>
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                <i class="fa-solid fa-user text-gray-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900"><?= $business['owner_name'] ?></p>
                                <p class="text-sm text-gray-500"><?= $business['owner_email'] ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isManager()): ?>
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Admin Actions</h3>
                            <form method="POST" class="flex items-center">
                                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                <select name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                                    <option value="pending" <?= $business['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="active" <?= $business['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= $business['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    <option value="rejected" <?= $business['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                </select>
                                <button type="submit" name="change_status" class="ml-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
                                    Update Status
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Services -->
    <?php if (!empty($services)): ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Services</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($services as $service): ?>
                        <div class="border rounded-lg overflow-hidden">
                            <?php if ($service['image']): ?>
                                <img src="<?= UPLOADS_URL ?>/services/<?= $service['image'] ?>" alt="<?= $service['name'] ?>" class="w-full h-40 object-cover">
                            <?php else: ?>
                                <div class="w-full h-40 bg-gray-200 flex items-center justify-center">
                                    <i class="fa-solid fa-briefcase text-gray-500 text-3xl"></i>
                                </div>
                            <?php endif; ?>
                            <div class="p-4">
                                <h4 class="text-lg font-medium text-gray-900"><?= $service['name'] ?></h4>
                                <p class="text-gray-700 mt-1"><?= $service['description'] ?></p>
                                <?php if ($service['price']): ?>
                                    <div class="mt-2 flex items-center justify-between">
                                        <span class="text-lg font-bold text-gray-900">₹<?= number_format($service['price'], 2) ?></span>
                                        <span class="text-sm text-gray-500"><?= ucfirst($service['price_type']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Reviews -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Reviews</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center mb-6">
                <div class="mr-4">
                    <div class="text-3xl font-bold text-gray-900"><?= number_format($averageRating, 1) ?></div>
                    <div class="flex items-center">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php if ($i <= $averageRating): ?>
                                <i class="fa-solid fa-star text-yellow-400"></i>
                            <?php elseif ($i - 0.5 <= $averageRating): ?>
                                <i class="fa-solid fa-star-half-stroke text-yellow-400"></i>
                            <?php else: ?>
                                <i class="fa-regular fa-star text-yellow-400"></i>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <div class="text-sm text-gray-500 mt-1"><?= $reviewCount ?> reviews</div>
                </div>
                <div class="flex-1">
                    <!-- Rating bars -->
                    <?php
                    // Get rating distribution
                    $db->query("SELECT rating, COUNT(*) as count FROM reviews WHERE business_id = :business_id AND status = 'approved' GROUP BY rating ORDER BY rating DESC");
                    $db->bind(':business_id', $businessId);
                    $ratingDistribution = $db->resultSet();

                    $ratingCounts = [];
                    foreach ($ratingDistribution as $rating) {
                        $ratingCounts[$rating['rating']] = $rating['count'];
                    }
                    ?>

                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <?php
                        $count = isset($ratingCounts[$i]) ? $ratingCounts[$i] : 0;
                        $percentage = $reviewCount > 0 ? ($count / $reviewCount) * 100 : 0;
                        ?>
                        <div class="flex items-center">
                            <span class="text-sm text-gray-700 w-6"><?= $i ?></span>
                            <i class="fa-solid fa-star text-yellow-400 ml-1 mr-2"></i>
                            <div class="flex-1 h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-yellow-400 rounded-full" style="width: <?= $percentage ?>%"></div>
                            </div>
                            <span class="text-sm text-gray-700 ml-2 w-8"><?= $count ?></span>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <?php if (empty($reviews)): ?>
                <div class="text-gray-500">No reviews yet</div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($reviews as $review): ?>
                        <div class="border-b border-gray-200 pb-4 last:border-b-0 last:pb-0">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <i class="fa-solid fa-user text-gray-500"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900"><?= $review['user_name'] ?></p>
                                        <p class="text-sm text-gray-500"><?= formatDate($review['created_at']) ?></p>
                                    </div>
                                </div>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $review['status'] === 'approved' ? 'bg-green-100 text-green-800' : ($review['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                    <?= ucfirst($review['status']) ?>
                                </span>
                            </div>
                            <div class="mt-2">
                                <div class="flex items-center mb-1">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $review['rating']): ?>
                                            <i class="fa-solid fa-star text-yellow-400"></i>
                                        <?php else: ?>
                                            <i class="fa-regular fa-star text-yellow-400"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                                <?php if ($review['title']): ?>
                                    <h4 class="text-sm font-medium text-gray-900"><?= $review['title'] ?></h4>
                                <?php endif; ?>
                                <p class="text-gray-700 mt-1"><?= nl2br($review['comment']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-4">
                    <a href="<?= BASE_URL ?>/modules/reviews/index.php?business_id=<?= $businessId ?>" class="text-blue-600 hover:text-blue-800 font-medium">
                        View all reviews
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Business Images -->
    <?php if (!empty($businessImages)): ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Gallery</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php foreach ($businessImages as $image): ?>
                        <div class="relative rounded-lg overflow-hidden">
                            <img src="<?= UPLOADS_URL ?>/businesses/<?= $image['image'] ?>" alt="<?= $image['title'] ?: $business['name'] ?>" class="w-full h-40 object-cover">
                            <?php if ($image['title']): ?>
                                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-2 text-sm">
                                    <?= $image['title'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Business Stats -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Business Stats</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-blue-500 text-xl mb-1">
                        <i class="fa-solid fa-eye"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900"><?= $business['views'] ?></div>
                    <div class="text-sm text-gray-500">Total Views</div>
                </div>

                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-green-500 text-xl mb-1">
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900"><?= $reviewCount ?></div>
                    <div class="text-sm text-gray-500">Reviews</div>
                </div>

                <div class="bg-yellow-50 rounded-lg p-4">
                    <div class="text-yellow-500 text-xl mb-1">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900"><?= count($services) ?></div>
                    <div class="text-sm text-gray-500">Services</div>
                </div>

                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="text-purple-500 text-xl mb-1">
                        <i class="fa-solid fa-calendar"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900"><?= formatDate($business['created_at'], 'd M Y') ?></div>
                    <div class="text-sm text-gray-500">Registered On</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>