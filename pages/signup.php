
<?php 
require_once __DIR__ . '/../vendor/autoload.php';
use App\Controllers\UserAuthController;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (UserAuthController::isLoggedIn()) {
    header('Location: ?page=home');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $terms = isset($_POST['terms']);
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($address) || empty($phone)) {
        $error = 'Please fill in all required fields';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = 'Password must contain uppercase, lowercase, and numbers';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } elseif (!$terms) {
        $error = 'You must accept the Terms of Service and Privacy Policy';
    } else {
        $authController = new UserAuthController();
        $result = $authController->register($name, $email, $password, $address, $phone);
        
        if ($result['success']) {
            $success = 'Registration successful! You can now log in.';
        } else {
            $error = $result['message'];
        }
    }
}

include "include/header.php"; 
?>

    <!-- Signup Section -->
    <section class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="bg-white p-10 rounded-xl shadow-2xl">
                <div class="text-center mb-8">
                    <i class="fas fa-user-plus text-6xl text-primary mb-4"></i>
                    <h2 class="text-3xl font-extrabold text-gray-900">Create Your Account</h2>
                    <p class="mt-2 text-sm text-gray-600">Join EShop and start shopping today!</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline"><i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline"><i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($success); ?></span>
                        <p class="mt-2 text-sm"><a href="?page=login" class="font-bold underline hover:text-green-800">Click here to login</a></p>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="signupForm" class="mt-8 space-y-6">
                    <div class="rounded-md shadow-sm space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" id="name" name="name" autocomplete="name" required 
                                       class="appearance-none rounded-md relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" 
                                       placeholder="John Doe" 
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input type="email" id="email" name="email" autocomplete="email" required 
                                       class="appearance-none rounded-md relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" 
                                       placeholder="your@email.com" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-gray-400"></i>
                                </div>
                                <input type="tel" id="phone" name="phone" autocomplete="tel" required 
                                       class="appearance-none rounded-md relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" 
                                       placeholder="+1 (555) 123-4567" 
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                                </div>
                                <textarea id="address" name="address" rows="2" required 
                                          class="appearance-none rounded-md relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" 
                                          placeholder="123 Main St, City, State, ZIP"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password" id="password" name="password" autocomplete="new-password" required 
                                       class="appearance-none rounded-md relative block w-full px-3 py-2 pl-10 pr-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" 
                                       placeholder="Create a strong password">
                                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500"><i class="fas fa-info-circle mr-1"></i>At least 8 characters with uppercase, lowercase, and numbers</p>
                        </div>

                        <div>
                            <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password" id="confirmPassword" name="confirmPassword" autocomplete="new-password" required 
                                       class="appearance-none rounded-md relative block w-full px-3 py-2 pl-10 pr-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" 
                                       placeholder="Re-enter your password">
                                <button type="button" id="toggleConfirmPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="terms" name="terms" type="checkbox" required 
                                   class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                        </div>
                        <label for="terms" class="ml-2 block text-sm text-gray-900">
                            I agree to the <a href="#" class="font-medium text-primary hover:text-indigo-500">Terms of Service</a> and <a href="#" class="font-medium text-primary hover:text-indigo-500">Privacy Policy</a>
                        </label>
                    </div>

                    <div>
                        <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition duration-300">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-user-plus text-indigo-300 group-hover:text-indigo-200"></i>
                            </span>
                            Create Account
                        </button>
                    </div>

                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">OR</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3">
                        <button type="button" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition duration-300">
                            <i class="fab fa-google text-red-500 mr-2 text-lg"></i> Sign up with Google
                        </button>
                        <button type="button" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition duration-300">
                            <i class="fab fa-facebook-f text-blue-600 mr-2 text-lg"></i> Sign up with Facebook
                        </button>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-sm text-gray-600">Already have an account? <a href="?page=login" class="font-medium text-primary hover:text-indigo-500">Sign in here</a></p>
                    </div>
                </form>
                </div>

                <!-- Features -->
                <div class="grid grid-cols-3 gap-4 text-center mt-8">
                    <div>
                        <i class="fas fa-lock text-primary text-3xl mb-2"></i>
                        <p class="text-xs text-gray-500 font-medium">Secure Signup</p>
                    </div>
                    <div>
                        <i class="fas fa-truck text-primary text-3xl mb-2"></i>
                        <p class="text-xs text-gray-500 font-medium">Fast Delivery</p>
                    </div>
                    <div>
                        <i class="fas fa-gift text-primary text-3xl mb-2"></i>
                        <p class="text-xs text-gray-500 font-medium">Exclusive Offers</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include "include/footer.php"; ?>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Toggle confirm password visibility
        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const confirmPasswordInput = document.getElementById('confirmPassword');
            const icon = this.querySelector('i');
            if (confirmPasswordInput.type === 'password') {
                confirmPasswordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                confirmPasswordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Client-side validation for better UX
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const terms = document.getElementById('terms').checked;

            // Validate password match
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }

            // Validate password strength
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long!');
                return false;
            }

            if (!/[A-Z]/.test(password) || !/[a-z]/.test(password) || !/[0-9]/.test(password)) {
                e.preventDefault();
                alert('Password must contain uppercase, lowercase, and numbers!');
                return false;
            }

            // Validate terms acceptance
            if (!terms) {
                e.preventDefault();
                alert('Please accept the Terms of Service and Privacy Policy!');
                return false;
            }

            // Form will submit naturally if validation passes
        });

        // Real-time password validation indicator
        const passwordInput = document.getElementById('password');
        const passwordFeedback = document.createElement('div');
        passwordFeedback.id = 'passwordStrength';
        passwordFeedback.className = 'mt-2';

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let feedbackText = '';
            let feedbackClass = '';

            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            if (strength === 0) {
                feedbackText = '';
                feedbackClass = '';
            } else if (strength < 2) {
                feedbackText = '⚠️ Weak password';
                feedbackClass = 'text-red-500';
            } else if (strength < 4) {
                feedbackText = '⚡ Fair password';
                feedbackClass = 'text-yellow-500';
            } else {
                feedbackText = '✓ Strong password';
                feedbackClass = 'text-green-500';
            }

            if (document.getElementById('passwordStrength')) {
                document.getElementById('passwordStrength').textContent = feedbackText;
                document.getElementById('passwordStrength').className = `mt-2 ${feedbackClass} text-xs font-bold`;
            } else if (feedbackText) {
                passwordFeedback.textContent = feedbackText;
                passwordFeedback.className = `mt-2 ${feedbackClass} text-xs font-bold`;
                this.parentNode.appendChild(passwordFeedback);
            }
        });
    </script>
</body>
</html>
