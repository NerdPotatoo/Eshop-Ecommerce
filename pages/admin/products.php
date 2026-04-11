<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\AdminAuthController;
use App\Controllers\ProductController;

// Check if admin is logged in
AdminAuthController::checkAuth();

$productController = new ProductController();

// Handle delete request
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $result = $productController->deleteProduct($_GET['id']);
    header('Location: ?page=admin/products&msg=' . urlencode($result['message']));
    exit();
}

// Get all products
$products = $productController->getAllProducts();

include "include/header.php"; 
?>

    <?php if (isset($_GET['msg'])): ?>
        <div id="successMessage" class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg transition-opacity duration-500">
            <?php echo htmlspecialchars($_GET['msg']); ?>
        </div>
    <?php endif; ?>

    <div class="mb-6 flex justify-between items-center">
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="fas fa-search text-gray-400"></i>
            </span>
            <input type="text" id="searchInput" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" placeholder="Search products...">
        </div>
        <a href="?page=admin/add-product" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
            <i class="fas fa-plus"></i> Add Product
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold">Product</th>
                        <th class="p-4 font-semibold">Category</th>
                        <th class="p-4 font-semibold">Price</th>
                        <th class="p-4 font-semibold">Stock</th>
                        <th class="p-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500">
                            <i class="fas fa-box-open text-4xl mb-2 text-gray-300"></i>
                            <p>No products found</p>
                            <a href="?page=admin/add-product" class="text-blue-500 hover:text-blue-700 mt-2 inline-block">Add your first product</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 flex-shrink-0 overflow-hidden">
                                    <?php if ($product['image']): ?>
                                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900"><?php echo htmlspecialchars($product['title']); ?></p>
                                    <p class="text-xs text-gray-500">ID: <?php echo $product['id']; ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-gray-600"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                        <td class="p-4 font-medium text-gray-900">$<?php echo number_format($product['price'], 2); ?></td>
                        <td class="p-4 text-gray-600"><?php echo $product['stock']; ?></td>
                        <td class="p-4 text-right space-x-2">
                            <a href="?page=admin/add-product&id=<?php echo $product['id']; ?>" class="text-blue-500 hover:text-blue-700 transition-colors">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(<?php echo $product['id']; ?>)" class="text-red-500 hover:text-red-700 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 flex justify-between items-center">
            <p class="text-sm text-gray-500">Showing <?php echo count($products); ?> product(s)</p>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                window.location.href = '?page=admin/products&action=delete&id=' + id;
            }
        }

        // Auto-hide success message after 5 seconds
        <?php if (isset($_GET['msg'])): ?>
        setTimeout(function() {
            const message = document.getElementById('successMessage');
            if (message) {
                message.style.opacity = '0';
                setTimeout(function() {
                    message.remove();
                }, 500);
            }
        }, 5000);
        <?php endif; ?>
    </script>

<?php include "include/footer.php"; ?>
