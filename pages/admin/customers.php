<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\AdminAuthController;
use App\Controllers\CustomerController;

// Check if admin is logged in
AdminAuthController::checkAuth();

$customerController = new CustomerController();

// Get all customers
$customers = $customerController->getAllCustomers();

include "include/header.php"; 
?>

    <div class="mb-6 flex justify-between items-center">
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="fas fa-search text-gray-400"></i>
            </span>
            <input type="text" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" placeholder="Search customers...">
        </div>
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
            <i class="fas fa-download"></i> Export CSV
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold">Customer</th>
                        <th class="p-4 font-semibold">Email</th>
                        <th class="p-4 font-semibold">Phone</th>
                        <th class="p-4 font-semibold">Orders</th>
                        <th class="p-4 font-semibold">Total Spent</th>
                        <th class="p-4 font-semibold">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">
                                <i class="fas fa-users text-4xl mb-2"></i>
                                <p>No customers found.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $customer): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold mr-3">
                                        <?php echo strtoupper(substr($customer['name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900"><?php echo htmlspecialchars($customer['name']); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($customer['address'] ?? 'No address'); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-gray-600"><?php echo htmlspecialchars($customer['email']); ?></td>
                            <td class="p-4 text-gray-600"><?php echo htmlspecialchars($customer['phone'] ?? 'N/A'); ?></td>
                            <td class="p-4 text-gray-900"><?php echo $customer['total_orders']; ?></td>
                            <td class="p-4 font-medium text-gray-900">$<?php echo number_format($customer['total_spent'], 2); ?></td>
                            <td class="p-4 text-gray-600"><?php echo date('M d, Y', strtotime($customer['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 flex justify-between items-center">
            <p class="text-sm text-gray-500">Showing <?php echo count($customers); ?> customer(s)</p>
        </div>
    </div>

<?php include "include/footer.php"; ?>
