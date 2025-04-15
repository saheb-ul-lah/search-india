<?php
$pageTitle = 'Add Service';
require_once '../../includes/header.php';

// Check if user has permission
if (!isManager()) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('modules/dashboard/index.php');
}

// Get categories for dropdown
$db = db();
$db->query("SELECT id, name FROM categories ORDER BY name");
$categories = $db->resultSet();

// Get businesses for dropdown
$db->query("SELECT id, name FROM businesses ORDER BY name");
$businesses = $db->resultSet();

// Initialize service data
$service = [
    'name' => '',
    'description' => '',
    'price' => '',
    'price_type' => 'fixed',
    'duration' => '',
    'category_id' => '',
    'business_id' => '',
    'status' => 'active'
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid CSRF token');
        redirect('modules/services/add.php');
    }

    // Validate and sanitize input
    $service['name'] = sanitize($_POST['name']);
    $service['description'] = sanitize($_POST['description']);
    $service['price'] = !empty($_POST['price']) ? (float)$_POST['price'] : null;
    $service['price_type'] = sanitize($_POST['price_type']);
    $service['duration'] = sanitize($_POST['duration']);
    $service['category_id'] = (int)$_POST['category_id'];
    $service['business_id'] = (int)$_POST['business_id'];
    $service['status'] = sanitize($_POST['status']);

    // Validate required fields
    $errors = [];
    if (empty($service['name'])) {
        $errors[] = 'Service name is required';
    }
    
    if (empty($service['category_id'])) {
        $errors[] = 'Category is required';
    }
    
    if (empty($service['business_id'])) {
        $errors[] = 'Business is required';
    }

    // Check if service name already exists for this business
    $db->query("SELECT id FROM services WHERE name = :name AND business_id = :business_id");
    $db->bind(':name', $service['name']);
    $db->bind(':business_id', $service['business_id']);
    $existingService = $db->single();
    
    if ($existingService) {
        $errors[] = 'A service with this name already exists for this business';
    }

    // Upload image if provided
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $uploadResult = uploadFile($_FILES['image'], 'services', ['jpg', 'jpeg', 'png', 'gif']);
        if ($uploadResult['success']) {
            $image = $uploadResult['filename'];
        } else {
            $errors[] = 'Image upload failed: ' . $uploadResult['message'];
        }
    }

    // If no errors, insert the service
    if (empty($errors)) {
        try {
            $db->query("INSERT INTO services (
                name, description, price, price_type, duration, image,
                category_id, business_id, status, created_at, updated_at
            ) VALUES (
                :name, :description, :price, :price_type, :duration, :image,
                :category_id, :business_id, :status, NOW(), NOW()
            )");
            
            $db->bind(':name', $service['name']);
            $db->bind(':description', $service['description']);
            $db->bind(':price', $service['price']);
            $db->bind(':price_type', $service['price_type']);
            $db->bind(':duration', $service['duration']);
            $db->bind(':image', $image);
            $db->bind(':category_id', $service['category_id']);
            $db->bind(':business_id', $service['business_id']);
            $db->bind(':status', $service['status']);
            
            $db->execute();
            $serviceId = $db->lastInsertId();
            
            // Log activity
            logActivity($_SESSION['user_id'], 'create', 'services', $serviceId, 'Created service: ' . $service['name']);
            
            setFlashMessage('success', 'Service added successfully');
            redirect('modules/services/index.php');
            
        } catch (Exception $e) {
            setFlashMessage('error', 'Error adding service: ' . $e->getMessage());
            redirect('modules/services/add.php');
        }
    }
}
?>

<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center my-6">
        <h2 class="text-2xl font-semibold text-gray-800">Add Service</h2>
        <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300">
            <i class="fa-solid fa-arrow-left mr-2"></i> Back to Services
        </a>
    </div>
    
    <!-- Flash Messages -->
    <?php include_once '../../config/functions.php'; ?>
    
    <!-- Error Messages -->
    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Error!</strong>
            <ul class="mt-1 ml-4 list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <form method="POST" enctype="multipart/form-data" class="p-6">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-1">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Service Name <span class="text-red-600">*</span></label>
                    <input type="text" id="name" name="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" value="<?= htmlspecialchars($service['name']) ?>" required>
                </div>
                
                <div class="col-span-1">
                    <label for="business_id" class="block text-sm font-medium text-gray-700 mb-1">Business <span class="text-red-600">*</span></label>
                    <select id="business_id" name="business_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                        <option value="">Select Business</option>
                        <?php foreach ($businesses as $business): ?>
                            <option value="<?= $business['id'] ?>" <?= $service['business_id'] == $business['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($business['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-span-1">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-600">*</span></label>
                    <select id="category_id" name="category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= $service['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-span-1">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-600">*</span></label>
                    <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                        <option value="active" <?= $service['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $service['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                
                <div class="col-span-1">
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-gray-500">₹</span>
                        </div>
                        <input type="number" id="price" name="price" step="0.01" min="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" value="<?= htmlspecialchars($service['price']) ?>" placeholder="0.00">
                    </div>
                </div>
                
                <div class="col-span-1">
                    <label for="price_type" class="block text-sm font-medium text-gray-700 mb-1">Price Type</label>
                    <select id="price_type" name="price_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="fixed" <?= $service['price_type'] === 'fixed' ? 'selected' : '' ?>>Fixed Price</option>
                        <option value="starting_from" <?= $service['price_type'] === 'starting_from' ? 'selected' : '' ?>>Starting From</option>
                        <option value="hourly" <?= $service['price_type'] === 'hourly' ? 'selected' : '' ?>>Hourly Rate</option>
                        <option value="daily" <?= $service['price_type'] === 'daily' ? 'selected' : '' ?>>Daily Rate</option>
                        <option value="custom" <?= $service['price_type'] === 'custom' ? 'selected' : '' ?>>Custom</option>
                    </select>
                </div>
                
                <div class="col-span-1">
                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                    <input type="text" id="duration" name="duration" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" value="<?= htmlspecialchars($service['duration']) ?>" placeholder="e.g. 1 hour, 30 mins">
                </div>
                
                <div class="col-span-1">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                    <input type="file" id="image" name="image" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" accept="image/*">
                    <p class="mt-1 text-xs text-gray-500">Recommended size: 800x600 pixels</p>
                </div>
                
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"><?= htmlspecialchars($service['description']) ?></textarea>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="window.location.href='index.php'" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg mr-2">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
                    Add Service
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>