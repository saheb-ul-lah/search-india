<?php
include_once 'includes/header.php';

$category_slug = isset($_GET['slug']) ? trim(filter_var($_GET['slug'], FILTER_SANITIZE_STRING)) : '';

if (empty($category_slug)) {
    // Redirect or show error if no slug provided
    header("Location: index.php"); // Redirect to homepage
    exit;
}

$category = get_category_by_slug($pdo, $category_slug);

if (!$category) {
    // Handle category not found - show 404 or specific message
     http_response_code(404);
     $page_title = "Category Not Found";
     // You might want a dedicated 404 include here
     echo "<main class='container mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center'><h1 class='text-3xl font-bold mb-4'>$page_title</h1><p>The category you requested could not be found.</p><a href='index.php' class='text-primary-600 hover:underline mt-4 inline-block'>Return to Homepage</a></main>";
     include_once 'includes/footer.php';
     exit;
}

$items_per_page = (int)get_setting('items_per_page', 9);
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

$total_businesses = get_business_count_for_category($pdo, $category['id']);
$businesses = get_businesses_by_category($pdo, $category['id'], $current_page, $items_per_page);

$page_title = "Businesses in " . htmlspecialchars($category['name']);
?>

<main class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center bg-primary-100 rounded-full p-4 mb-4">
             <i class="<?php echo render_icon($category['icon'], 'fa-tags'); ?> text-primary-600 text-4xl"></i>
        </div>
        <h1 class="text-3xl md:text-4xl font-bold font-montserrat mb-2"><?php safe_echo($category['name']); ?></h1>
        <?php if ($category['description']): ?>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto"><?php safe_echo($category['description']); ?></p>
        <?php endif; ?>
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
            $base_url = 'category.php?slug=' . urlencode($category_slug); // Pass slug
            echo render_pagination($current_page, $total_businesses, $items_per_page, $base_url);
        ?>

    <?php else: ?>
        <p class="text-center text-gray-500 text-xl mt-8">No businesses found in this category.</p>
    <?php endif; ?>

</main>

<?php include_once 'includes/footer.php'; ?>