<?php
// justdial-public/includes/functions.php

// Ensure DB connection is available
// Note: Including db.php in every function isn't ideal for performance.
// A better approach uses dependency injection or a single global/registry,
// but this is simpler for demonstration.
include_once 'db.php';

// --- Settings ---
$site_settings = null; // Cache settings

function get_all_settings($pdo) {
    global $site_settings;
    if ($site_settings === null) {
        try {
            $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
            $settings_raw = $stmt->fetchAll();
            $site_settings = [];
            foreach ($settings_raw as $setting) {
                $site_settings[$setting['setting_key']] = $setting['setting_value'];
            }
        } catch (PDOException $e) {
            // Handle error - log it or return empty array
            error_log("Error fetching settings: " . $e->getMessage());
            return [];
        }
    }
    return $site_settings;
}

function get_setting($key, $default = null) {
    global $pdo; // Assumes $pdo is available globally after including db.php
    $settings = get_all_settings($pdo);
    return isset($settings[$key]) ? $settings[$key] : $default;
}

// --- Categories ---
function get_featured_categories($pdo, $limit = 8) {
    try {
        $sql = "SELECT id, name, slug, icon, image, (SELECT COUNT(*) FROM businesses b JOIN business_categories bc ON b.id = bc.business_id WHERE bc.category_id = c.id AND b.status = 'active') as business_count
                FROM categories c
                WHERE status = 'active' AND featured = 1
                ORDER BY sort_order ASC, name ASC
                LIMIT :limit";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching featured categories: " . $e->getMessage());
        return [];
    }
}

// --- Businesses ---
function get_featured_businesses($pdo, $limit = 6) {
    try {
        // Fetch businesses, join with categories (optional), get review count/avg rating
        // This query can become complex; adjust as needed.
        $sql = "SELECT
                    b.id, b.name, b.slug, b.short_description, b.logo, b.cover_image, b.city, b.state,
                    (SELECT AVG(rating) FROM reviews r WHERE r.business_id = b.id AND r.status = 'approved') as avg_rating,
                    (SELECT COUNT(*) FROM reviews r WHERE r.business_id = b.id AND r.status = 'approved') as review_count,
                    GROUP_CONCAT(c.name SEPARATOR ', ') as category_names -- Get category names
                FROM businesses b
                LEFT JOIN business_categories bc ON b.id = bc.business_id
                LEFT JOIN categories c ON bc.category_id = c.id
                WHERE b.status = 'active' AND b.is_featured = 1 AND b.is_verified = 1
                GROUP BY b.id -- Group by business to avoid duplicate rows due to multiple categories
                ORDER BY b.updated_at DESC -- Or other criteria like rating
                LIMIT :limit";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching featured businesses: " . $e->getMessage());
        return [];
    }
}

// --- Cities ---
function get_popular_cities($pdo, $limit = 12) {
    try {
        $sql = "SELECT id, name, state, image,
                       (SELECT COUNT(*) FROM businesses b WHERE b.city = c.name AND b.state = c.state AND b.status = 'active') as business_count
                FROM cities c
                WHERE status = 'active' AND is_featured = 1
                ORDER BY name ASC
                LIMIT :limit";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching popular cities: " . $e->getMessage());
        return [];
    }
}

// Helper function for image paths (adjust base path as needed)
function get_image_url($type, $filename, $placeholder = 'https://via.placeholder.com/150') {
    $base_path = '../justdial-admin/uploads/'; // Relative path from justdial-public
    $folder = '';

    switch ($type) {
        case 'categories':
            $folder = 'categories/';
            break;
        case 'businesses': // For logos or covers
            $folder = 'businesses/';
             $placeholder = 'https://via.placeholder.com/600x400'; // Different default
            break;
         case 'business_logo': // For logos or covers
            $folder = 'businesses/';
             $placeholder = 'https://via.placeholder.com/100x100'; // Different default
            break;
        case 'users':
            $folder = 'users/';
             $placeholder = 'https://via.placeholder.com/100x100'; // Different default
            break;
        // Add other types if needed
    }

    $full_path = $base_path . $folder . $filename;
    $absolute_server_path = realpath(__DIR__ . '/../../') . '/justdial-admin/uploads/' . $folder . $filename; // Check actual file existence


    if (!empty($filename) && file_exists($absolute_server_path)) {
         return $full_path;
    } else {
        // You might want different placeholders for different types
         return $placeholder;
    }
}

// Helper function to safely echo output (prevents XSS)
function safe_echo($string) {
    echo htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Helper to generate Font Awesome icon (if using class names like 'fa-utensils')
function render_icon($icon_class, $default_icon = 'fa-building') {
    if (!empty($icon_class)) {
        // Basic check if it looks like a Font Awesome class
        if (strpos($icon_class, 'fa-') === 0 || strpos($icon_class, 'fas ') === 0 || strpos($icon_class, 'fab ') === 0 || strpos($icon_class, 'far ') === 0) {
             return "fas " . preg_replace('/^(fas|fab|far)\s+/', '', $icon_class); // Ensure 'fas' prefix if missing
        } else {
             // Assume it's just the icon name without prefix
             return "fas fa-" . $icon_class;
        }
    }
    return "fas " . $default_icon;
}




function get_category_by_slug($pdo, $slug) {
    try {
        $sql = "SELECT id, name, slug, description, icon, image
                FROM categories
                WHERE slug = :slug AND status = 'active'
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(); // Fetch single category
    } catch (PDOException $e) {
        error_log("Error fetching category by slug '$slug': " . $e->getMessage());
        return null; // Return null on error or not found
    }
}

// --- Businesses ---
// ... (get_featured_businesses function remains the same) ...

function get_businesses_by_category($pdo, $category_id, $page = 1, $per_page = 9) {
    $offset = ($page - 1) * $per_page;
    try {
        $sql = "SELECT
                    b.id, b.name, b.slug, b.short_description, b.logo, b.cover_image, b.city, b.state,
                    (SELECT AVG(rating) FROM reviews r WHERE r.business_id = b.id AND r.status = 'approved') as avg_rating,
                    (SELECT COUNT(*) FROM reviews r WHERE r.business_id = b.id AND r.status = 'approved') as review_count,
                    GROUP_CONCAT(c.name SEPARATOR ', ') as category_names
                FROM businesses b
                JOIN business_categories bc ON b.id = bc.business_id
                LEFT JOIN categories c ON bc.category_id = c.id -- Join again for category names if needed
                WHERE b.status = 'active' AND bc.category_id = :category_id
                GROUP BY b.id
                ORDER BY b.is_featured DESC, b.updated_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching businesses by category ID $category_id: " . $e->getMessage());
        return [];
    }
}

function get_business_count_for_category($pdo, $category_id) {
    try {
        $sql = "SELECT COUNT(DISTINCT b.id)
                FROM businesses b
                JOIN business_categories bc ON b.id = bc.business_id
                WHERE b.status = 'active' AND bc.category_id = :category_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Error counting businesses for category ID $category_id: " . $e->getMessage());
        return 0;
    }
}

function get_all_businesses($pdo, $page = 1, $per_page = 9) {
     $offset = ($page - 1) * $per_page;
    try {
        $sql = "SELECT
                    b.id, b.name, b.slug, b.short_description, b.logo, b.cover_image, b.city, b.state,
                    (SELECT AVG(rating) FROM reviews r WHERE r.business_id = b.id AND r.status = 'approved') as avg_rating,
                    (SELECT COUNT(*) FROM reviews r WHERE r.business_id = b.id AND r.status = 'approved') as review_count,
                    GROUP_CONCAT(c.name SEPARATOR ', ') as category_names
                FROM businesses b
                LEFT JOIN business_categories bc ON b.id = bc.business_id
                LEFT JOIN categories c ON bc.category_id = c.id
                WHERE b.status = 'active'
                GROUP BY b.id
                ORDER BY b.is_featured DESC, b.updated_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching all businesses: " . $e->getMessage());
        return [];
    }
}

function get_total_business_count($pdo) {
    try {
        $sql = "SELECT COUNT(*) FROM businesses WHERE status = 'active'";
        $stmt = $pdo->query($sql);
        return (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Error counting total businesses: " . $e->getMessage());
        return 0;
    }
}


// --- Cities ---
// ... (get_popular_cities function remains the same) ...

function get_city_details($pdo, $name, $state) {
     try {
        $sql = "SELECT id, name, state, country, image
                FROM cities
                WHERE name = :name AND state = :state AND status = 'active'
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':state', $state, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching city details for $name, $state: " . $e->getMessage());
        return null;
    }
}

function get_businesses_by_city($pdo, $city_name, $city_state, $page = 1, $per_page = 9) {
    $offset = ($page - 1) * $per_page;
    try {
         $sql = "SELECT
                    b.id, b.name, b.slug, b.short_description, b.logo, b.cover_image, b.city, b.state,
                    (SELECT AVG(rating) FROM reviews r WHERE r.business_id = b.id AND r.status = 'approved') as avg_rating,
                    (SELECT COUNT(*) FROM reviews r WHERE r.business_id = b.id AND r.status = 'approved') as review_count,
                    GROUP_CONCAT(c.name SEPARATOR ', ') as category_names
                FROM businesses b
                LEFT JOIN business_categories bc ON b.id = bc.business_id
                LEFT JOIN categories c ON bc.category_id = c.id
                WHERE b.status = 'active' AND b.city = :city_name AND b.state = :city_state
                GROUP BY b.id
                ORDER BY b.is_featured DESC, b.updated_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':city_name', $city_name, PDO::PARAM_STR);
        $stmt->bindParam(':city_state', $city_state, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching businesses for city $city_name, $city_state: " . $e->getMessage());
        return [];
    }
}

function get_business_count_for_city($pdo, $city_name, $city_state) {
     try {
        $sql = "SELECT COUNT(*)
                FROM businesses
                WHERE status = 'active' AND city = :city_name AND state = :city_state";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':city_name', $city_name, PDO::PARAM_STR);
        $stmt->bindParam(':city_state', $city_state, PDO::PARAM_STR);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Error counting businesses for city $city_name, $city_state: " . $e->getMessage());
        return 0;
    }
}

// --- Search ---
function search_businesses($pdo, $query, $location, $page = 1, $per_page = 9) {
    $offset = ($page - 1) * $per_page;
    $query_param = !empty($query) ? '%' . $query . '%' : '%';
    $location_param = !empty($location) ? '%' . $location . '%' : '%';

    try {
        // Base query
        $sql = "SELECT DISTINCT -- Use DISTINCT to avoid duplicates if joining multiple tables like categories
                    b.id, b.name, b.slug, b.short_description, b.logo, b.cover_image, b.city, b.state,
                    (SELECT AVG(rating) FROM reviews r WHERE r.business_id = b.id AND r.status = 'approved') as avg_rating,
                    (SELECT COUNT(*) FROM reviews r WHERE r.business_id = b.id AND r.status = 'approved') as review_count,
                    GROUP_CONCAT(c.name SEPARATOR ', ') as category_names
                FROM businesses b
                LEFT JOIN business_categories bc ON b.id = bc.business_id
                LEFT JOIN categories c ON bc.category_id = c.id
                LEFT JOIN services s ON s.business_id = b.id -- Join services if searching service names too
                WHERE b.status = 'active'";

        $params = [];

        // Add search conditions
        if (!empty($query)) {
            $sql .= " AND (b.name LIKE :query OR b.description LIKE :query OR b.short_description LIKE :query OR c.name LIKE :query OR s.name LIKE :query)";
            $params[':query'] = $query_param;
        }

        if (!empty($location)) {
            $sql .= " AND (b.address LIKE :location OR b.city LIKE :location OR b.state LIKE :location OR b.postal_code LIKE :location)";
             $params[':location'] = $location_param;
        }

        $sql .= " GROUP BY b.id -- Group after WHERE, before ORDER BY
                  ORDER BY b.is_featured DESC, b.name ASC -- Adjust sorting as needed
                  LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        // Bind dynamic params
        foreach ($params as $key => &$val) {
             $stmt->bindParam($key, $val); // Use bindParam with reference for LIKE params
        }
        // Or bind directly if not using reference above
        // foreach ($params as $key => $val) {
        //     $stmt->bindValue($key, $val);
        // }

        $stmt->execute();
        return $stmt->fetchAll();

    } catch (PDOException $e) {
        error_log("Error searching businesses (query: $query, location: $location): " . $e->getMessage());
        return [];
    }
}

function get_search_results_count($pdo, $query, $location) {
    $query_param = !empty($query) ? '%' . $query . '%' : '%';
    $location_param = !empty($location) ? '%' . $location . '%' : '%';

     try {
        // Base query - Count distinct business IDs
        $sql = "SELECT COUNT(DISTINCT b.id)
                FROM businesses b
                LEFT JOIN business_categories bc ON b.id = bc.business_id
                LEFT JOIN categories c ON bc.category_id = c.id
                LEFT JOIN services s ON s.business_id = b.id
                WHERE b.status = 'active'";

        $params = [];

        // Add search conditions (must match the search_businesses function)
        if (!empty($query)) {
            $sql .= " AND (b.name LIKE :query OR b.description LIKE :query OR b.short_description LIKE :query OR c.name LIKE :query OR s.name LIKE :query)";
            $params[':query'] = $query_param;
        }

        if (!empty($location)) {
            $sql .= " AND (b.address LIKE :location OR b.city LIKE :location OR b.state LIKE :location OR b.postal_code LIKE :location)";
             $params[':location'] = $location_param;
        }

        $stmt = $pdo->prepare($sql);

         // Bind dynamic params
        foreach ($params as $key => &$val) {
             $stmt->bindParam($key, $val);
        }
        // Or bind directly:
        // foreach ($params as $key => $val) {
        //     $stmt->bindValue($key, $val);
        // }

        $stmt->execute();
        return (int) $stmt->fetchColumn();

    } catch (PDOException $e) {
        error_log("Error counting search results (query: $query, location: $location): " . $e->getMessage());
        return 0;
    }
}


// --- Helper Functions ---
// ... (get_image_url, safe_echo, render_icon functions remain the same) ...

// --- Pagination Helper ---
function render_pagination($current_page, $total_items, $per_page, $base_url) {
    $total_pages = ceil($total_items / $per_page);

    if ($total_pages <= 1) {
        return ''; // No pagination needed
    }

    $output = '<nav aria-label="Page navigation" class="mt-8 flex justify-center">';
    $output .= '<ul class="inline-flex items-center -space-x-px">';

    // Previous Button
    if ($current_page > 1) {
        $prev_page = $current_page - 1;
        $output .= '<li><a href="' . $base_url . '&page=' . $prev_page . '" class="py-2 px-3 ml-0 leading-tight text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700"><span class="sr-only">Previous</span><i class="fas fa-chevron-left"></i></a></li>';
    } else {
        $output .= '<li><span class="py-2 px-3 ml-0 leading-tight text-gray-300 bg-white rounded-l-lg border border-gray-300 cursor-not-allowed"><span class="sr-only">Previous</span><i class="fas fa-chevron-left"></i></span></li>';
    }

    // Page Numbers (simplified version: show current and neighbors)
    $start_page = max(1, $current_page - 2);
    $end_page = min($total_pages, $current_page + 2);

    if ($start_page > 1) {
         $output .= '<li><a href="' . $base_url . '&page=1" class="py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">1</a></li>';
        if ($start_page > 2) {
             $output .= '<li><span class="py-2 px-3 leading-tight text-gray-400 bg-white border border-gray-300">...</span></li>';
        }
    }

    for ($i = $start_page; $i <= $end_page; $i++) {
        if ($i == $current_page) {
            $output .= '<li><span aria-current="page" class="z-10 py-2 px-3 leading-tight text-primary-600 bg-primary-50 border border-primary-300 hover:bg-primary-100 hover:text-primary-700">' . $i . '</span></li>';
        } else {
            $output .= '<li><a href="' . $base_url . '&page=' . $i . '" class="py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">' . $i . '</a></li>';
        }
    }

     if ($end_page < $total_pages) {
         if ($end_page < $total_pages - 1) {
              $output .= '<li><span class="py-2 px-3 leading-tight text-gray-400 bg-white border border-gray-300">...</span></li>';
         }
         $output .= '<li><a href="' . $base_url . '&page=' . $total_pages . '" class="py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">' . $total_pages . '</a></li>';
    }


    // Next Button
    if ($current_page < $total_pages) {
        $next_page = $current_page + 1;
        $output .= '<li><a href="' . $base_url . '&page=' . $next_page . '" class="py-2 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700"><span class="sr-only">Next</span><i class="fas fa-chevron-right"></i></a></li>';
    } else {
        $output .= '<li><span class="py-2 px-3 leading-tight text-gray-300 bg-white rounded-r-lg border border-gray-300 cursor-not-allowed"><span class="sr-only">Next</span><i class="fas fa-chevron-right"></i></span></li>';
    }

    $output .= '</ul>';
    $output .= '</nav>';

    return $output;
}

?>