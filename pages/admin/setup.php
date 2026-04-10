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

// If admin already exists, redirect to login
if ($authController->hasAdmins()) {
    header('Location: ?page=admin/login');
    exit();
}

$error = '';
$success = '';

// Handle setup form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $result = $authController->registerFirstAdmin($username, $password);
        
        if ($result['success']) {
            $success = $result['message'];
            // Auto login after successful setup
            $loginResult = $authController->login($username, $password);
            if ($loginResult['success']) {
                header('Location: ?page=admin/dashboard');
                exit();
            }
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
    <title>EShop Admin - First Time Setup</title>
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
<body class="bg-gradient-to-br from-primary/10 to-secondary/10 font-sans antialiased min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md px-4">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-primary/80 p-8 text-white">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white/20 backdrop-blur-sm mb-4">
                        <i class="fas fa-rocket text-4xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold">Welcome to EShop!</h2>
                    <p class="mt-2 text-primary-100">Let's set up your admin account</p>
                </div>
            </div>

            <div class="p-8">
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        <div>
                            <p class="text-sm text-blue-800 font-medium">First Time Setup</p>
                            <p class="text-xs text-blue-600 mt-1">No admin accounts found. Create your first admin account to get started.</p>
                        </div>
                    </div>
                </div>

                <?php if ($error): ?>
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                        <i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-5">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user mr-1 text-gray-400"></i>Admin Username
                        </label>
                        <input type="text" id="username" name="username" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors" 
                               placeholder="Choose a username" 
                               required 
                               autofocus>
                        <p class="text-xs text-gray-500 mt-1">This will be used to login to the admin panel</p>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-lock mr-1 text-gray-400"></i>Password
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors" 
                                   placeholder="Create a strong password" 
                                   required 
                                   minlength="6">
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-lock mr-1 text-gray-400"></i>Confirm Password
                        </label>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors" 
                               placeholder="Re-enter your password" 
                               required 
                               minlength="6">
                    </div>

                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-base font-medium text-white bg-gradient-to-r from-primary to-primary/80 hover:from-primary/90 hover:to-primary/70 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200 transform hover:scale-[1.02]">
                        <i class="fas fa-check-circle mr-2 mt-1"></i>
                        Create Admin Account & Continue
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-center text-sm text-gray-500">
                        <i class="fas fa-shield-alt mr-2 text-primary"></i>
                        Your data is secure and encrypted
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="?page=home" class="text-sm text-gray-600 hover:text-primary transition-colors">
                <i class="fas fa-arrow-left mr-1"></i>Back to Store
            </a>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });

        // Password match validation
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');

        confirmPassword.addEventListener('input', function() {
            if (password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Passwords do not match');
            } else {
                confirmPassword.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
