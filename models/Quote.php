<?php
class Quote
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Fetch all quotes
    public function getAllquotes()
    {
        try {
            $sql = "SELECT * FROM quotes ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => 'Failed to fetch quotes: ' . $e->getMessage()];
        }
    }
    public function getQuoteById($id)
    {
        try {
            $sql = "SELECT * FROM quotes WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => 'Failed to fetch quotes: ' . $e->getMessage()];
        }
    }

    public function deleteQuote($id)
    {
        try {
            $sql = "DELETE FROM quotes WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return true; // Successfully deleted
            } else {
                return false; // No row found with this ID
            }
        } catch (PDOException $e) {
            return ['error' => 'Failed to delete quote: ' . $e->getMessage()];
        }
    }
    public function createQuote($data)
    {
        try {
            $sql = "INSERT INTO quotes (fullnames, email, location,budget_range, notes, created_at) VALUES (:fullnames, :email, :location, :budget_range, :notes, NOW())";
            $stmt = $this->conn->prepare($sql);

            if (!isset($data['fullnames']) || !isset($data['email']) || !isset($data['budget_range'])) {
                return ['error' => 'Fullnames, email and budget_range are required'];
            }

            $stmt->bindParam(':fullnames', $data['fullnames']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':location', $data['location']);
            $stmt->bindParam(':budget_range', $data['budget_range']);
            $stmt->bindParam(':notes', $data['notes']);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId(); // Return the ID of the new quote
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return ['error' => 'Failed to create quote: ' . $e->getMessage()];
        }
    }

}

