
<?php 
require_once __DIR__ . '/../vendor/autoload.php';
use App\Controllers\CartController;
use App\Controllers\ProductController;

$cartController = new CartController();
$productController = new ProductController();

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update':
                $productId = $_POST['product_id'] ?? 0;
                $quantity = $_POST['quantity'] ?? 0;
                $result = $cartController->updateCart($productId, $quantity);
                break;
            case 'remove':
                $productId = $_POST['product_id'] ?? 0;
                $result = $cartController->removeFromCart($productId);
                break;
            case 'clear':
                $result = $cartController->clearCart();
                break;
        }
        if (isset($result)) {
            header('Location: ?page=cart');
            exit;
        }
    }
}

$cartItems = $cartController->getCart();
$cartTotal = $cartController->getCartTotal();
$tax = $cartTotal * 0.10; // 10% tax
$grandTotal = $cartTotal + $tax;

include "include/header.php"; 
?>
    <!-- Page Title -->
    <section class="bg-gray-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Shopping Cart</h1>
            <p class="text-gray-600">Review and manage your items (<?php echo $cartController->getCartCount(); ?> items)</p>
        </div>
    </section>

    <!-- Cart Section -->
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php if (empty($cartItems)): ?>
                <!-- Empty Cart -->
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <i class="fas fa-shopping-cart text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Your cart is empty</h3>
                    <p class="text-gray-600 mb-6">Looks like you haven't added anything to your cart yet.</p>
                    <a href="?page=products" class="inline-block bg-primary hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg transition duration-300">
                        <i class="fas fa-shopping-bag mr-2"></i>Start Shopping
                    </a>
                </div>
            <?php else: ?>
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Cart Items -->
                <div class="w-full md:w-2/3">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6 border-b border-gray-200">
                            <h5 class="text-xl font-bold text-gray-900">Cart Items</h5>
                        </div>

                        <div class="p-6 space-y-6">
                            <?php foreach ($cartItems as $productId => $item): 
                                $itemTotal = $item['price'] * $item['quantity'];
                                $imagePath = !empty($item['image']) ? '../' . $item['image'] : 'https://via.placeholder.com/100x100?text=No+Image';
                            ?>
                            <div class="flex flex-col sm:flex-row items-center border-b border-gray-200 pb-6 last:border-0 last:pb-0">
                                <div class="w-full sm:w-24 h-24 flex-shrink-0 overflow-hidden rounded-md border border-gray-200 mb-4 sm:mb-0">
                                    <img src="<?php echo htmlspecialchars($imagePath); ?>" class="w-full h-full object-cover object-center" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                </div>
                                <div class="sm:ml-6 flex-1 flex flex-col sm:flex-row items-center justify-between w-full">
                                    <div class="text-center sm:text-left mb-4 sm:mb-0">
                                        <h6 class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($item['title']); ?></h6>
                                        <p class="text-sm text-gray-500">Product ID: <?php echo $productId; ?></p>
                                        <p class="text-primary font-bold mt-1">$<?php echo number_format($item['price'], 2); ?></p>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <form method="POST" class="flex items-center border border-gray-300 rounded-md">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                            <button type="submit" name="quantity" value="<?php echo $item['quantity'] - 1; ?>" class="px-3 py-1 text-gray-600 hover:bg-gray-100 transition duration-300">-</button>
                                            <input type="number" name="quantity_display" class="w-12 text-center border-l border-r border-gray-300 py-1 focus:outline-none" value="<?php echo $item['quantity']; ?>" readonly>
                                            <button type="submit" name="quantity" value="<?php echo $item['quantity'] + 1; ?>" class="px-3 py-1 text-gray-600 hover:bg-gray-100 transition duration-300">+</button>
                                        </form>
                                        <div class="text-right">
                                            <p class="font-bold text-gray-900 mb-2">$<?php echo number_format($itemTotal, 2); ?></p>
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="action" value="remove">
                                                <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                                <button type="submit" class="text-red-500 hover:text-red-700 transition duration-300" onclick="return confirm('Remove this item from cart?');">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-between items-center">
                            <a href="?page=products" class="text-primary hover:text-indigo-700 font-medium flex items-center transition duration-300">
                                <i class="fas fa-arrow-left mr-2"></i> Continue Shopping
                            </a>
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="clear">
                                <button type="submit" class="text-red-500 hover:text-red-700 font-medium transition duration-300" onclick="return confirm('Clear all items from cart?');">Clear Cart</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="w-full md:w-1/3">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-24">
                        <div class="p-6 border-b border-gray-200">
                            <h5 class="text-xl font-bold text-gray-900">Order Summary</h5>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span>$<?php echo number_format($cartTotal, 2); ?></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Shipping</span>
                                <span class="text-green-600 font-medium">FREE</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Tax (10%)</span>
                                <span>$<?php echo number_format($tax, 2); ?></span>
                            </div>
                            <div class="border-t border-gray-200 pt-4 flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-900">Total</span>
                                <span class="text-2xl font-bold text-primary">$<?php echo number_format($grandTotal, 2); ?></span>
                            </div>
                            
                            <div class="mt-6">
                                <label for="coupon" class="block text-sm font-medium text-gray-700 mb-2">Coupon Code</label>
                                <div class="flex space-x-2">
                                    <input type="text" id="coupon" class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Enter code">
                                    <button class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-md transition duration-300">Apply</button>
                                </div>
                            </div>
                            
                            <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                                <a href="?page=checkout" class="block w-full bg-primary hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-md transition duration-300 shadow-md mt-6 text-center">
                                    <i class="fas fa-lock mr-2"></i> Proceed to Checkout
                                </a>
                            <?php else: ?>
                                <a href="?page=login" class="block w-full bg-primary hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-md transition duration-300 shadow-md mt-6 text-center">
                                    <i class="fas fa-sign-in-alt mr-2"></i> Login to Checkout
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include "include/footer.php"; ?>