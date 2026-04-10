<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\CartController;
use App\Controllers\UserAuthController;

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: ?page=login");
    exit;
}

$cartController = new CartController();
$userAuthController = new UserAuthController();

$cartItems = $cartController->getCart();
$cartTotal = $cartController->getCartTotal();
$tax = $cartTotal * 0.10;
$grandTotal = $cartTotal + $tax;

// If cart is empty, redirect to cart page
if (empty($cartItems)) {
    header("Location: ?page=cart");
    exit;
}

// Get user details
$userId = $_SESSION['user_id'];
$user = $userAuthController->getUserById($userId);

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // Get shipping details from form
    $shippingName = htmlspecialchars(trim($_POST['shipping_name']));
    $shippingEmail = htmlspecialchars(trim($_POST['shipping_email']));
    $shippingPhone = htmlspecialchars(trim($_POST['shipping_phone']));
    $shippingAddress = htmlspecialchars(trim($_POST['shipping_address']));
    $paymentMethod = htmlspecialchars(trim($_POST['payment_method']));
    
    // Create shipping details array
    $shippingDetails = [
        'name' => $shippingName,
        'email' => $shippingEmail,
        'phone' => $shippingPhone,
        'address' => $shippingAddress
    ];
    
    // Place order
    $orderId = $cartController->checkout($userId, $shippingDetails, $paymentMethod);
    
    if ($orderId) {
        $_SESSION['order_success'] = true;
        $_SESSION['order_id'] = $orderId;
        header("Location: ?page=order-confirmation");
        exit;
    } else {
        $error = "Failed to place order. Please try again.";
    }
}

include 'pages/include/header.php';
?>

<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
            <p class="text-gray-600 mt-2">Complete your purchase</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="flex flex-col md:flex-row gap-8">
            
            <!-- Left Column - Shipping & Payment -->
            <div class="w-full md:w-2/3 space-y-6">
                
                <!-- Shipping Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Shipping Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="shipping_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" id="shipping_name" name="shipping_name" required
                                   value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="shipping_email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                            <input type="email" id="shipping_email" name="shipping_email" required
                                   value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="shipping_phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                            <input type="tel" id="shipping_phone" name="shipping_phone" required
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-2">Shipping Address *</label>
                            <textarea id="shipping_address" name="shipping_address" required rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Payment Method</h2>
                    
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="cod" checked
                                   class="h-4 w-4 text-primary focus:ring-primary">
                            <span class="ml-3 text-gray-700 font-medium">Cash on Delivery</span>
                        </label>
                        
                        <label class="flex items-center p-4 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50 opacity-50">
                            <input type="radio" name="payment_method" value="card" disabled
                                   class="h-4 w-4 text-primary focus:ring-primary">
                            <span class="ml-3 text-gray-700 font-medium">Credit/Debit Card (Coming Soon)</span>
                        </label>
                        
                        <label class="flex items-center p-4 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50 opacity-50">
                            <input type="radio" name="payment_method" value="paypal" disabled
                                   class="h-4 w-4 text-primary focus:ring-primary">
                            <span class="ml-3 text-gray-700 font-medium">PayPal (Coming Soon)</span>
                        </label>
                    </div>
                </div>
                
            </div>
            
            <!-- Right Column - Order Summary -->
            <div class="w-full md:w-1/3">
                <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-24">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Order Summary</h2>
                    </div>
                    
                    <div class="p-6">
                        <!-- Cart Items -->
                        <div class="space-y-4 mb-6">
                            <?php foreach ($cartItems as $item): ?>
                                <div class="flex items-center space-x-3">
                                    <img src="<?php echo !empty($item['image']) ? '../' . htmlspecialchars($item['image']) : 'https://via.placeholder.com/60'; ?>" 
                                         alt="<?php echo htmlspecialchars($item['title']); ?>"
                                         class="w-16 h-16 object-cover rounded-md">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item['title']); ?></h4>
                                        <p class="text-sm text-gray-600">Qty: <?php echo $item['quantity']; ?></p>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Price Breakdown -->
                        <div class="space-y-3 border-t border-gray-200 pt-4">
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
                            <div class="border-t border-gray-200 pt-3 flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-900">Total</span>
                                <span class="text-2xl font-bold text-primary">$<?php echo number_format($grandTotal, 2); ?></span>
                            </div>
                        </div>
                        
                        <button type="submit" name="place_order"
                                class="w-full bg-primary hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-md transition duration-300 shadow-md mt-6">
                            <i class="fas fa-check-circle mr-2"></i> Place Order
                        </button>
                        
                        <a href="?page=cart" class="block text-center text-gray-600 hover:text-gray-800 mt-4 text-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Cart
                        </a>
                    </div>
                </div>
            </div>
            
        </form>
        
    </div>
</div>

<?php include 'pages/include/footer.php'; ?>
