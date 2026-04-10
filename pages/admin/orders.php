<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\AdminAuthController;
use App\Controllers\OrderController;

// Check if admin is logged in
AdminAuthController::checkAuth();

$orderController = new OrderController();

// Handle status update
if (isset($_GET['action']) && $_GET['action'] == 'update_status' && isset($_GET['id']) && isset($_GET['status'])) {
    $result = $orderController->updateOrderStatus($_GET['id'], $_GET['status']);
    header('Location: ?page=admin/orders&msg=' . urlencode($result['message']));
    exit();
}

// Get all orders
$orders = $orderController->getAllOrders();

include "include/header.php"; 
?>

    <?php if (isset($_GET['msg'])): ?>
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
            <?php echo htmlspecialchars($_GET['msg']); ?>
        </div>
    <?php endif; ?>

    <div class="mb-6 flex justify-between items-center">
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="fas fa-search text-gray-400"></i>
            </span>
            <input type="text" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" placeholder="Search orders...">
        </div>
        <div class="flex gap-2">
            <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
                <i class="fas fa-filter"></i> Filter
            </button>
            <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
                <i class="fas fa-download"></i> Export
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold">Order ID</th>
                        <th class="p-4 font-semibold">Customer</th>
                        <th class="p-4 font-semibold">Date</th>
                        <th class="p-4 font-semibold">Total</th>
                        <th class="p-4 font-semibold">Payment</th>
                        <th class="p-4 font-semibold">Status</th>
                        <th class="p-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-500">
                                <i class="fas fa-shopping-cart text-4xl mb-2"></i>
                                <p>No orders found.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): 
                            $statusColors = [
                                'Pending' => 'bg-yellow-100 text-yellow-700',
                                'Processing' => 'bg-blue-100 text-blue-700',
                                'Completed' => 'bg-green-100 text-green-700',
                                'Cancelled' => 'bg-red-100 text-red-700'
                            ];
                            $statusColor = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-700';
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 font-medium text-primary">#<?php echo $order['id']; ?></td>
                            <td class="p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs mr-2">
                                        <?php echo strtoupper(substr($order['customer_name'] ?? 'N', 0, 1)); ?>
                                    </div>
                                    <span class="text-gray-900"><?php echo htmlspecialchars($order['customer_name'] ?? 'Unknown'); ?></span>
                                </div>
                            </td>
                            <td class="p-4 text-gray-600"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            <td class="p-4 font-medium text-gray-900">$<?php echo number_format($order['total'], 2); ?></td>
                            <td class="p-4 text-gray-600">Online</td>
                            <td class="p-4">
                                <select onchange="updateStatus(<?php echo $order['id']; ?>, this.value)" class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $statusColor; ?> border-0">
                                    <option value="Pending" <?php echo $order['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Processing" <?php echo $order['status'] == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="Completed" <?php echo $order['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="Cancelled" <?php echo $order['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </td>
                            <td class="p-4 text-right">
                                <button class="text-gray-500 hover:text-primary transition-colors"><i class="fas fa-eye"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 flex justify-between items-center">
            <p class="text-sm text-gray-500">Showing <?php echo count($orders); ?> order(s)</p>
        </div>
    </div>

    <script>
        function updateStatus(orderId, status) {
            if (confirm('Are you sure you want to update this order status?')) {
                window.location.href = '?page=admin/orders&action=update_status&id=' + orderId + '&status=' + status;
            } else {
                location.reload();
            }
        }
    </script>

<?php include "include/footer.php"; ?>
