<?php
namespace App\Controllers;

use App\configs\Database;
use PDO;

class CartController {
    private $db;
    private $conn;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = new Database();
        $this->conn = $this->db->connect();
        
        // Initialize cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function addToCart($productId, $quantity = 1) {
        try {
            // Check if product exists and has stock
            $query = "SELECT id, title, price, stock, image FROM products WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $productId);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($product['stock'] <= 0) {
                    return ['success' => false, 'message' => 'Product is out of stock'];
                }
                
                // Check if product already in cart
                if (isset($_SESSION['cart'][$productId])) {
                    $newQuantity = $_SESSION['cart'][$productId]['quantity'] + $quantity;
                    if ($newQuantity > $product['stock']) {
                        return ['success' => false, 'message' => 'Not enough stock available'];
                    }
                    $_SESSION['cart'][$productId]['quantity'] = $newQuantity;
                } else {
                    if ($quantity > $product['stock']) {
                        return ['success' => false, 'message' => 'Not enough stock available'];
                    }
                    $_SESSION['cart'][$productId] = [
                        'id' => $product['id'],
                        'title' => $product['title'],
                        'price' => $product['price'],
                        'image' => $product['image'],
                        'quantity' => $quantity
                    ];
                }
                
                return ['success' => true, 'message' => 'Product added to cart', 'cart_count' => $this->getCartCount()];
            }
            return ['success' => false, 'message' => 'Product not found'];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function updateCart($productId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeFromCart($productId);
        }
        
        if (isset($_SESSION['cart'][$productId])) {
            // Check stock
            $query = "SELECT stock FROM products WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $productId);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($quantity > $product['stock']) {
                return ['success' => false, 'message' => 'Not enough stock available'];
            }
            
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
            return ['success' => true, 'message' => 'Cart updated', 'cart_count' => $this->getCartCount()];
        }
        return ['success' => false, 'message' => 'Product not in cart'];
    }

    public function removeFromCart($productId) {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            return ['success' => true, 'message' => 'Product removed from cart', 'cart_count' => $this->getCartCount()];
        }
        return ['success' => false, 'message' => 'Product not in cart'];
    }

    public function clearCart() {
        $_SESSION['cart'] = [];
        return ['success' => true, 'message' => 'Cart cleared'];
    }

    public function getCart() {
        return $_SESSION['cart'] ?? [];
    }

    public function getCartCount() {
        $count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }

    public function getCartTotal() {
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function checkout($userId, $shippingDetails = [], $paymentMethod = 'cod') {
        if (empty($_SESSION['cart'])) {
            return ['success' => false, 'message' => 'Cart is empty'];
        }

        try {
            $this->conn->beginTransaction();
            
            // Calculate total (including tax)
            $subtotal = $this->getCartTotal();
            $tax = $subtotal * 0.10;
            $total = $subtotal + $tax;
            
            // Prepare shipping info
            $shippingInfo = !empty($shippingDetails) ? json_encode($shippingDetails) : null;
            
            // Create order
            $query = "INSERT INTO orders (user_id, total, status, shipping_info, payment_method) 
                      VALUES (:user_id, :total, 'Pending', :shipping_info, :payment_method)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':total', $total);
            $stmt->bindParam(':shipping_info', $shippingInfo);
            $stmt->bindParam(':payment_method', $paymentMethod);
            $stmt->execute();
            
            $orderId = $this->conn->lastInsertId();
            
            // Insert order items and update stock
            foreach ($_SESSION['cart'] as $productId => $item) {
                // Insert order item
                $query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                          VALUES (:order_id, :product_id, :quantity, :price)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':order_id', $orderId);
                $stmt->bindParam(':product_id', $productId);
                $stmt->bindParam(':quantity', $item['quantity']);
                $stmt->bindParam(':price', $item['price']);
                $stmt->execute();
                
                // Update product stock
                $query = "UPDATE products SET stock = stock - :quantity WHERE id = :product_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':quantity', $item['quantity']);
                $stmt->bindParam(':product_id', $productId);
                $stmt->execute();
            }
            
            // Clear cart
            $_SESSION['cart'] = [];
            
            $this->conn->commit();
            
            return $orderId;
        } catch (\PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Checkout error: " . $e->getMessage());
            return false;
        }
    }
}
