<?php
include_once 'includes/header.php';

// Get and sanitize search parameters
$search_query = isset($_GET['query']) ? trim(filter_var($_GET['query'], FILTER_SANITIZE_STRING)) : '';
$search_location = isset($_GET['location']) ? trim(filter_var($_GET['location'], FILTER_SANITIZE_STRING)) : '';

$items_per_page = (int)get_setting('items_per_page', 9);
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

$total_results = get_search_results_count($pdo, $search_query, $search_location);
$businesses = search_businesses($pdo, $search_query, $search_location, $current_page, $items_per_page);

$page_title = "Search Results";
if ($search_query && $search_location) {
    $page_title .= " for \"" . htmlspecialchars($search_query) . "\" near \"" . htmlspecialchars($search_location) . "\"";
} elseif ($search_query) {
     $page_title .= " for \"" . htmlspecialchars($search_query) . "\"";
} elseif ($search_location) {
     $page_title .= " near \"" . htmlspecialchars($search_location) . "\"";
}

?>

<main class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl md:text-4xl font-bold font-montserrat mb-4 text-center"><?php echo $page_title; ?></h1>
    <p class="text-center text-gray-600 mb-8"><?php echo $total_results; ?> result(s) found.</p>

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
                            <a href="city.php?name=<?php echo urlencode($business['city']); ?>&state=<?php echo urlencode($business['state']); ?>" class="hover:underline">
                                <span><?php safe_echo($business['city']); ?>, <?php safe_echo($business['state']); ?></span>
                            </a>
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
            $base_url = 'search.php?query=' . urlencode($search_query) . '&location=' . urlencode($search_location); // Pass search params
            echo render_pagination($current_page, $total_results, $items_per_page, $base_url);
        ?>

    <?php else: ?>
        <p class="text-center text-gray-500 text-xl">No businesses found matching your criteria.</p>
        <div class="text-center mt-4">
             <a href="index.php" class="text-primary-600 hover:underline">Return to Homepage</a>
        </div>
    <?php endif; ?>

</main>

<?php include_once 'includes/footer.php'; ?>