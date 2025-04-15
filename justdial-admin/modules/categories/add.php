<?php
$pageTitle = 'Add Category';
require_once '../../includes/header.php';

// Check if user has permission
if (!isManager()) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('modules/dashboard/index.php');
}

// Get parent categories for dropdown
$db = db();
$db->query("SELECT id, name FROM categories ORDER BY name");
$parentCategories = $db->resultSet();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid CSRF token');
        redirect('modules/categories/add.php');
    }
    
    // Validate input
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $icon = sanitize($_POST['icon']);
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    $status = sanitize($_POST['status']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $sort_order = (int)$_POST['sort_order'];
    
    // Generate slug
    $slug = generateSlug($name);
    
    // Check if slug already exists
    $db->query("SELECT id FROM categories WHERE slug = :slug");
    $db->bind(':slug', $slug);
    $existingCategory = $db->single();
    
    if ($existingCategory) {
        $slug = $slug . '-' . time();
    }
    
    // Upload image if provided
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $uploadResult = uploadFile($_FILES['image'], 'categories', ['jpg', 'jpeg', 'png', 'gif']);
        if ($uploadResult['success']) {
            $image = $uploadResult['filename'];
        } else {
            setFlashMessage('error', 'Image upload failed: ' . $uploadResult['message']);
            redirect('modules/categories/add.php');
        }
    }
    
    try {
        // Insert category
        $db->query("INSERT INTO categories (name, slug, description, icon, image, parent_id, status, featured, sort_order, created_at) 
                    VALUES (:name, :slug, :description, :icon, :image, :parent_id, :status, :featured, :sort_order, NOW())");
        
        $db->bind(':name', $name);
        $db->bind(':slug', $slug);
        $db->bind(':description', $description);
        $db->bind(':icon', $icon);
        $db->bind(':image', $image);
        $db->bind(':parent_id', $parent_id);
        $db->bind(':status', $status);
        $db->bind(':featured', $featured);
        $db->bind(':sort_order', $sort_order);
        
        $db->execute();
        $categoryId = $db->lastInsertId();
        
        // Log activity
        logActivity($_SESSION['user_id'], 'create', 'categories', $categoryId, 'Created category: ' . $name);
        
        setFlashMessage('success', 'Category added successfully');
        redirect('modules/categories/index.php');
        
    } catch (Exception $e) {
        setFlashMessage('error', 'Error adding category: ' . $e->getMessage());
        redirect('modules/categories/add.php');
    }
}
?>

<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center my-6">
        <h2 class="text-2xl font-semibold text-gray-800">Add Category</h2>
        <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300">
            <i class="fa-solid fa-arrow-left mr-2"></i> Back to Categories
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <form method="POST" enctype="multipart/form-data" class="p-6">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-1">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Category Name <span class="text-red-600">*</span></label>
                    <input type="text" id="name" name="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>
                
                <div class="col-span-1">
                    <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">Parent Category</label>
                    <select id="parent_id" name="parent_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="">None (Top Level)</option>
                        <?php foreach ($parentCategories as $parent): ?>
                        <option value="<?= $parent['id'] ?>"><?= $parent['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-span-1">
                    <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">Icon (Font Awesome)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fa-solid fa-icons text-gray-400"></i>
                        </div>
                        <input type="text" id="icon" name="icon" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" placeholder="e.g. utensils, hotel, car">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Enter Font Awesome icon name without the "fa-" prefix</p>
                </div>
                
                <div class="col-span-1">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-600">*</span></label>
                    <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                
                <div class="col-span-1">
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" value="0" min="0">
                </div>
                
                <div class="col-span-1">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                    <input type="file" id="image" name="image" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" accept="image/*">
                    <p class="mt-1 text-xs text-gray-500">Recommended size: 200x200 pixels</p>
                </div>
                
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"></textarea>
                </div>
                
                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <input id="featured" name="featured" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <label for="featured" class="ml-2 text-sm font-medium text-gray-900">Featured Category</label>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Featured categories will be displayed prominently on the website</p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="window.location.href='index.php'" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg mr-2">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
                    Add Category
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
