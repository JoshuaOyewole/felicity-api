<?php
namespace Models;

require_once __DIR__ . '/../config/database.php'; // Include the Database class

use PDO;
use Database;

class Admin
{
    public static function findByEmail($email)
    {
        // Initialize database and get connection
        $db = new Database();
        $pdo = $db->connect();

        // Prepare and execute the query
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
