<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'felicity';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function connect() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Connection failed']);
            http_response_code(500);
            error_log("Database connection error: " . $e->getMessage());    
            exit;
        }
    }
}
