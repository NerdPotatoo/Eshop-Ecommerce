<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EShop Admin - Dashboard</title>
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
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-dark text-white hidden md:flex flex-col transition-all duration-300">
            <div class="h-16 flex items-center justify-center border-b border-gray-700">
                <a href="?page=home" class="text-2xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-shopping-bag text-primary"></i> EShop Admin
                </a>
            </div>
            
            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-2 px-4">
                    <li>
                        <a href="?page=admin/dashboard" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors <?php echo ($_GET['page'] == 'admin/dashboard') ? 'bg-primary text-white' : ''; ?>">
                            <i class="fas fa-tachometer-alt w-6"></i>
                            <span class="font-medium">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="?page=admin/products" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors <?php echo ($_GET['page'] == 'admin/products' || $_GET['page'] == 'admin/add-product') ? 'bg-primary text-white' : ''; ?>">
                            <i class="fas fa-box w-6"></i>
                            <span class="font-medium">Products</span>
                        </a>
                    </li>
                    <li>
                        <a href="?page=admin/orders" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors <?php echo ($_GET['page'] == 'admin/orders') ? 'bg-primary text-white' : ''; ?>">
                            <i class="fas fa-shopping-cart w-6"></i>
                            <span class="font-medium">Orders</span>
                        </a>
                    </li>
                    <li>
                        <a href="?page=admin/customers" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors <?php echo ($_GET['page'] == 'admin/customers') ? 'bg-primary text-white' : ''; ?>">
                            <i class="fas fa-users w-6"></i>
                            <span class="font-medium">Customers</span>
                        </a>
                    </li>
                    <li>
                        <a href="?page=admin/contacts" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors <?php echo ($_GET['page'] == 'admin/contacts') ? 'bg-primary text-white' : ''; ?>">
                            <i class="fas fa-envelope w-6"></i>
                            <span class="font-medium">Contacts</span>
                        </a>
                    </li>
                    <li class="pt-4 mt-4 border-t border-gray-700">
                        <a href="?page=home" target="_blank" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors">
                            <i class="fas fa-external-link-alt w-6"></i>
                            <span class="font-medium">Visit Site</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="p-4 border-t border-gray-700">
                <a href="?page=logout&logout=admin" class="flex items-center px-4 py-2 text-red-400 hover:bg-gray-700 hover:text-red-300 rounded-lg transition-colors w-full">
                    <i class="fas fa-sign-out-alt w-6"></i>
                    <span class="font-medium">Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm z-10 h-16 flex items-center justify-between px-6">
                <div class="flex items-center">
                    <button class="text-gray-500 focus:outline-none md:hidden mr-4">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h2 class="text-xl font-semibold text-gray-800">
                        <?php 
                            if($_GET['page'] == 'admin/dashboard') echo 'Dashboard';
                            elseif($_GET['page'] == 'admin/products') echo 'Products';
                            elseif($_GET['page'] == 'admin/add-product') echo 'Add Product';
                            elseif($_GET['page'] == 'admin/orders') echo 'Orders';
                            elseif($_GET['page'] == 'admin/customers') echo 'Customers';
                            else echo 'Admin Panel';
                        ?>
                    </h2>
                </div>

                <div class="flex items-center gap-4">
                    <button class="text-gray-500 hover:text-primary transition-colors relative">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">3</span>
                    </button>
                    
                    <div class="flex items-center gap-3 border-l pl-4 ml-2">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-gray-800">
                                <?php echo isset($_SESSION['admin_username']) ? htmlspecialchars($_SESSION['admin_username']) : 'Admin'; ?>
                            </p>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-primary text-white flex items-center justify-center font-bold text-lg">
                            <?php echo isset($_SESSION['admin_username']) ? strtoupper(substr($_SESSION['admin_username'], 0, 1)) : 'A'; ?>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
