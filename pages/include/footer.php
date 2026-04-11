    <footer class="bg-gray-900 text-white pt-12 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="mb-8 md:mb-0">
                    <h5 class="text-xl font-bold mb-4 text-primary">About EShop</h5>
                    <p class="text-gray-400 leading-relaxed">Your one-stop destination for quality products at great prices. We believe in providing the best shopping experience for our customers.</p>
                </div>
                <div class="mb-8 md:mb-0">
                    <h5 class="text-xl font-bold mb-4 text-primary">Quick Links</h5>
                    <ul class="space-y-2">
                        <li><a href="?page=home" class="text-gray-400 hover:text-white transition duration-300 flex items-center"><i class="fas fa-chevron-right text-xs mr-2"></i> Home</a></li>
                        <li><a href="?page=products" class="text-gray-400 hover:text-white transition duration-300 flex items-center"><i class="fas fa-chevron-right text-xs mr-2"></i> Products</a></li>
                        <li><a href="?page=about" class="text-gray-400 hover:text-white transition duration-300 flex items-center"><i class="fas fa-chevron-right text-xs mr-2"></i> About</a></li>
                        <li><a href="?page=contact" class="text-gray-400 hover:text-white transition duration-300 flex items-center"><i class="fas fa-chevron-right text-xs mr-2"></i> Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-xl font-bold mb-4 text-primary">Follow Us</h5>
                    <div class="flex space-x-4">
                        <a href="https://www.facebook.com/YASIRADNANSAMI" target="_blank" class="bg-gray-800 hover:bg-blue-600 text-white h-10 w-10 rounded-full flex items-center justify-center transition duration-300"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/_myself_mishu/" target="_blank" class="bg-gray-800 hover:bg-pink-600 text-white h-10 w-10 rounded-full flex items-center justify-center transition duration-300"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.linkedin.com/in/yasiradnan-/" target="_blank" class="bg-gray-800 hover:bg-blue-700 text-white h-10 w-10 rounded-full flex items-center justify-center transition duration-300"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <hr class="border-gray-800 my-8">
            <div class="text-center text-gray-500">
                <p>&copy; 2024 EShop. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        function addToCart(productId) {
            // Note: This function may be overridden in individual pages
            // Default implementation for pages that don't have their own
            const button = event.target.closest('button');
            if (button) {
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Adding...';
            }

            fetch('action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'page=add-to-cart&product_id=' + productId + '&quantity=1'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Product added to cart!', 'success');
                    updateCartCount();
                } else {
                    showNotification(data.message || 'Failed to add product to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while adding to cart', 'error');
            })
            .finally(() => {
                if (button) {
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-shopping-cart mr-1"></i>Add to Cart';
                }
            });
        }

        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-20 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        function updateCartCount() {
            fetch('action.php?page=cart-count')
                .then(response => response.json())
                .then(data => {
                    const cartCountElement = document.getElementById('cart-count');
                    if (cartCountElement && data.count > 0) {
                        cartCountElement.textContent = data.count;
                        cartCountElement.classList.remove('hidden');
                    }
                })
                .catch(error => console.error('Error updating cart count:', error));
        }
    </script>
</body>
</html>
