<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: ?page=admin/dashboard');
    exit();
}

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\AdminAuthController;

$authController = new AdminAuthController();

// Check if any admin exists, if not redirect to setup
if (!$authController->hasAdmins()) {
    header('Location: ?page=admin/setup');
    exit();
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        $result = $authController->login($username, $password);
        
        if ($result['success']) {
            header('Location: ?page=admin/dashboard');
            exit();
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EShop Admin - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#10B981',
                        dark: '#1F2937',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans antialiased min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md px-4">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-8">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 text-primary mb-4">
                        <i class="fas fa-user-shield text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Admin Login</h2>
                    <p class="text-gray-500 mt-2">Sign in to manage your store</p>
                </div>

                <?php if ($error): ?>
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" id="username" name="username" class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors" placeholder="admin" required>
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="password" name="password" class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-900">Remember me</label>
                        </div>
                        <div class="text-sm">
                            <a href="#" class="font-medium text-primary hover:text-primary-dark">Forgot password?</a>
                        </div>
                    </div>

                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                        Sign in
                    </button>
                </form>
            </div>
            <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 text-center">
                <p class="text-sm text-gray-500">
                    Back to <a href="?page=home" class="font-medium text-primary hover:text-primary-dark">EShop Store</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
