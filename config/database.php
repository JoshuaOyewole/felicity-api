<?php
class Database {
    private $host = 'sql205.hstn.me';
    private $db_name = 'mseet_39093726_felicity';
    private $username = 'mseet_39093726';
    private $password = 'Oyewole2025';
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
           // echo json_encode(['error' => 'Connection failed']);
               echo json_encode([
                'error' => 'Connection failed',
                'message' => $e->getMessage()
            ]);
            http_response_code(500);
            error_log("Database connection error: " . $e->getMessage());    
            exit;
        }
    }
}
