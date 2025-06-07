<?php
class Queries
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Fetch all contact-us
    public function getAllQueries()
    {
        try {
            $sql = "SELECT * FROM queries ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => 'Failed to fetch queries: ' . $e->getMessage()];
        }
    }
    public function getQueryById($id)
    {
        try {
            $sql = "SELECT * FROM queries WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => 'Failed to fetch query: ' . $e->getMessage()];
        }
    }

    public function deleteQuery($id)
    {
        try {
            $sql = "DELETE FROM queries WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return true; // Successfully deleted
            } else {
                return false; // No row found with this ID
            }
        } catch (PDOException $e) {
            return ['error' => 'Failed to delete query: ' . $e->getMessage()];
        }
    }
    public function createQuery($data)
    {
        try {
            $sql = "INSERT INTO queries (firstname, lastname, email, phone, message, created_at) VALUES (:firstname, :lastname, :email, :phone, :message, NOW())";
            $stmt = $this->conn->prepare($sql);

            if (!isset($data['firstname']) || !isset($data['email']) || !isset($data['message'])) {
                return ['error' => 'Firstname, email and message are required'];
            }

            $stmt->bindParam(':firstname', $data['firstname']);
            $stmt->bindParam(':lastname', $data['lastname']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':phone', $data['phone']);
            $stmt->bindParam(':message', $data['message']);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId(); // Return the ID of the new query
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return ['error' => 'Failed to create query: ' . $e->getMessage()];
        }
    }

}

