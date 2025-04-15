<?php
$pageTitle = 'Edit Service';
require_once '../../includes/header.php';

// Check if user has permission
if (!isManager()) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('modules/dashboard/index.php');
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('error', 'Service ID is required');
    redirect('modules/services/index.php');
}

$serviceId = (int)$_GET['id'];

// Get service data
$db = db();
$db->query("SELECT * FROM services WHERE id = :id");
$db->bind(':id', $serviceId);
$service = $db->single();

if (!$service) {
    setFlashMessage('error', 'Service not found');
    redirect('modules/services/index.php');
}

// Get categories and businesses for dropdowns
$db->query("SELECT id, name FROM categories ORDER BY name");
$categories = $db->resultSet();

$db->query("SELECT id, name FROM businesses ORDER BY name");
$businesses = $db->resultSet();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid CSRF token');
        redirect('modules/services/edit.php?id=' . $serviceId);
    }

    // Validate and sanitize input
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $price = !empty($_POST['price']) ? (float)$_POST['price'] : null;
    $priceType = sanitize($_POST['price_type']);
    $duration = sanitize($_POST['duration']);
    $categoryId = (int)$_POST['category_id'];
    $businessId = (int)$_POST['business_id'];
    $status = sanitize($_POST['status']);

    // Validate required fields
    $errors = [];
    if (empty($name)) {
        $errors[] = 'Service name is required';
    }
    
    if (empty($categoryId)) {
        $errors[] = 'Category is required';
    }
    
    if (empty($businessId)) {
        $errors[] = 'Business is required';
    }

    // Check if service name already exists for this business (excluding current service)
    $db->query("SELECT id FROM services WHERE name = :name AND business_id = :business_id AND id != :id");
    $db->bind(':name', $name);
    $db->bind(':business_id', $businessId);
    $db->bind(':id', $serviceId);
    $existingService = $db->single();
    
    if ($existingService) {
        $errors[] = 'A service with this name already exists for this business';
    }

    // Upload image if provided
    $image = $service['image'];
    if (!empty($_FILES['image']['name'])) {
        $uploadResult = uploadFile($_FILES['image'], 'services', ['jpg', 'jpeg', 'png', 'gif']);
        if ($uploadResult['success']) {
            // Delete old image if exists
            if ($image) {
                deleteFile('services/' . $image);
            }
            $image = $uploadResult['filename'];
        } else {
            $errors[] = 'Image upload failed: ' . $uploadResult['message'];
        }
    }

    // If no errors, update the service
    if (empty($errors)) {
        try {
            $db->query("UPDATE services SET 
                        name = :name, 
                        description = :description, 
                        price = :price, 
                        price_type = :price_type, 
                        duration = :duration, 
                        image = :image,
                        category_id = :category_id, 
                        business_id = :business_id, 
                        status = :status, 
                        updated_at = NOW() 
                        WHERE id = :id");
            
            $db->bind(':name', $name);
            $db->bind(':description', $description);
            $db->bind(':price', $price);
            $db->bind(':price_type', $priceType);
            $db->bind(':duration', $duration);
            $db->bind(':image', $image);
            $db->bind(':category_id', $categoryId);
            $db->bind(':business_id', $businessId);
            $db->bind(':status', $status);
            $db->bind(':id', $serviceId);
            
            $db->execute();
            
            // Log activity
            logActivity($_SESSION['user_id'], 'update', 'services', $serviceId, 'Updated service: ' . $name);
            
            setFlashMessage('success', 'Service updated successfully');
            redirect('modules/services/index.php');
            
        } catch (Exception $e) {
            setFlashMessage('error', 'Error updating service: ' . $e->getMessage());
            redirect('modules/services/edit.php?id=' . $serviceId);
        }
    }
}
?>

<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center my-6">
        <h2 class="text-2xl font-semibold text-gray-800">Edit Service</h2>
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
                    <?php if ($service['image']): ?>
                        <div class="mb-2">
                            <img src="<?= UPLOADS_URL ?>/services/<?= $service['image'] ?>" alt="<?= $service['name'] ?>" class="h-20 w-20 object-cover rounded-lg">
                            <div class="mt-1">
                                <input type="checkbox" id="remove_image" name="remove_image" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                <label for="remove_image" class="ml-2 text-sm font-medium text-gray-900">Remove current image</label>
                            </div>
                        </div>
                    <?php endif; ?>
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
                    Update Service
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>