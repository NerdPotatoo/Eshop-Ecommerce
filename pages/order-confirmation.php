<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\OrderController;

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: ?page=login");
    exit;
}

// Check if order was successfully placed
if (!isset($_SESSION['order_success']) || !isset($_SESSION['order_id'])) {
    header("Location: ?page=index");
    exit;
}

$orderId = $_SESSION['order_id'];
$orderController = new OrderController();
$order = $orderController->getOrderById($orderId);

// Clear the order success session
unset($_SESSION['order_success']);
unset($_SESSION['order_id']);

include 'pages/include/header.php';
?>

<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Success Message -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="text-center py-12 px-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-6">
                    <i class="fas fa-check-circle text-5xl text-green-600"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Placed Successfully!</h1>
                <p class="text-gray-600 mb-6">Thank you for your purchase. Your order has been received and is being processed.</p>
                
                <div class="bg-gray-50 rounded-lg p-6 mb-6 inline-block">
                    <p class="text-sm text-gray-600 mb-1">Order Number</p>
                    <p class="text-2xl font-bold text-primary">#<?php echo str_pad($orderId, 6, '0', STR_PAD_LEFT); ?></p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="?page=products" class="bg-primary hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-md transition duration-300">
                        <i class="fas fa-shopping-bag mr-2"></i> Continue Shopping
                    </a>
                    <a href="?page=index" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-6 rounded-md transition duration-300">
                        <i class="fas fa-home mr-2"></i> Go to Homepage
                    </a>
                </div>
            </div>
        </div>
        
        <?php if ($order): ?>
        <!-- Order Details -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Order Details</h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-600 mb-2">Order Information</h3>
                        <div class="space-y-1">
                            <p class="text-gray-900"><span class="font-medium">Order ID:</span> #<?php echo str_pad($orderId, 6, '0', STR_PAD_LEFT); ?></p>
                            <p class="text-gray-900"><span class="font-medium">Date:</span> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                            <p class="text-gray-900"><span class="font-medium">Status:</span> 
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                    <?php echo htmlspecialchars($order['status']); ?>
                                </span>
                            </p>
                            <p class="text-gray-900"><span class="font-medium">Payment:</span> <?php echo strtoupper(htmlspecialchars($order['payment_method'] ?? 'COD')); ?></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($order['shipping_info'])): 
                        $shippingInfo = json_decode($order['shipping_info'], true);
                    ?>
                    <div>
                        <h3 class="text-sm font-medium text-gray-600 mb-2">Shipping Information</h3>
                        <div class="space-y-1">
                            <p class="text-gray-900"><?php echo htmlspecialchars($shippingInfo['name'] ?? ''); ?></p>
                            <p class="text-gray-900"><?php echo htmlspecialchars($shippingInfo['email'] ?? ''); ?></p>
                            <p class="text-gray-900"><?php echo htmlspecialchars($shippingInfo['phone'] ?? ''); ?></p>
                            <p class="text-gray-900"><?php echo htmlspecialchars($shippingInfo['address'] ?? ''); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Order Items -->
                <?php 
                $orderItems = $orderController->getOrderItems($orderId);
                if (!empty($orderItems)): 
                ?>
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-sm font-medium text-gray-600 mb-4">Order Items</h3>
                    <div class="space-y-4">
                        <?php foreach ($orderItems as $item): ?>
                        <div class="flex items-center space-x-4">
                            <img src="<?php echo !empty($item['image']) ? 'uploads/products/' . htmlspecialchars($item['image']) : 'https://via.placeholder.com/80'; ?>" 
                                 alt="<?php echo htmlspecialchars($item['title']); ?>"
                                 class="w-20 h-20 object-cover rounded-md">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900"><?php echo htmlspecialchars($item['title']); ?></h4>
                                <p class="text-sm text-gray-600">Quantity: <?php echo $item['quantity']; ?></p>
                                <p class="text-sm text-gray-600">Price: $<?php echo number_format($item['price'], 2); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-900">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Order Total -->
                <div class="border-t border-gray-200 mt-6 pt-6">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-900">Total Amount</span>
                        <span class="text-2xl font-bold text-primary">$<?php echo number_format($order['total'], 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Help Section -->
        <div class="mt-8 text-center">
            <p class="text-gray-600 mb-2">Need help with your order?</p>
            <a href="?page=contact" class="text-primary hover:text-indigo-700 font-medium">
                <i class="fas fa-envelope mr-1"></i> Contact Us
            </a>
        </div>
        
    </div>
</div>

<?php include 'pages/include/footer.php'; ?>
