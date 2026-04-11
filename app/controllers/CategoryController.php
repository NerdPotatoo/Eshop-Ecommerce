<?php

namespace App\Controllers;

use App\configs\Database;
use PDO;

class CategoryController {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    /**
     * Get all categories
     */
    public function getAllCategories() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM categories ORDER BY name ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error fetching categories: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get category by ID
     */
    public function getCategoryById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error fetching category: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create new category
     */
    public function createCategory($data) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $stmt->execute([
                $data['name'],
                $data['description'] ?? ''
            ]);

            return [
                'success' => true,
                'message' => 'Category created successfully',
                'id' => $this->conn->lastInsertId()
            ];
        } catch (\PDOException $e) {
            error_log("Error creating category: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to create category: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update category
     */
    public function updateCategory($id, $data) {
        try {
            $stmt = $this->conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
            $stmt->execute([
                $data['name'],
                $data['description'] ?? '',
                $id
            ]);

            return [
                'success' => true,
                'message' => 'Category updated successfully'
            ];
        } catch (\PDOException $e) {
            error_log("Error updating category: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update category: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete category
     */
    public function deleteCategory($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);

            return [
                'success' => true,
                'message' => 'Category deleted successfully'
            ];
        } catch (\PDOException $e) {
            error_log("Error deleting category: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check if any categories exist
     */
    public function hasCategories() {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM categories");
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            error_log("Error checking categories: " . $e->getMessage());
            return false;
        }
    }
}
