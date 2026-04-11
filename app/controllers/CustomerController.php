<?php
namespace App\Controllers;

use App\configs\Database;
use PDO;

class CustomerController {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    public function getAllCustomers() {
        try {
            $query = "SELECT u.*, 
                      COUNT(DISTINCT o.id) as total_orders, 
                      COALESCE(SUM(o.total_amount), 0) as total_spent 
                      FROM users u 
                      LEFT JOIN orders o ON u.id = o.user_id 
                      GROUP BY u.id 
                      ORDER BY u.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getCustomerById($id) {
        try {
            $query = "SELECT * FROM users WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function getCustomerOrders($customerId) {
        try {
            $query = "SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $customerId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
}
