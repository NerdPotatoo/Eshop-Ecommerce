
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$userName = $_SESSION['user_name'] ?? '';

// Get cart count
require_once __DIR__ . '/../../vendor/autoload.php';
use App\Controllers\CartController;
$cartController = new CartController();
$cartCount = $cartController->getCartCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EShop - Home</title>
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
<body class="bg-gray-50 font-sans antialiased">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="?page=home" class="flex-shrink-0 flex items-center text-primary text-2xl font-bold">
                        <i class="fas fa-shopping-bag mr-2"></i> EShop
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="?page=home" class="<?php echo (isset($_GET['page']) && $_GET['page']=='home') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary transition duration-300'; ?>">Home</a>
                    <a href="?page=products" class="<?php echo (isset($_GET['page']) && $_GET['page']=='products') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary transition duration-300'; ?>">Products</a>
                    <a href="?page=about" class="<?php echo (isset($_GET['page']) && $_GET['page']=='about') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary transition duration-300'; ?>">About</a>
                    <a href="?page=contact" class="<?php echo (isset($_GET['page']) && $_GET['page']=='contact') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary transition duration-300'; ?>">Contact</a>
                    
                    <?php if ($isLoggedIn): ?>
                        <span class="text-gray-600"><i class="fas fa-user-circle mr-1"></i>Hi, <?php echo htmlspecialchars(explode(' ', $userName)[0]); ?></span>
                        <a href="?page=logout" class="text-gray-600 hover:text-primary transition duration-300"><i class="fas fa-sign-out-alt mr-1"></i>Logout</a>
                    <?php else: ?>
                        <a href="?page=login" class="<?php echo (isset($_GET['page']) && $_GET['page']=='login') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary transition duration-300'; ?>">Login</a>
                        <a href="?page=signup" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition duration-300">Sign Up</a>
                    <?php endif; ?>
                    
                    <a href="?page=cart" class="relative <?php echo (isset($_GET['page']) && $_GET['page']=='cart') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary transition duration-300'; ?>">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center <?php echo $cartCount > 0 ? '' : 'hidden'; ?>">
                            <?php echo $cartCount; ?>
                        </span>
                    </a>
                </div>
                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-button" class="text-gray-600 hover:text-primary focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="?page=home" class="block px-3 py-2 rounded-md text-base font-medium <?php echo (isset($_GET['page']) && $_GET['page']=='home') ? 'text-primary bg-gray-50' : 'text-gray-600 hover:text-primary hover:bg-gray-50'; ?>">Home</a>
                <a href="?page=products" class="block px-3 py-2 rounded-md text-base font-medium <?php echo (isset($_GET['page']) && $_GET['page']=='products') ? 'text-primary bg-gray-50' : 'text-gray-600 hover:text-primary hover:bg-gray-50'; ?>">Products</a>
                <a href="?page=about" class="block px-3 py-2 rounded-md text-base font-medium <?php echo (isset($_GET['page']) && $_GET['page']=='about') ? 'text-primary bg-gray-50' : 'text-gray-600 hover:text-primary hover:bg-gray-50'; ?>">About</a>
                <a href="?page=contact" class="block px-3 py-2 rounded-md text-base font-medium <?php echo (isset($_GET['page']) && $_GET['page']=='contact') ? 'text-primary bg-gray-50' : 'text-gray-600 hover:text-primary hover:bg-gray-50'; ?>">Contact</a>
                
                <?php if ($isLoggedIn): ?>
                    <div class="px-3 py-2 text-sm text-gray-600 border-t border-gray-200 mt-2 pt-2">
                        <i class="fas fa-user-circle mr-1"></i> <?php echo htmlspecialchars($userName); ?>
                    </div>
                    <a href="?page=logout" class="block px-3 py-2 rounded-md text-base font-medium text-red-600 hover:text-red-700 hover:bg-red-50"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
                <?php else: ?>
                    <a href="?page=login" class="block px-3 py-2 rounded-md text-base font-medium <?php echo (isset($_GET['page']) && $_GET['page']=='login') ? 'text-primary bg-gray-50' : 'text-gray-600 hover:text-primary hover:bg-gray-50'; ?>">Login</a>
                    <a href="?page=signup" class="block px-3 py-2 rounded-md text-base font-medium bg-primary text-white hover:bg-indigo-700">Sign Up</a>
                <?php endif; ?>
                
                <a href="?page=cart" class="block px-3 py-2 rounded-md text-base font-medium <?php echo (isset($_GET['page']) && $_GET['page']=='cart') ? 'text-primary bg-gray-50' : 'text-gray-600 hover:text-primary hover:bg-gray-50'; ?>">Cart (0)</a>
            </div>
        </div>
    </nav>
    <script>
        const btn = document.getElementById('mobile-menu-button');
        const menu = document.getElementById('mobile-menu');

        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    </script>
