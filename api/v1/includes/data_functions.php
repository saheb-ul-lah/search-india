<?php
// api/v1/includes/data_functions.php
// Contains functions to fetch data specifically for the API

// --- API Image URL Helper ---
// Assuming API is at domain.com/api/v1/ and uploads are at domain.com/justdial-admin/uploads/
// Adjust BASE_URL based on your final hosting setup. Maybe make it a config constant.
define('API_BASE_ASSET_URL', '/justdial-admin/uploads/'); // Path relative to domain root

function get_api_image_url($type, $filename, $placeholder_dimensions = '150x150') {
    if (empty($filename)) {
        return "https://via.placeholder.com/" . $placeholder_dimensions;
    }

    $folder = '';
    switch ($type) {
        case 'categories': $folder = 'categories/'; break;
        case 'businesses': $folder = 'businesses/'; $placeholder_dimensions = '600x400'; break;
        case 'business_logo': $folder = 'businesses/'; $placeholder_dimensions = '100x100'; break;
        case 'users': $folder = 'users/'; $placeholder_dimensions = '100x100'; break;
        // Add other types if needed
    }

    // Construct absolute URL (adjust logic if BASE_URL is different)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST']; // Assumes API is on the same domain

    return $protocol . $domainName . API_BASE_ASSET_URL . $folder . $filename;
}

// --- Businesses ---

function api_get_businesses(PDO $pdo, array $params): array {
    $pagination = get_pagination_params(); // Use helper
    $limit = $pagination['limit'];
    $offset = $pagination['offset'];

    // Search/Filter Params (Example)
    $search_query = isset($params['q']) ? trim($params['q']) : '';
    $location = isset($params['location']) ? trim($params['location']) : '';
    $category_slug_or_id = isset($params['category']) ? trim($params['category']) : '';
    $city_name = isset($params['city']) ? trim($params['city']) : '';
    $state_name = isset($params['state']) ? trim($params['state']) : '';

    $sql = "SELECT SQL_CALC_FOUND_ROWS -- Get total count efficiently
                b.id, b.name, b.slug, b.short_description, b.logo, b.cover_image,
                b.address, b.city, b.state, b.country, b.postal_code,
                b.phone, b.email, b.website, b.latitude, b.longitude,
                b.status, b.is_verified, b.is_featured, b.views,
                (SELECT AVG(rating) FROM reviews r WHERE r.business_id = b.id AND r.status = 'approved') as avg_rating,
                (SELECT COUNT(*) FROM reviews r WHERE r.business_id = b.id AND r.status = 'approved') as review_count
            FROM businesses b ";
    $where_clauses = ["b.status = 'active'"];
    $sql_params = [];

    // Join categories only if needed for filtering or display (less efficient)
    $needs_category_join = !empty($category_slug_or_id) || !empty($search_query); // Join if filtering by category or searching category name
    if ($needs_category_join) {
        $sql .= " LEFT JOIN business_categories bc ON b.id = bc.business_id
                  LEFT JOIN categories c ON bc.category_id = c.id ";
    }
    // Join services only if needed for search
    $needs_service_join = !empty($search_query);
     if ($needs_service_join) {
         $sql .= " LEFT JOIN services s ON s.business_id = b.id ";
     }


    // Filtering
    if (!empty($search_query)) {
         $where_clauses[] = "(b.name LIKE :query OR b.description LIKE :query OR b.short_description LIKE :query "
                           . ($needs_category_join ? "OR c.name LIKE :query " : "")
                           . ($needs_service_join ? "OR s.name LIKE :query " : "")
                           . ")";
        $sql_params[':query'] = '%' . $search_query . '%';
    }
    if (!empty($location)) {
         $where_clauses[] = "(b.address LIKE :location OR b.city LIKE :location OR b.state LIKE :location OR b.postal_code LIKE :location)";
         $sql_params[':location'] = '%' . $location . '%';
    }
     if (!empty($city_name)) {
         $where_clauses[] = "b.city = :city_name";
         $sql_params[':city_name'] = $city_name;
     }
     if (!empty($state_name)) {
         $where_clauses[] = "b.state = :state_name";
         $sql_params[':state_name'] = $state_name;
     }
    if (!empty($category_slug_or_id)) {
        // Check if it's numeric (ID) or string (slug)
        if (is_numeric($category_slug_or_id)) {
            $where_clauses[] = "bc.category_id = :category_id";
            $sql_params[':category_id'] = (int)$category_slug_or_id;
        } else {
            $where_clauses[] = "c.slug = :category_slug";
            $sql_params[':category_slug'] = $category_slug_or_id;
        }
    }

    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }

    // Use GROUP BY only if joins might cause duplicates
     if ($needs_category_join || $needs_service_join) {
         $sql .= " GROUP BY b.id ";
     }


    $sql .= " ORDER BY b.is_featured DESC, b.name ASC LIMIT :limit OFFSET :offset";
    $sql_params[':limit'] = $limit;
    $sql_params[':offset'] = $offset;

    try {
        $stmt = $pdo->prepare($sql);
        // Bind parameters by value (safer for mixed types and LIKE)
        foreach ($sql_params as $key => $val) {
             if ($key == ':limit' || $key == ':offset' || $key == ':category_id') {
                 $stmt->bindValue($key, $val, PDO::PARAM_INT);
             } else {
                 $stmt->bindValue($key, $val, PDO::PARAM_STR);
             }
        }

        $stmt->execute();
        $businesses = $stmt->fetchAll();

        // Get total count found by the query (ignoring LIMIT)
        $total_items_stmt = $pdo->query("SELECT FOUND_ROWS()");
        $total_items = (int)$total_items_stmt->fetchColumn();

        // Add full image URLs
        foreach ($businesses as &$business) {
            $business['logo_url'] = $business['logo'] ? get_api_image_url('business_logo', $business['logo']) : null;
            $business['cover_image_url'] = $business['cover_image'] ? get_api_image_url('businesses', $business['cover_image']) : null;
            // Cast numeric types if needed
            $business['id'] = (int)$business['id'];
            $business['avg_rating'] = $business['avg_rating'] ? (float)$business['avg_rating'] : null;
            $business['review_count'] = (int)$business['review_count'];
            $business['views'] = (int)$business['views'];
            $business['is_verified'] = (bool)$business['is_verified'];
            $business['is_featured'] = (bool)$business['is_featured'];
            // Remove original filenames if desired
            // unset($business['logo'], $business['cover_image']);
        }

        return [
            'businesses' => $businesses,
            'pagination' => build_pagination_meta($pagination['page'], $limit, $total_items)
        ];

    } catch (\PDOException $e) {
        error_log("API Error fetching businesses: " . $e->getMessage() . " SQL: " . $sql);
        send_error_response(500, "Error fetching business data.");
        return ['businesses' => [], 'pagination' => build_pagination_meta($pagination['page'], $limit, 0)]; // Should not reach here
    }
}


function api_get_business_by_id(PDO $pdo, int $id): ?array {
     try {
        // Fetch more details than the list view
        $sql = "SELECT
                    b.*, -- Select all columns from businesses
                    (SELECT AVG(rating) FROM reviews r WHERE r.business_id = b.id AND r.status = 'approved') as avg_rating,
                    (SELECT COUNT(*) FROM reviews r WHERE r.business_id = b.id AND r.status = 'approved') as review_count
                FROM businesses b
                WHERE b.id = :id AND b.status = 'active'
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $business = $stmt->fetch();

        if ($business) {
            $business['logo_url'] = $business['logo'] ? get_api_image_url('business_logo', $business['logo']) : null;
            $business['cover_image_url'] = $business['cover_image'] ? get_api_image_url('businesses', $business['cover_image']) : null;
            $business['id'] = (int)$business['id'];
            $business['avg_rating'] = $business['avg_rating'] ? (float)$business['avg_rating'] : null;
            $business['review_count'] = (int)$business['review_count'];
            $business['views'] = (int)$business['views'];
            $business['is_verified'] = (bool)$business['is_verified'];
            $business['is_featured'] = (bool)$business['is_featured'];
            // Optionally fetch related data like categories, services, reviews, hours, images
            $business['categories'] = api_get_business_categories($pdo, $id);
            $business['services'] = api_get_business_services($pdo, $id);
            // $business['reviews'] = api_get_business_reviews($pdo, $id); // Add pagination
            // $business['images'] = api_get_business_images($pdo, $id);
            // $business['hours'] = api_get_business_hours($pdo, $id);
        }

        return $business ?: null;

    } catch (\PDOException $e) {
        error_log("API Error fetching business ID $id: " . $e->getMessage());
        send_error_response(500, "Error fetching business details.");
         return null; // Should not reach here
    }
}

// Helper function for related business data (add more as needed)
function api_get_business_categories(PDO $pdo, int $business_id): array {
    try {
        $stmt = $pdo->prepare("SELECT c.id, c.name, c.slug, c.icon FROM categories c JOIN business_categories bc ON c.id = bc.category_id WHERE bc.business_id = :id AND c.status = 'active'");
        $stmt->bindParam(':id', $business_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (\PDOException $e) {
        error_log("API Error fetching categories for business ID $business_id: " . $e->getMessage());
        return []; // Return empty array on error
    }
}

function api_get_business_services(PDO $pdo, int $business_id): array {
     try {
        $stmt = $pdo->prepare("SELECT id, name, description, price, price_type, duration FROM services WHERE business_id = :id AND status = 'active'");
        $stmt->bindParam(':id', $business_id, PDO::PARAM_INT);
        $stmt->execute();
        $services = $stmt->fetchAll();
         foreach ($services as &$service) {
             $service['id'] = (int)$service['id'];
             $service['price'] = $service['price'] ? (float)$service['price'] : null;
         }
        return $services;
    } catch (\PDOException $e) {
        error_log("API Error fetching services for business ID $business_id: " . $e->getMessage());
        return []; // Return empty array on error
    }
}

// --- Categories ---

function api_get_categories(PDO $pdo, array $params): array {
    $pagination = get_pagination_params(); // Use helper
    $limit = $pagination['limit'];
    $offset = $pagination['offset'];

    try {
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    c.id, c.name, c.slug, c.description, c.icon, c.image, c.parent_id, c.featured,
                    (SELECT COUNT(*) FROM businesses b JOIN business_categories bc ON b.id = bc.business_id WHERE bc.category_id = c.id AND b.status = 'active') as business_count
                FROM categories c
                WHERE c.status = 'active'";
        // Add filtering if needed (e.g., parent_id)
        if (isset($params['parent_id'])) {
            if ($params['parent_id'] === 'null') {
                 $sql .= " AND c.parent_id IS NULL ";
            } else {
                 $sql .= " AND c.parent_id = :parent_id ";
                 $sql_params[':parent_id'] = (int)$params['parent_id'];
            }
        }
        if (isset($params['featured'])) {
             $sql .= " AND c.featured = :featured ";
             $sql_params[':featured'] = ($params['featured'] == '1' || $params['featured'] === true) ? 1 : 0;
        }

        $sql .= " ORDER BY c.sort_order ASC, c.name ASC LIMIT :limit OFFSET :offset";
        $sql_params[':limit'] = $limit;
        $sql_params[':offset'] = $offset;

        $stmt = $pdo->prepare($sql);
         foreach ($sql_params as $key => $val) {
             if ($key == ':limit' || $key == ':offset' || $key == ':parent_id' || $key == ':featured') {
                 $stmt->bindValue($key, $val, PDO::PARAM_INT);
             } else {
                 $stmt->bindValue($key, $val, PDO::PARAM_STR);
             }
        }
        $stmt->execute();
        $categories = $stmt->fetchAll();

        $total_items_stmt = $pdo->query("SELECT FOUND_ROWS()");
        $total_items = (int)$total_items_stmt->fetchColumn();

        foreach ($categories as &$category) {
            $category['image_url'] = $category['image'] ? get_api_image_url('categories', $category['image']) : null;
            $category['id'] = (int)$category['id'];
            $category['parent_id'] = $category['parent_id'] ? (int)$category['parent_id'] : null;
            $category['featured'] = (bool)$category['featured'];
            $category['business_count'] = (int)$category['business_count'];
            // unset($category['image']);
        }

         return [
            'categories' => $categories,
            'pagination' => build_pagination_meta($pagination['page'], $limit, $total_items)
        ];

    } catch (\PDOException $e) {
        error_log("API Error fetching categories: " . $e->getMessage());
        send_error_response(500, "Error fetching category data.");
        return ['categories' => [], 'pagination' => build_pagination_meta($pagination['page'], $limit, 0)];
    }
}

function api_get_category_by_id_or_slug(PDO $pdo, $id_or_slug): ?array {
     try {
         $sql = "SELECT id, name, slug, description, icon, image, parent_id, featured
                FROM categories
                WHERE status = 'active' AND ";
        if (is_numeric($id_or_slug)) {
            $sql .= " id = :identifier ";
            $param_type = PDO::PARAM_INT;
        } else {
            $sql .= " slug = :identifier ";
            $param_type = PDO::PARAM_STR;
        }
        $sql .= " LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':identifier', $id_or_slug, $param_type);
        $stmt->execute();
        $category = $stmt->fetch();

        if ($category) {
            $category['image_url'] = $category['image'] ? get_api_image_url('categories', $category['image']) : null;
            $category['id'] = (int)$category['id'];
            $category['parent_id'] = $category['parent_id'] ? (int)$category['parent_id'] : null;
            $category['featured'] = (bool)$category['featured'];
            // Optionally add business count or children categories
        }

        return $category ?: null;

    } catch (\PDOException $e) {
        error_log("API Error fetching category by identifier '$id_or_slug': " . $e->getMessage());
        send_error_response(500, "Error fetching category details.");
        return null;
    }
}


// --- Cities ---

function api_get_cities(PDO $pdo, array $params): array {
    $pagination = get_pagination_params();
    $limit = $pagination['limit'];
    $offset = $pagination['offset'];

    try {
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    c.id, c.name, c.state, c.country, c.image, c.is_featured,
                    (SELECT COUNT(*) FROM businesses b WHERE b.city = c.name AND b.state = c.state AND b.status = 'active') as business_count
                FROM cities c
                WHERE c.status = 'active'";

        // Add filtering if needed
        if (isset($params['featured'])) {
             $sql .= " AND c.is_featured = :featured ";
             $sql_params[':featured'] = ($params['featured'] == '1' || $params['featured'] === true) ? 1 : 0;
        }
        if (isset($params['state'])) {
             $sql .= " AND c.state = :state ";
             $sql_params[':state'] = $params['state'];
        }

        $sql .= " ORDER BY c.name ASC LIMIT :limit OFFSET :offset";
        $sql_params[':limit'] = $limit;
        $sql_params[':offset'] = $offset;

        $stmt = $pdo->prepare($sql);
        foreach ($sql_params as $key => $val) {
             if ($key == ':limit' || $key == ':offset' || $key == ':featured') {
                 $stmt->bindValue($key, $val, PDO::PARAM_INT);
             } else {
                 $stmt->bindValue($key, $val, PDO::PARAM_STR);
             }
        }
        $stmt->execute();
        $cities = $stmt->fetchAll();

        $total_items_stmt = $pdo->query("SELECT FOUND_ROWS()");
        $total_items = (int)$total_items_stmt->fetchColumn();

        foreach ($cities as &$city) {
            $city['image_url'] = $city['image'] ? get_api_image_url('cities', $city['image']) : null; // Assuming a 'cities' upload folder exists
            $city['id'] = (int)$city['id'];
            $city['is_featured'] = (bool)$city['is_featured'];
            $city['business_count'] = (int)$city['business_count'];
            // unset($city['image']);
        }

        return [
            'cities' => $cities,
            'pagination' => build_pagination_meta($pagination['page'], $limit, $total_items)
        ];

    } catch (\PDOException $e) {
        error_log("API Error fetching cities: " . $e->getMessage());
        send_error_response(500, "Error fetching city data.");
        return ['cities' => [], 'pagination' => build_pagination_meta($pagination['page'], $limit, 0)];
    }
}

// --- Settings ---

function api_get_public_settings(PDO $pdo): array {
    try {
        // Only fetch settings considered safe for public API consumption
        $allowed_keys = ['site_name', 'site_tagline', 'primary_color', 'secondary_color', 'accent_color']; // Add more as needed
        $placeholders = rtrim(str_repeat('?,', count($allowed_keys)), ',');
        $sql = "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($allowed_keys);
        $settings_raw = $stmt->fetchAll();

        $settings = [];
         foreach ($settings_raw as $setting) {
             $settings[$setting['setting_key']] = $setting['setting_value'];
         }
         // Add logo URL separately
         $logo_filename = $settings['site_logo'] ?? null; // Fetch logo filename if allowed
         if ($logo_filename) {
              $settings['logo_url'] = get_api_image_url('logos', $logo_filename); // Assuming 'logos' type
         }


        return $settings;

    } catch (\PDOException $e) {
        error_log("API Error fetching settings: " . $e->getMessage());
        send_error_response(500, "Error fetching settings data.");
        return [];
    }
}


?>