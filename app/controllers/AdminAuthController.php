<?php
namespace App\Controllers;

use App\configs\Database;
use PDO;

class AdminAuthController {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    public function login($username, $password) {
        try {
            $query = "SELECT * FROM admins WHERE username = :username LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verify password
                if (password_verify($password, $admin['password_hash'])) {
                    // Set session variables
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    return ['success' => true, 'message' => 'Login successful'];
                } else {
                    return ['success' => false, 'message' => 'Invalid password'];
                }
            } else {
                return ['success' => false, 'message' => 'Admin not found'];
            }
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Login error: ' . $e->getMessage()];
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
    }

    public static function checkAuth() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: ?page=admin/login');
            exit();
        }
    }

    public function hasAdmins() {
        try {
            $query = "SELECT COUNT(*) as count FROM admins";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function registerFirstAdmin($username, $password) {
        try {
            // Check if any admin already exists
            if ($this->hasAdmins()) {
                return ['success' => false, 'message' => 'Admin already exists. Please use login.'];
            }

            // Validate input
            if (empty($username) || empty($password)) {
                return ['success' => false, 'message' => 'Username and password are required'];
            }

            if (strlen($password) < 6) {
                return ['success' => false, 'message' => 'Password must be at least 6 characters'];
            }

            // Hash password and create admin
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO admins (username, password_hash) VALUES (:username, :password_hash)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password_hash', $password_hash);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Admin account created successfully'];
            }
            return ['success' => false, 'message' => 'Failed to create admin account'];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
