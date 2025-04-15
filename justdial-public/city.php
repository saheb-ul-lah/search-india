<?php
include_once 'includes/header.php';

$city_name = isset($_GET['name']) ? trim(filter_var($_GET['name'], FILTER_SANITIZE_STRING)) : '';
$city_state = isset($_GET['state']) ? trim(filter_var($_GET['state'], FILTER_SANITIZE_STRING)) : '';

if (empty($city_name) || empty($city_state)) {
    header("Location: index.php");
    exit;
}

// Optional: Fetch city details if needed for display
// $city = get_city_details($pdo, $city_name, $city_state);
// if (!$city) { ... handle not found ... }

$items_per_page = (int)get_setting('items_per_page', 9);
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

$total_businesses = get_business_count_for_city($pdo, $city_name, $city_state);
$businesses = get_businesses_by_city($pdo, $city_name, $city_state, $current_page, $items_per_page);

$page_title = "Businesses in " . htmlspecialchars($city_name) . ", " . htmlspecialchars($city_state);
?>

<main class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
     <div class="text-center mb-8">
        <h1 class="text-3xl md:text-4xl font-bold font-montserrat mb-2"><?php echo $page_title; ?></h1>
        <p class="text-gray-500 mt-2"><?php echo number_format($total_businesses); ?> business(es) found.</p>
    </div>

    <?php if (!empty($businesses)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
           <?php foreach ($businesses as $business): ?>
                 <?php // Re-use the business card structure ?>
                 <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover">
                    <div class="relative">
                        <a href="business.php?id=<?php echo $business['id']; ?>" class="block">
                           <img src="<?php echo get_image_url('businesses', $business['cover_image']); ?>" alt="<?php safe_echo($business['name']); ?> Cover" class="w-full h-48 object-cover">
                        </a>
                         <?php if ($business['avg_rating'] > 0): ?>
                        <div class="absolute top-4 right-4">
                            <span class="bg-white text-gray-800 text-xs font-semibold px-3 py-1 rounded-full shadow-md">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <?php echo number_format($business['avg_rating'], 1); ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                         <?php if (!empty($business['category_names'])):
                            $categories = explode(',', $business['category_names']);
                            $first_category = trim($categories[0]);
                        ?>
                        <div class="mb-2">
                            <a href="category.php?slug=<?php /* Need slug */ ?>" class="text-xs font-medium text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 inline-block"><?php safe_echo($first_category); ?></a>
                        </div>
                        <?php endif; ?>
                         <h3 class="text-xl font-bold mb-2">
                             <a href="business.php?id=<?php echo $business['id']; ?>" class="hover:text-primary-600 transition-colors duration-200 business-link" data-business-id="<?php echo $business['id']; ?>">
                                <?php safe_echo($business['name']); ?>
                            </a>
                        </h3>
                        <div class="flex items-center text-gray-500 text-sm mb-4">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span><?php safe_echo($business['city']); ?>, <?php safe_echo($business['state']); ?></span>
                        </div>
                         <p class="text-gray-600 text-sm mb-4">
                            <?php safe_echo($business['short_description'] ? mb_strimwidth($business['short_description'], 0, 100, '...') : 'No description available.'); ?>
                        </p>
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span><?php echo number_format($business['review_count']); ?> reviews</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php
            // Pagination
            $base_url = 'city.php?name=' . urlencode($city_name) . '&state=' . urlencode($city_state); // Pass city params
            echo render_pagination($current_page, $total_businesses, $items_per_page, $base_url);
        ?>

    <?php else: ?>
        <p class="text-center text-gray-500 text-xl mt-8">No businesses found in this city.</p>
    <?php endif; ?>

</main>

<?php include_once 'includes/footer.php'; ?>