<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\AdminAuthController;
use App\Controllers\ProductController;
use App\Controllers\CategoryController;

// Check if admin is logged in
AdminAuthController::checkAuth();

$productController = new ProductController();
$categoryController = new CategoryController();
$error = '';
$success = '';
$product = null;
$isEdit = false;

// Handle AJAX category creation
if (isset($_POST['ajax_add_category'])) {
    header('Content-Type: application/json');
    $name = trim($_POST['category_name'] ?? '');
    $description = trim($_POST['category_description'] ?? '');
    
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Category name is required']);
        exit();
    }
    
    $result = $categoryController->createCategory([
        'name' => $name,
        'description' => $description
    ]);
    
    if ($result['success']) {
        $categories = $categoryController->getAllCategories();
        $result['categories'] = $categories;
    }
    
    echo json_encode($result);
    exit();
}

// Get all categories
$categories = $categoryController->getAllCategories();
$hasCategories = !empty($categories);

// Check if editing existing product
if (isset($_GET['id'])) {
    $isEdit = true;
    $product = $productController->getProductById($_GET['id']);
    if (!$product) {
        header('Location: ?page=admin/products&msg=Product not found');
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $image = $_POST['existing_image'] ?? '';

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/products/';
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = 'uploads/products/' . $fileName;
        }
    }

    // Validation
    if (empty($title) || $price <= 0) {
        $error = 'Please fill in all required fields';
    } else {
        $data = [
            'title' => $title,
            'description' => $description,
            'price' => $price,
            'category_id' => $category_id,
            'stock' => $stock,
            'image' => $image
        ];

        if ($isEdit) {
            $result = $productController->updateProduct($_GET['id'], $data);
        } else {
            $result = $productController->createProduct($data);
        }

        if ($result['success']) {
            header('Location: ?page=admin/products&msg=' . urlencode($result['message']));
            exit();
        } else {
            $error = $result['message'];
        }
    }
}

include "include/header.php";
?>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800"><?php echo $isEdit ? 'Edit' : 'Add New'; ?> Product</h3>
                <a href="?page=admin/products" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
            
            <?php if ($error): ?>
                <div class="m-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($product['image'] ?? ''); ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($product['title'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors" placeholder="e.g. Wireless Headphones" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price ($) *</label>
                                <input type="number" id="price" name="price" value="<?php echo $product['price'] ?? ''; ?>" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors" placeholder="0.00" required>
                            </div>
                            <div>
                                <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity *</label>
                                <input type="number" id="stock" name="stock" value="<?php echo $product['stock'] ?? ''; ?>" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors" placeholder="0" required>
                            </div>
                        </div>

                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1 flex justify-between items-center">
                                <span>Category</span>
                                <button type="button" onclick="openCategoryModal()" class="text-xs text-primary hover:text-primary-dark">
                                    <i class="fas fa-plus"></i> Add New
                                </button>
                            </label>
                            <select id="category_id" name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo (isset($product['category_id']) && $product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Product Image</label>
                            <?php if ($isEdit && $product['image']): ?>
                                <div class="mb-2">
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Current image" class="w-32 h-32 object-cover rounded-lg">
                                    <p class="text-xs text-gray-500 mt-1">Current image (upload new to replace)</p>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="image" id="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                            <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 10MB</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors" placeholder="Product description..."><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                    <a href="?page=admin/products" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors inline-block">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg shadow-md transition-colors">
                        <i class="fas fa-save mr-2"></i><?php echo $isEdit ? 'Update' : 'Save'; ?> Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Category Modal -->
    <div id="categoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Add New Category</h3>
                <button onclick="closeCategoryModal()" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="categoryForm" class="p-6 space-y-4">
                <div id="categoryError" class="hidden p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    <i class="fas fa-exclamation-circle mr-2"></i><span></span>
                </div>
                
                <div id="categorySuccess" class="hidden p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    <i class="fas fa-check-circle mr-2"></i><span></span>
                </div>

                <div>
                    <label for="category_name" class="block text-sm font-medium text-gray-700 mb-1">Category Name *</label>
                    <input type="text" id="category_name" name="category_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors" placeholder="e.g. Electronics" required>
                </div>

                <div>
                    <label for="category_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="category_description" name="category_description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors" placeholder="Category description..."></textarea>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" onclick="closeCategoryModal()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg shadow-md transition-colors">
                        <i class="fas fa-save mr-2"></i>Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Show modal automatically if no categories exist
        <?php if (!$hasCategories): ?>
        window.addEventListener('DOMContentLoaded', function() {
            openCategoryModal();
            showInfo('No categories found. Please add a category first.');
        });
        <?php endif; ?>

        function openCategoryModal() {
            document.getElementById('categoryModal').classList.remove('hidden');
            document.getElementById('category_name').focus();
        }

        function closeCategoryModal() {
            document.getElementById('categoryModal').classList.add('hidden');
            document.getElementById('categoryForm').reset();
            document.getElementById('categoryError').classList.add('hidden');
            document.getElementById('categorySuccess').classList.add('hidden');
        }

        function showInfo(message) {
            const successDiv = document.getElementById('categorySuccess');
            successDiv.querySelector('span').textContent = message;
            successDiv.classList.remove('hidden');
        }

        function showError(message) {
            const errorDiv = document.getElementById('categoryError');
            errorDiv.querySelector('span').textContent = message;
            errorDiv.classList.remove('hidden');
        }

        // Handle category form submission
        document.getElementById('categoryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('ajax_add_category', '1');
            formData.append('category_name', document.getElementById('category_name').value);
            formData.append('category_description', document.getElementById('category_description').value);

            fetch('?page=admin/add-product', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update category dropdown
                    const categorySelect = document.getElementById('category_id');
                    categorySelect.innerHTML = '<option value="">Select Category</option>';
                    
                    data.categories.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.name;
                        if (category.id == data.id) {
                            option.selected = true;
                        }
                        categorySelect.appendChild(option);
                    });

                    showInfo(data.message);
                    setTimeout(() => {
                        closeCategoryModal();
                    }, 1500);
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                showError('An error occurred. Please try again.');
                console.error('Error:', error);
            });
        });

        // Close modal when clicking outside
        document.getElementById('categoryModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCategoryModal();
            }
        });
    </script>

<?php include "include/footer.php"; ?>
