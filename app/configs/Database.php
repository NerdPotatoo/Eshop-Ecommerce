<?php
namespace App\configs;

use PDO;
use PDOException;

class Database {
    private $host = '127.0.0.1';
    private $db_name = 'eshop_db';
    private $username = 'root';
    private $password = '';
    private $port = '3306';
    public $conn;

    public function connect() {
        $this->conn = null;

        try {
            // First connect without database to check/create it
            $tempConn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port, $this->username, $this->password);
            $tempConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Ensure database exists
            $tempConn->exec("CREATE DATABASE IF NOT EXISTS " . $this->db_name);
            $tempConn = null; // Close temp connection

            // Now connect to the database
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Check if admins table exists (corrected query)
            $result = $this->conn->query("SHOW TABLES LIKE 'admins'");
            if ($result->rowCount() == 0) {
                // Run the SQL file to create tables
                $sql = file_get_contents(__DIR__ . '/eshop_db.sql');
                
                // Split SQL into individual statements
                $statements = array_filter(array_map('trim', explode(';', $sql)));
                
                foreach ($statements as $statement) {
                    if (!empty($statement)) {
                        $this->conn->exec($statement);
                    }
                }
            }

        } catch(PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
            die();
        }

        return $this->conn;
    }
}










