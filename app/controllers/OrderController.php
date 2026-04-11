<?php
namespace App\Controllers;

use App\configs\Database;
use PDO;

class OrderController {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    public function getAllOrders() {
        try {
            $query = "SELECT o.*, u.name as customer_name, u.email as customer_email 
                      FROM orders o 
                      LEFT JOIN users u ON o.user_id = u.id 
                      ORDER BY o.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getOrderById($id) {
        try {
            $query = "SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone, u.address 
                      FROM orders o 
                      LEFT JOIN users u ON o.user_id = u.id 
                      WHERE o.id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function getOrderItems($orderId) {
        try {
            $query = "SELECT oi.*, p.title, p.image 
                      FROM order_items oi 
                      LEFT JOIN products p ON oi.product_id = p.id 
                      WHERE oi.order_id = :order_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':order_id', $orderId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function updateOrderStatus($id, $status) {
        try {
            $query = "UPDATE orders SET status = :status WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':status', $status);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Order status updated successfully'];
            }
            return ['success' => false, 'message' => 'Failed to update order status'];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
