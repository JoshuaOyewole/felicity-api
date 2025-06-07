<?php
class Database
{
    private $host;
    private $db_name;
    private $username;
    private $password;

    public $conn;

    public function __construct()
    {
        $this->host = $_ENV['DB_HOST'];
        $this->db_name = $_ENV['DB_NAME'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASS'];
    }


    public function connect()
    {
        $this->conn = null;

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
