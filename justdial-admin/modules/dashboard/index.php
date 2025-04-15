<?php
$pageTitle = 'Dashboard';
$useCharts = true;
require_once '../../includes/header.php';

// Get counts for dashboard
$db = db();

// Count businesses
$db->query("SELECT COUNT(*) as total FROM businesses");
$businessCount = $db->single()['total'];

// Count active businesses
$db->query("SELECT COUNT(*) as total FROM businesses WHERE status = 'active'");
$activeBusinessCount = $db->single()['total'];

// Count pending businesses
$db->query("SELECT COUNT(*) as total FROM businesses WHERE status = 'pending'");
$pendingBusinessCount = $db->single()['total'];

// Count categories
$db->query("SELECT COUNT(*) as total FROM categories");
$categoryCount = $db->single()['total'];

// Count users
$db->query("SELECT COUNT(*) as total FROM users");
$userCount = $db->single()['total'];

// Count reviews
$db->query("SELECT COUNT(*) as total FROM reviews");
$reviewCount = $db->single()['total'];

// Count pending reviews
$db->query("SELECT COUNT(*) as total FROM reviews WHERE status = 'pending'");
$pendingReviewCount = $db->single()['total'];

// Get recent businesses
$db->query("SELECT b.*, u.name as owner_name 
            FROM businesses b 
            LEFT JOIN users u ON b.owner_id = u.id 
            ORDER BY b.created_at DESC 
            LIMIT 5");
$recentBusinesses = $db->resultSet();

// Get recent reviews
$db->query("SELECT r.*, u.name as user_name, b.name as business_name 
            FROM reviews r 
            LEFT JOIN users u ON r.user_id = u.id 
            LEFT JOIN businesses b ON r.business_id = b.id 
            ORDER BY r.created_at DESC 
            LIMIT 5");
$recentReviews = $db->resultSet();

// Get business registrations by month (for chart)
$db->query("SELECT 
            MONTH(created_at) as month, 
            COUNT(*) as count 
            FROM businesses 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) 
            GROUP BY MONTH(created_at) 
            ORDER BY MONTH(created_at)");
$businessRegistrations = $db->resultSet();

// Format data for chart
$months = [];
$businessData = [];

// Initialize all months with 0
for ($i = 1; $i <= 12; $i++) {
    $months[] = date('M', mktime(0, 0, 0, $i, 1));
    $businessData[$i] = 0;
}

// Fill in actual data
foreach ($businessRegistrations as $registration) {
    $businessData[$registration['month']] = $registration['count'];
}

// Get business categories distribution (for pie chart)
$db->query("SELECT c.name, COUNT(bc.business_id) as count 
            FROM categories c 
            LEFT JOIN business_categories bc ON c.id = bc.category_id 
            GROUP BY c.id 
            ORDER BY count DESC 
            LIMIT 6");
$categoryDistribution = $db->resultSet();

// Format data for pie chart
$categoryLabels = [];
$categoryData = [];

foreach ($categoryDistribution as $category) {
    $categoryLabels[] = $category['name'];
    $categoryData[] = $category['count'];
}
?>

<div class="container px-6 mx-auto">
    <h2 class="text-2xl font-semibold text-gray-800 my-6">Dashboard</h2>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Businesses Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-5 bg-gradient-to-r from-blue-500 to-blue-600">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-white p-3 rounded-lg">
                        <i class="fa-solid fa-building text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-blue-100 truncate">
                                Total Businesses
                            </dt>
                            <dd>
                                <div class="text-lg font-bold text-white"><?= $businessCount ?></div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="<?= BASE_URL ?>/modules/businesses/index.php" class="font-medium text-blue-600 hover:text-blue-500">
                        View all
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Categories Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-5 bg-gradient-to-r from-green-500 to-green-600">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-white p-3 rounded-lg">
                        <i class="fa-solid fa-tags text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-green-100 truncate">
                                Total Categories
                            </dt>
                            <dd>
                                <div class="text-lg font-bold text-white"><?= $categoryCount ?></div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="<?= BASE_URL ?>/modules/categories/index.php" class="font-medium text-green-600 hover:text-green-500">
                        View all
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Users Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-5 bg-gradient-to-r from-purple-500 to-purple-600">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-white p-3 rounded-lg">
                        <i class="fa-solid fa-users text-purple-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-purple-100 truncate">
                                Total Users
                            </dt>
                            <dd>
                                <div class="text-lg font-bold text-white"><?= $userCount ?></div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="<?= BASE_URL ?>/modules/users/index.php" class="font-medium text-purple-600 hover:text-purple-500">
                        View all
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Reviews Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-5 bg-gradient-to-r from-yellow-500 to-yellow-600">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-white p-3 rounded-lg">
                        <i class="fa-solid fa-star text-yellow-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-yellow-100 truncate">
                                Total Reviews
                            </dt>
                            <dd>
                                <div class="text-lg font-bold text-white"><?= $reviewCount ?></div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="<?= BASE_URL ?>/modules/reviews/index.php" class="font-medium text-yellow-600 hover:text-yellow-500">
                        View all
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Business Registrations Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Business Registrations</h3>
            <div class="h-80">
                <canvas id="businessChart"></canvas>
            </div>
        </div>
        
        <!-- Category Distribution Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Category Distribution</h3>
            <div class="h-80">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Businesses -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Recent Businesses</h3>
            </div>
            <div class="divide-y divide-gray-200">
                <?php if (empty($recentBusinesses)): ?>
                <div class="px-6 py-4 text-gray-500">No businesses found</div>
                <?php else: ?>
                <?php foreach ($recentBusinesses as $business): ?>
                <div class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <?php if ($business['logo']): ?>
                            <img class="h-10 w-10 rounded-full object-cover" src="<?= UPLOADS_URL ?>/businesses/<?= $business['logo'] ?>" alt="<?= $business['name'] ?>">
                            <?php else: ?>
                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                <i class="fa-solid fa-building text-gray-500"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                <a href="<?= BASE_URL ?>/modules/businesses/view.php?id=<?= $business['id'] ?>" class="hover:underline">
                                    <?= $business['name'] ?>
                                </a>
                            </div>
                            <div class="text-sm text-gray-500">
                                <?= $business['city'] ?>, <?= $business['state'] ?>
                            </div>
                        </div>
                        <div class="ml-auto">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $business['status'] === 'active' ? 'bg-green-100 text-green-800' : ($business['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                <?= ucfirst($business['status']) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="bg-gray-50 px-6 py-3">
                <div class="text-sm">
                    <a href="<?= BASE_URL ?>/modules/businesses/index.php" class="font-medium text-blue-600 hover:text-blue-500">
                        View all businesses
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Reviews -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Recent Reviews</h3>
            </div>
            <div class="divide-y divide-gray-200">
                <?php if (empty($recentReviews)): ?>
                <div class="px-6 py-4 text-gray-500">No reviews found</div>
                <?php else: ?>
                <?php foreach ($recentReviews as $review): ?>
                <div class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                <i class="fa-solid fa-user text-gray-500"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                <?= $review['user_name'] ?> reviewed <a href="<?= BASE_URL ?>/modules/businesses/view.php?id=<?= $review['business_id'] ?>" class="hover:underline"><?= $review['business_name'] ?></a>
                            </div>
                            <div class="text-sm text-gray-500">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                <?php if ($i < $review['rating']): ?>
                                <i class="fa-solid fa-star text-yellow-400"></i>
                                <?php else: ?>
                                <i class="fa-regular fa-star text-yellow-400"></i>
                                <?php endif; ?>
                                <?php endfor; ?>
                                <span class="ml-2"><?= formatDate($review['created_at']) ?></span>
                            </div>
                        </div>
                        <div class="ml-auto">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $review['status'] === 'approved' ? 'bg-green-100 text-green-800' : ($review['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                <?= ucfirst($review['status']) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="bg-gray-50 px-6 py-3">
                <div class="text-sm">
                    <a href="<?= BASE_URL ?>/modules/reviews/index.php" class="font-medium text-blue-600 hover:text-blue-500">
                        View all reviews
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Pending Businesses -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                    <i class="fa-solid fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-5">
                    <h3 class="text-lg font-semibold text-gray-800">Pending Businesses</h3>
                    <p class="text-3xl font-bold text-gray-900"><?= $pendingBusinessCount ?></p>
                </div>
            </div>
            <div class="mt-4">
                <a href="<?= BASE_URL ?>/modules/businesses/index.php?status=pending" class="text-sm font-medium text-yellow-600 hover:text-yellow-500">
                    View pending businesses
                </a>
            </div>
        </div>
        
        <!-- Pending Reviews -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-orange-100 rounded-md p-3">
                    <i class="fa-solid fa-star-half-stroke text-orange-600 text-xl"></i>
                </div>
                <div class="ml-5">
                    <h3 class="text-lg font-semibold text-gray-800">Pending Reviews</h3>
                    <p class="text-3xl font-bold text-gray-900"><?= $pendingReviewCount ?></p>
                </div>
            </div>
            <div class="mt-4">
                <a href="<?= BASE_URL ?>/modules/reviews/index.php?status=pending" class="text-sm font-medium text-orange-600 hover:text-orange-500">
                    View pending reviews
                </a>
            </div>
        </div>
        
        <!-- Active Businesses -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <i class="fa-solid fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-5">
                    <h3 class="text-lg font-semibold text-gray-800">Active Businesses</h3>
                    <p class="text-3xl font-bold text-gray-900"><?= $activeBusinessCount ?></p>
                </div>
            </div>
            <div class="mt-4">
                <a href="<?= BASE_URL ?>/modules/businesses/index.php?status=active" class="text-sm font-medium text-green-600 hover:text-green-500">
                    View active businesses
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Business Registrations Chart
    const businessCtx = document.getElementById('businessChart').getContext('2d');
    const businessChart = new Chart(businessCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($months) ?>,
            datasets: [{
                label: 'Business Registrations',
                data: <?= json_encode(array_values($businessData)) ?>,
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
    
    // Category Distribution Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($categoryLabels) ?>,
            datasets: [{
                data: <?= json_encode($categoryData) ?>,
                backgroundColor: [
                    'rgba(59, 130, 246, 0.7)',
                    'rgba(16, 185, 129, 0.7)',
                    'rgba(245, 158, 11, 0.7)',
                    'rgba(239, 68, 68, 0.7)',
                    'rgba(139, 92, 246, 0.7)',
                    'rgba(236, 72, 153, 0.7)'
                ],
                borderColor: [
                    'rgba(59, 130, 246, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(245, 158, 11, 1)',
                    'rgba(239, 68, 68, 1)',
                    'rgba(139, 92, 246, 1)',
                    'rgba(236, 72, 153, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>

<?php require_once '../../includes/footer.php'; ?>