<?php
$pageTitle = 'Add Business';
require_once '../../includes/header.php';

// Check if user has permission
if (!isManager()) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('modules/dashboard/index.php');
}

// Get categories for dropdown
$db = db();
$db->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name");
$categories = $db->resultSet();

// Get users for owner dropdown
$db->query("SELECT id, name, email FROM users WHERE status = 'active' ORDER BY name");
$users = $db->resultSet();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid CSRF token');
        redirect('modules/businesses/add.php');
    }
    
    // Validate input
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $short_description = sanitize($_POST['short_description']);
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $state = sanitize($_POST['state']);
    $country = sanitize($_POST['country']);
    $postal_code = sanitize($_POST['postal_code']);
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    $website = sanitize($_POST['website']);
    $founded_year = !empty($_POST['founded_year']) ? (int)$_POST['founded_year'] : null;
    $owner_id = !empty($_POST['owner_id']) ? (int)$_POST['owner_id'] : null;
    $status = sanitize($_POST['status']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_verified = isset($_POST['is_verified']) ? 1 : 0;
    $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
    $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;
    $selectedCategories = isset($_POST['categories']) ? $_POST['categories'] : [];
    
    // Generate slug
    $slug = generateSlug($name);
    
    // Check if slug already exists
    $db->query("SELECT id FROM businesses WHERE slug = :slug");
    $db->bind(':slug', $slug);
    $existingBusiness = $db->single();
    
    if ($existingBusiness) {
        $slug = $slug . '-' . time();
    }
    
    // Upload logo if provided
    $logo = null;
    if (!empty($_FILES['logo']['name'])) {
        $uploadResult = uploadFile($_FILES['logo'], 'businesses', ['jpg', 'jpeg', 'png', 'gif']);
        if ($uploadResult['success']) {
            $logo = $uploadResult['filename'];
        } else {
            setFlashMessage('error', 'Logo upload failed: ' . $uploadResult['message']);
            redirect('modules/businesses/add.php');
        }
    }
    
    // Upload cover image if provided
    $cover_image = null;
    if (!empty($_FILES['cover_image']['name'])) {
        $uploadResult = uploadFile($_FILES['cover_image'], 'businesses', ['jpg', 'jpeg', 'png', 'gif']);
        if ($uploadResult['success']) {
            $cover_image = $uploadResult['filename'];
        } else {
            setFlashMessage('error', 'Cover image upload failed: ' . $uploadResult['message']);
            redirect('modules/businesses/add.php');
        }
    }
    
    try {
        // Begin transaction
        $db->beginTransaction();
        
        // Insert business
        $db->query("INSERT INTO businesses (name, slug, description, short_description, logo, cover_image, address, city, state, country, postal_code, latitude, longitude, phone, email, website, founded_year, owner_id, status, is_featured, is_verified, created_at) 
                    VALUES (:name, :slug, :description, :short_description, :logo, :cover_image, :address, :city, :state, :country, :postal_code, :latitude, :longitude, :phone, :email, :website, :founded_year, :owner_id, :status, :is_featured, :is_verified, NOW())");
        
        $db->bind(':name', $name);
        $db->bind(':slug', $slug);
        $db->bind(':description', $description);
        $db->bind(':short_description', $short_description);
        $db->bind(':logo', $logo);
        $db->bind(':cover_image', $cover_image);
        $db->bind(':address', $address);
        $db->bind(':city', $city);
        $db->bind(':state', $state);
        $db->bind(':country', $country);
        $db->bind(':postal_code', $postal_code);
        $db->bind(':latitude', $latitude);
        $db->bind(':longitude', $longitude);
        $db->bind(':phone', $phone);
        $db->bind(':email', $email);
        $db->bind(':website', $website);
        $db->bind(':founded_year', $founded_year);
        $db->bind(':owner_id', $owner_id);
        $db->bind(':status', $status);
        $db->bind(':is_featured', $is_featured);
        $db->bind(':is_verified', $is_verified);
        
        $db->execute();
        $businessId = $db->lastInsertId();
        
        // Insert business categories
        if (!empty($selectedCategories)) {
            foreach ($selectedCategories as $categoryId) {
                $db->query("INSERT INTO business_categories (business_id, category_id) VALUES (:business_id, :category_id)");
                $db->bind(':business_id', $businessId);
                $db->bind(':category_id', $categoryId);
                $db->execute();
            }
        }
        
        // Commit transaction
        $db->endTransaction();
        
        // Log activity
        logActivity($_SESSION['user_id'], 'create', 'businesses', $businessId, 'Created business: ' . $name);
        
        setFlashMessage('success', 'Business added successfully');
        redirect('modules/businesses/index.php');
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $db->cancelTransaction();
        setFlashMessage('error', 'Error adding business: ' . $e->getMessage());
        redirect('modules/businesses/add.php');
    }
}
?>

<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center my-6">
        <h2 class="text-2xl font-semibold text-gray-800">Add Business</h2>
        <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300">
            <i class="fa-solid fa-arrow-left mr-2"></i> Back to Businesses
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <form method="POST" enctype="multipart/form-data" class="p-6">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                </div>
                
                <div class="col-span-1">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Business Name <span class="text-red-600">*</span></label>
                    <input type="text" id="name" name="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>
                
                <div class="col-span-1">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-600">*</span></label>
                    <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                        <option value="pending">Pending</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                
                <div class="col-span-1">
                    <label for="short_description" class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
                    <input type="text" id="short_description" name="short_description" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" maxlength="255">
                </div>
                
                <div class="col-span-1">
                    <label for="owner_id" class="block text-sm font-medium text-gray-700 mb-1">Owner</label>
                    <select id="owner_id" name="owner_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="">Select Owner</option>
                        <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id'] ?>"><?= $user['name'] ?> (<?= $user['email'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"></textarea>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categories</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php foreach ($categories as $category): ?>
                        <div class="flex items-center">
                            <input id="category-<?= $category['id'] ?>" name="categories[]" type="checkbox" value="<?= $category['id'] ?>" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            <label for="category-<?= $category['id'] ?>" class="ml-2 text-sm font-medium text-gray-900"><?= $category['name'] ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
                </div>
                
                <div class="col-span-1">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" id="phone" name="phone" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                
                <div class="col-span-1">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                
                <div class="col-span-1">
                    <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                    <input type="url" id="website" name="website" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="https://">
                </div>
                
                <div class="col-span-1">
                    <label for="founded_year" class="block text-sm font-medium text-gray-700 mb-1">Founded Year</label>
                    <input type="number" id="founded_year" name="founded_year" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" min="1900" max="<?= date('Y') ?>">
                </div>
                
                <!-- Location Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Location Information</h3>
                </div>
                
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" id="address" name="address" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                
                <div class="col-span-1">
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City <span class="text-red-600">*</span></label>
                    <input type="text" id="city" name="city" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>
                
                <div class="col-span-1">
                    <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State <span class="text-red-600">*</span></label>
                    <input type="text" id="state" name="state" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>
                
                <div class="col-span-1">
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                    <input type="text" id="country" name="country" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" value="India">
                </div>
                
                <div class="col-span-1">
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                
                <div class="col-span-1">
                    <label for="latitude" class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                    <input type="text" id="latitude" name="latitude" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                
                <div class="col-span-1">
                    <label for="longitude" class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                    <input type="text" id="longitude" name="longitude" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                
                <!-- Media -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Media</h3>
                </div>
                
                <div class="col-span-1">
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                    <input type="file" id="logo" name="logo" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" accept="image/*">
                    <p class="mt-1 text-sm text-gray-500">Recommended size: 200x200 pixels</p>
                </div>
                
                <div class="col-span-1">
                    <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-1">Cover Image</label>
                    <input type="file" id="cover_image" name="cover_image" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" accept="image/*">
                    <p class="mt-1 text-sm text-gray-500">Recommended size: 1200x400 pixels</p>
                </div>
                
                <!-- Additional Options -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Options</h3>
                </div>
                
                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <input id="is_featured" name="is_featured" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <label for="is_featured" class="ml-2 text-sm font-medium text-gray-900">Featured Business</label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Featured businesses will be displayed prominently on the website</p>
                </div>
                
                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <input id="is_verified" name="is_verified" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <label for="is_verified" class="ml-2 text-sm font-medium text-gray-900">Verified Business</label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Verified businesses will display a verification badge</p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="window.location.href='index.php'" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg mr-2">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
                    Add Business
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>